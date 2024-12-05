<?php

namespace Jupitern\Scheduler;

class Scheduler {

    private array $schedules = [];
    private array $oneTimeEvents = [];
    private \DateTime|null $startTime = null;
    private \DateTime|null $endTime = null;

    /**
     * @return static
     */
    public static function instance()
    {
        return new static();
    }


    /**
     * set a time frame in which events will occur
     *
     * @param string|null $startTime time string compatible with \Datetime object. example: '08:00'
     * @param string|null $endTime time string compatible with \Datetime object. example: '17:00'
     * @return Scheduler
     * @throws \Exception
     */
    public function setTimeFrame(string $startTime = null, string $endTime = null): self
    {
        if (!empty($startTime)) {
            $this->startTime = new \DateTime($startTime);
        }
        if (!empty($endTime)) {
            $this->endTime = new \DateTime($endTime);
        }

        return $this;
    }


    /**
     * add a one time occurring date
     *
     * @param string $dateTimeStr date string compatible with \Datetime object
     */
    public function add(string $dateTimeStr): self
    {
        $this->oneTimeEvents[] = $dateTimeStr;
        return $this;
    }

    /**
     * add a recurring date
     *
     * @param string $dateTimeStr date string compatible with \Datetime object
     */
    public function addRecurring(string $dateTimeStr): self
    {
        $this->schedules[] = $dateTimeStr;
        return $this;
    }


    /**
     * get next schedule date
     *
     * @param string $fromDateStr date string compatible with \Datetime object
     * @return \Datetime|null or null
     */
    public function getNextSchedule(string $fromDateStr = 'now'): ?\Datetime
    {
        $dates = $this->getNextSchedules($fromDateStr, 1);
        return count($dates) ? $dates[0] : null;
    }


    /**
     * get a number of next schedule dates
     *
     * @param string $fromDateStr date string compatible with \Datetime object
     * @param int $limit number of dates to return
     * @return array
     */
    public function getNextSchedules(string $fromDateStr = 'now', int $limit = 5): array
    {
        $dates = [];

        foreach ($this->oneTimeEvents as $schedule) {
            if ($schedule == 'now') $schedule = '+1 second';
            $d = new \DateTime($fromDateStr);
            $d->modify($schedule);

            if ($this->isInTimeFrame($d, $fromDateStr)) {
                $dates[$d->format('YmdHis')] = clone $d;
            }
        }

        foreach ($this->schedules as $schedule) {
            $d = new \DateTime($fromDateStr);

            for ($i=0, $maxRecursion = 100 * $limit; $i < $limit && $maxRecursion > 0; ++$i, --$maxRecursion) {
                if ($this->isDateRelative($schedule)) {
                    $d->modify($schedule);

                    if ($this->startTime instanceof \DateTime && $d < $this->startTime->modify($d->format('Y-m-d'))) {
                        $d->modify($this->startTime->format('H:i:s'));
                    } elseif ($this->endTime instanceof \DateTime && $d > $this->endTime->modify($d->format('Y-m-d'))) {
                        $d->modify('next day')->modify($this->startTime->format('H:i:s'));
                    }

                    if (!array_key_exists($d->format('YmdHis'), $dates)) {
                        $dates[$d->format('YmdHis')] = clone $d;
                    }

                } elseif ($this->isInTimeFrame($d->modify($schedule), $fromDateStr)) {
                    if (!array_key_exists($d->format('YmdHis'), $dates))  {
                        $dates[$d->format('YmdHis')] = clone $d;
                    }
                } else {
                    --$i;
                }
            }
        }

        $this->orderDates($dates);

        return array_slice($dates, 0, $limit);
    }


    /**
     * @param array $dates
     * @return void
     */
    private function orderDates(array &$dates): void
    {
        uasort($dates, function($a, $b) {
            return strtotime($a->format('Y-m-d H:i:s')) - strtotime($b->format('Y-m-d H:i:s'));
        });
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    private function isInTimeFrame(\DateTime $date, string $fromDateStr = 'now'): bool
    {
        if ($date < new \DateTime($fromDateStr)) {
            return false;
        }

        if ($this->startTime && $date < $this->startTime->modify($date->format('Y-m-d'))) {
            return false;
        }
        if ($this->endTime && $date > $this->endTime->modify($date->format('Y-m-d'))) {
            return false;
        }

        return true;
    }

    /**
     * @param string $dateStr \Datetime object valid date string
     * @return bool
     */
    private function isDateRelative(string $dateStr): bool
    {
        return str_contains($dateStr, '+');
    }

}