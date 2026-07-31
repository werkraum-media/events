<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use DateInterval;
use DateMalformedStringException;
use DatePeriod;
use DateTimeImmutable;
use Generator;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Log\LogManager;
use WerkraumMedia\Events\Domain\DestinationData\Date as DestinationDataDate;
use WerkraumMedia\Events\Domain\Model\Date;
use WerkraumMedia\Events\Domain\Model\Import;

final class DatesFactory
{
    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly Context $context,
        LogManager $logManager
    ) {
        $this->logger = $logManager->getLogger(self::class);
    }

    /**
     * @param array[] $timeIntervals
     *
     * @return Generator<Date>
     */
    public function createDates(
        Import $import,
        array $timeIntervals,
        bool $canceled
    ): Generator {
        foreach ($timeIntervals as $date) {
            $dates = $this->createDate($import, $date, $canceled);
            if (!$dates instanceof Generator) {
                return null;
            }

            foreach ($dates as $createdDate) {
                yield $createdDate;
            }
        }
    }

    /**
     * @return Generator<Date>|null
     */
    private function createDate(
        Import $import,
        array $date,
        bool $canceled
    ): ?Generator {
        $date = new DestinationDataDate($date);

        if ($date->isSingle()) {
            $this->logger->info('Is single date', ['date' => $date]);
            return $this->createSingleDate($date, $canceled);
        }

        if ($date->isInterval()) {
            $this->logger->info('Is interval date', ['date' => $date]);
            return $this->createDateFromInterval($import, $date, $canceled);
        }

        return null;
    }

    /**
     * @return Generator<Date>
     */
    private function createSingleDate(
        DestinationDataDate $date,
        bool $canceled
    ): Generator {
        if ($date->isAfter($this->getToday())) {
            yield Date::createFromDestinationDataDate($date, $canceled);
        }
    }

    /**
     * @return Generator<Date>|null
     */
    private function createDateFromInterval(
        Import $import,
        DestinationDataDate $date,
        bool $canceled
    ): ?Generator {
        $date = $this->ensureRepeatUntil($import, $date);

        if ($date->isDaily()) {
            return $this->createDailyDates($date, $canceled);
        }

        if ($date->isWeekly()) {
            return $this->createWeeklyDates($date, $canceled);
        }

        if ($date->isMonthly()) {
            return $this->createMonthlyDates($date, $canceled);
        }

        return null;
    }

    private function ensureRepeatUntil(
        Import $import,
        DestinationDataDate $date
    ): DestinationDataDate {
        if ($date->hasKnownRepeat()) {
            return $date;
        }

        $repeatUntil = $this->getToday()->modify($import->getRepeatUntil())->format('c');
        $this->logger->info('Interval did not provide repeatUntil.', ['newRepeat' => $repeatUntil]);

        return $date->withRepeatUntil($repeatUntil);
    }

    /**
     * @return Generator<Date>
     */
    private function createDailyDates(
        DestinationDataDate $date,
        bool $canceled
    ): Generator {
        $today = $this->getToday();
        $timeZone = $date->getTimeZone();
        $start = $date->getStart();
        $end = $date->getEnd();

        $period = new DatePeriod($start, new DateInterval('P1D'), $date->getRepeatUntil());
        foreach ($period as $day) {
            $day = $day->setTimezone($timeZone);
            if ($day < $today) {
                $this->logger->debug('Date was in the past.', ['day' => $day]);
                continue;
            }

            yield $this->createDateFromStartAndEnd(
                $day,
                $start,
                $end,
                $canceled
            );
        }
    }

    /**
     * @return Generator<Date>
     */
    private function createWeeklyDates(
        DestinationDataDate $date,
        bool $canceled
    ): Generator {
        $today = $this->getToday();
        $timeZone = $date->getTimeZone();
        $start = $date->getStart();
        $end = $date->getEnd();
        $until = $date->getRepeatUntil();

        foreach ($date->getWeekdays() as $day) {
            $dateToUse = $this->modify($start, $day);
            if ($dateToUse === null) {
                continue;
            }
            $dateToUse = $dateToUse->setTime((int)$start->format('H'), (int)$start->format('i'));

            $period = new DatePeriod($dateToUse, new DateInterval('P1W'), $until);
            foreach ($period as $day) {
                $day = $day->setTimezone($timeZone);
                if ($day < $today) {
                    $this->logger->debug('Date was in the past.', ['day' => $day]);
                    continue;
                }

                yield $this->createDateFromStartAndEnd(
                    $day,
                    $start,
                    $end,
                    $canceled
                );
            }
        }
    }

    /**
     * @return Generator<Date>
     */
    private function createMonthlyDates(
        DestinationDataDate $date,
        bool $canceled
    ): Generator {
        $today = $this->getToday();
        $timeZone = $date->getTimeZone();
        $start = $date->getStart();
        $end = $date->getEnd();
        $until = $date->getRepeatUntil();

        $dateToUse = $this->modify($start, $date->getWeekday());
        if ($dateToUse === null) {
            return;
        }
        $dateToUse = $dateToUse->setTime((int)$start->format('H'), (int)$start->format('i'));

        $period = new DatePeriod($dateToUse, new DateInterval('P1M'), $until);
        foreach ($period as $day) {
            $day = $day->setTimezone($timeZone);
            $day = $this->modify($day, 'first day of');
            if ($day === null) {
                continue;
            }
            $day = $this->modify($day, $date->getDayOrdinal() . ' ' . $date->getWeekday());
            if ($day === null) {
                continue;
            }
            if ($day < $today) {
                $this->logger->debug('Date was in the past.', ['day' => $day]);
                continue;
            }

            yield $this->createDateFromStartAndEnd(
                $day,
                $start,
                $end,
                $canceled
            );
        }
    }

    private function createDateFromStartAndEnd(
        DateTimeImmutable $dateToUse,
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        bool $canceled
    ): Date {
        return Date::createFromDestinationData(
            $dateToUse->setTime((int)$start->format('H'), (int)$start->format('i')),
            $dateToUse->setTime((int)$end->format('H'), (int)$end->format('i')),
            $canceled
        );
    }

    /**
     * modify() reports an unparseable expression differently per supported PHP
     * version — a warning plus false up to 8.2, an exception from 8.3 — so the
     * expression is parsed first: date_parse() reports the failure quietly and
     * alike. Returns null for anything that names no date.
     */
    private function modify(DateTimeImmutable $date, string $modification): ?DateTimeImmutable
    {
        $parsed = date_parse($modification);
        if ($parsed['error_count'] > 0) {
            $this->logger->warning('Modification could not be resolved to a date.', [
                'modification' => $modification,
                'errors' => $parsed['errors'],
            ]);
            return null;
        }

        try {
            $modified = $date->modify($modification);
        } catch (DateMalformedStringException) {
            $modified = false;
        }

        if ($modified === false) {
            $this->logger->warning('Modification could not be applied to a date.', ['modification' => $modification]);
            return null;
        }

        return $modified;
    }

    private function getToday(): DateTimeImmutable
    {
        $today = $this->context->getPropertyFromAspect('date', 'full', new DateTimeImmutable());
        if (!$today instanceof DateTimeImmutable) {
            $today = new DateTimeImmutable();
        }

        return $today->modify('midnight');
    }
}
