<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\DestinationData;

use DateTimeImmutable;
use DateTimeZone;
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
            return new DateTimeImmutable($this->data['repeatUntil'], $this->getTimezone());
        }

        $repeatCountUnit = '';
        if ($this->isDaily()) {
            $repeatCountUnit = 'days';
        } elseif ($this->isWeekly()) {
            $repeatCountUnit = 'weeks';
        }

        if ($repeatCountUnit === '') {
            throw new RuntimeException('The current date can not repeat.', 1739279561);
        }

        return $this->getStart()->modify(
            '+' . ((int)$this->data['repeatCount']) . ' ' . $repeatCountUnit
        );
    }

    private function getFrequency(): string
    {
        return $this->data['freq'] ?? '';
    }
}
