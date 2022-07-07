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
        if (strtotime($date['start']) > $this->getToday()) {
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
        $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
        $until = new \DateTime($date['repeatUntil'], new \DateTimeZone($date['tz']));

        $i = (int) strtotime($start->format('l'), $start->getTimestamp());
        while ($i !== 0 && $i <= $until->getTimestamp()) {
            $i = (int) strtotime('+1 day', $i);
            if ($i < $today) {
                continue;
            }

            yield $this->createDateFromStartAndUntil(
                (string) $i,
                $start,
                $until,
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
        $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
        $until = new \DateTime($date['repeatUntil'], new \DateTimeZone($date['tz']));

        foreach ($date['weekdays'] as $day) {
            $i = strtotime($day, $start->getTimestamp());
            while ($i !== 0 && $i <= $until->getTimestamp()) {
                $timeStampToUse = (string) $i;
                $i = strtotime('+1 week', $i);
                if ($i < $today) {
                    continue;
                }

                yield $this->createDateFromStartAndUntil(
                    $timeStampToUse,
                    $start,
                    $until,
                    $canceled
                );
            }
        }
    }

    private function createDateFromStartAndUntil(
        string $timestamp,
        \DateTime $start,
        \DateTime $until,
        bool $canceled
    ): Date {
        $eventStart = new \DateTime('@' . $timestamp);
        $eventStart->setTime((int) $start->format('H'), (int) $start->format('i'));
        $eventEnd = new \DateTime('@' . $timestamp);
        $eventEnd->setTime((int) $until->format('H'), (int) $until->format('i'));

        return Date::createFromDestinationData(
            $eventStart,
            $eventEnd,
            $canceled
        );
    }

    private function getToday(): int
    {
        $today = $this->context->getPropertyFromAspect('date', 'full', new \DateTimeImmutable());
        if (!$today instanceof \DateTimeImmutable) {
            $today = new \DateTimeImmutable();
        }

        $midnight = $today->modify('midnight');
        return (int) $midnight->format('U');
    }
}
