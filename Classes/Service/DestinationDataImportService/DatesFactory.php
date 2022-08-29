<?php

namespace Wrm\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Core\Context\Context;
use Wrm\Events\Domain\Model\Date;

class DatesFactory
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return \Generator<Date>
     */
    public function createDates(
        array $timeIntervals,
        bool $canceled
    ): \Generator {
        foreach ($timeIntervals as $date) {
            $dates = $this->createDate($date, $canceled);
            if (!$dates instanceof \Generator) {
                return null;
            }

            foreach ($dates as $createdDate) {
                yield $createdDate;
            }
        }
    }

    /**
     * @return \Generator<Date>|null
     */
    private function createDate(
        array $date,
        bool $canceled
    ): ?\Generator {
        if ($this->isDateSingleDate($date)) {
            return $this->createSingleDate($date, $canceled);
        }

        if ($this->isDateInterval($date)) {
            return $this->createDateFromInterval($date, $canceled);
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
        if (empty($date['repeatUntil'])) {
            return false;
        }

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
     * @return \Generator<Date>
     */
    private function createSingleDate(
        array $date,
        bool $canceled
    ): \Generator {
        if (new \DateTimeImmutable($date['start']) > $this->getToday()) {
            yield Date::createFromDestinationDataDate($date, $canceled);
        }
    }

    /**
     * @return \Generator<Date>|null
     */
    private function createDateFromInterval(
        array $date,
        bool $canceled
    ): ?\Generator {
        if ($date['freq'] == 'Daily') {
            return $this->createDailyDates($date, $canceled);
        }

        if ($date['freq'] == 'Weekly') {
            return $this->createWeeklyDates($date, $canceled);
        }

        return null;
    }

    /**
     * @return \Generator<Date>
     */
    private function createDailyDates(
        array $date,
        bool $canceled
    ): \Generator {
        $today = $this->getToday();
        $timeZone = new \DateTimeZone($date['tz']);
        $start = new \DateTimeImmutable($date['start'], $timeZone);
        $end = new \DateTimeImmutable($date['end'], $timeZone);
        $until = new \DateTimeImmutable($date['repeatUntil'], $timeZone);

        $period = new \DatePeriod($start, new \DateInterval('P1D'), $until);
        foreach ($period as $day) {
            $day = $day->setTimezone($timeZone);
            if ($day < $today) {
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
     * @return \Generator<Date>
     */
    private function createWeeklyDates(
        array $date,
        bool $canceled
    ): \Generator {
        $today = $this->getToday();
        $timeZone = new \DateTimeZone($date['tz']);
        $start = new \DateTimeImmutable($date['start'], $timeZone);
        $end = new \DateTimeImmutable($date['end'], $timeZone);
        $until = new \DateTimeImmutable($date['repeatUntil'], $timeZone);

        foreach ($date['weekdays'] as $day) {
            $dateToUse = $start->modify($day);
            $dateToUse = $dateToUse->setTime((int) $start->format('H'), (int) $start->format('i'));

            $period = new \DatePeriod($dateToUse, new \DateInterval('P1W'), $until);
            foreach ($period as $day) {
                $day = $day->setTimezone($timeZone);
                if ($day < $today) {
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
        \DateTimeImmutable $dateToUse,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        bool $canceled
    ): Date {
        return Date::createFromDestinationData(
            $dateToUse->setTime((int) $start->format('H'), (int) $start->format('i')),
            $dateToUse->setTime((int) $end->format('H'), (int) $end->format('i')),
            $canceled
        );
    }

    private function getToday(): \DateTimeImmutable
    {
        $today = $this->context->getPropertyFromAspect('date', 'full', new \DateTimeImmutable());
        if (!$today instanceof \DateTimeImmutable) {
            $today = new \DateTimeImmutable();
        }

        return $today->modify('midnight');
    }
}
