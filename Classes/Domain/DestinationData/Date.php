<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\DestinationData;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use RuntimeException;

final class Date
{
    public function __construct(
        private readonly array $data
    ) {
    }

    public function isSingle(): bool
    {
        $frequency = $this->data['freq'] ?? '';
        $start = $this->data['start'] ?? '';

        return $frequency === ''
            && $start !== '';
    }

    public function isInterval(): bool
    {
        return $this->isDaily()
            || $this->isWeekly()
            || $this->isMonthly();
    }

    public function isDaily(): bool
    {
        return $this->getFrequency() == 'Daily'
            && empty($this->getWeekdays());
    }

    public function isWeekly(): bool
    {
        return $this->getFrequency() == 'Weekly'
            && !empty($this->getWeekdays());
    }

    public function isMonthly(): bool
    {
        return $this->getFrequency() == 'Monthly'
            && !empty($this->getWeekday())
            && $this->getDayOrdinal() > 0;
    }

    public function isAfter(DateTimeImmutable $comparison): bool
    {
        return new DateTimeImmutable($this->data['start']) > $comparison;
    }

    public function hasKnownRepeat(): bool
    {
        return empty($this->data['repeatUntil']) === false
            || empty($this->data['repeatCount']) === false;
    }

    public function withRepeatUntil(string $repeatUntil): self
    {
        $data = $this->data;
        $data['repeatUntil'] = $repeatUntil;

        return new self($data);
    }

    public function getStart(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            $this->data['start'],
            new DateTimeZone($this->data['tz'])
        );
    }

    public function getEnd(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            $this->data['end'],
            new DateTimeZone($this->data['tz'])
        );
    }

    public function getTimeZone(): DateTimeZone
    {
        return new DateTimeZone($this->data['tz']);
    }

    /**
     * @return string[]
     */
    public function getWeekdays(): array
    {
        return $this->data['weekdays'] ?? [];
    }

    public function getWeekday(): string
    {
        return $this->data['weekday'] ?? '';
    }

    public function getDayOrdinal(): int
    {
        return $this->data['dayOrdinal'] ?? 0;
    }

    public function getRepeatUntil(): DateTimeImmutable
    {
        if (array_key_exists('repeatUntil', $this->data)) {
            return $this->getRepeatUntilByRepeatUntil();
        }

        if (array_key_exists('repeatCount', $this->data)) {
            return $this->getRepeatUntilByCount();
        }

        throw new Exception('The event current date does not provide a supported repeat option.', 1770792700);
    }

    private function getRepeatUntilByRepeatUntil(): DateTimeImmutable
    {
        $date = new DateTimeImmutable($this->data['repeatUntil'], $this->getTimezone());
        $date = $date->setTime(
            (int)$this->getStart()->format('H'),
            (int)$this->getStart()->format('m'),
            (int)$this->getStart()->format('s'),
        );

        return $date;
    }

    private function getRepeatUntilByCount(): DateTimeImmutable
    {
        $repeatCount = $this->data['repeatCount'] ?? null;
        if (is_numeric($repeatCount) === false) {
            throw new RuntimeException('The given repeatCount was not numeric.', 1770792769);
        }

        $repeatCount = (int)$repeatCount;
        $repeatCountUnit = $this->getRepeatCountUnit();

        return $this->getStart()->modify('+' . $repeatCount . ' ' . $repeatCountUnit);
    }

    private function getRepeatCountUnit(): string
    {
        if ($this->isDaily()) {
            return 'days';
        }

        if ($this->isWeekly()) {
            return 'weeks';
        }

        if ($this->isMonthly()) {
            return 'months';
        }

        throw new RuntimeException('The current date does not provide a supported repeat count unit.', 1739279561);
    }

    private function getFrequency(): string
    {
        return $this->data['freq'] ?? '';
    }
}
