<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Log\LogManager;
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
        if ($this->isDateSingleDate($date)) {
            $this->logger->info('Is single date', ['date' => $date]);
            return $this->createSingleDate($date, $canceled);
        }

        if ($this->isDateInterval($date)) {
            $this->logger->info('Is interval date', ['date' => $date]);
            return $this->createDateFromInterval($import, $date, $canceled);
        }

        return null;
    }

    private function isDateSingleDate(array $date): bool
    {
        $frequency = $date['freq'] ?? '';
        $start = $date['start'] ?? '';

        return $frequency === ''
            && $start !== '';
    }

    private function isDateInterval(array $date): bool
    {
        $frequency = $date['freq'] ?? '';

        if ($frequency == 'Daily' && empty($date['weekdays'])) {
            return true;
        }

        if ($frequency == 'Weekly' && !empty($date['weekdays'])) {
            return true;
        }

        return false;
    }

    /**
     * @return Generator<Date>
     */
    private function createSingleDate(
        array $date,
        bool $canceled
    ): Generator {
        if (new DateTimeImmutable($date['start']) > $this->getToday()) {
            yield Date::createFromDestinationDataDate($date, $canceled);
        }
    }

    /**
     * @return Generator<Date>|null
     */
    private function createDateFromInterval(
        Import $import,
        array $date,
        bool $canceled
    ): ?Generator {
        $date = $this->ensureRepeatUntil($import, $date);

        if ($date['freq'] == 'Daily') {
            return $this->createDailyDates($date, $canceled);
        }

        if ($date['freq'] == 'Weekly') {
            return $this->createWeeklyDates($date, $canceled);
        }

        return null;
    }

    private function ensureRepeatUntil(
        Import $import,
        array $date
    ): array {
        if (empty($date['repeatUntil']) === false) {
            return $date;
        }
        if (empty($date['repeatCount']) === false) {
            return $date;
        }

        $date['repeatUntil'] = $this->getToday()->modify($import->getRepeatUntil())->format('c');
        $this->logger->info('Interval did not provide repeatUntil.', ['newRepeat' => $date['repeatUntil']]);

        return $date;
    }

    /**
     * @return Generator<Date>
     */
    private function createDailyDates(
        array $date,
        bool $canceled
    ): Generator {
        $today = $this->getToday();
        $timeZone = new DateTimeZone($date['tz']);
        $start = new DateTimeImmutable($date['start'], $timeZone);
        $end = new DateTimeImmutable($date['end'], $timeZone);
        $until = $this->createUntil($start, $date, 'days');

        $period = new DatePeriod($start, new DateInterval('P1D'), $until);
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
        array $date,
        bool $canceled
    ): Generator {
        $today = $this->getToday();
        $timeZone = new DateTimeZone($date['tz']);
        $start = new DateTimeImmutable($date['start'], $timeZone);
        $end = new DateTimeImmutable($date['end'], $timeZone);
        $until = $this->createUntil($start, $date, 'weeks');

        foreach ($date['weekdays'] as $day) {
            $dateToUse = $start->modify($day);
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
     * @param string $repeatCountUnit E.g. weeks or days
     */
    private function createUntil(
        DateTimeImmutable $start,
        array $date,
        string $repeatCountUnit,
    ): DateTimeImmutable {
        if (array_key_exists('repeatUntil', $date)) {
            return new DateTimeImmutable($date['repeatUntil'], $start->getTimezone());
        }

        return $start->modify('+' . ((int)$date['repeatCount']) . ' ' . $repeatCountUnit);
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
