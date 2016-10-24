<?php

namespace Jupitern\Scheduler;

/*
 * Simple Scheduler class
 *
 * Author: Nuno Chaves
 * */

class Scheduler {

    private $schedules = [];
    private $oneTimeEvents = [];
    private $startTime = null;
    private $endTime = null;

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
     * @param string $startTime \Datetime start time string compatible with php Datetime class. example: '08:00'
     * @param string $endTime \Datetime end time string compatible with php Datetime class. example: '17:00'
     */
    public function setTimeFrame( $startTime = null, $endTime = null )
    {
        if ($startTime != null && !empty($startTime)) {
            $this->startTime = new \DateTime($startTime);
        }
        if ($endTime != null && !empty($endTime)) {
            $this->endTime = new \DateTime($endTime);
        }

        return $this;
    }


    /**
     * add a one time occurring date
     *
     * @param string $dateTimeStr \Datetime object valid date string
     */
    public function add( $dateTimeStr )
    {
        $this->oneTimeEvents[] = new \DateTime($dateTimeStr);
        return $this;
    }

    /**
     * add a recurring date
     *
     * @param string $dateTimeStr \Datetime object valid date string
     */
    public function addRecurring( $dateTimeStr )
    {
        $this->schedules[] = $dateTimeStr;
        return $this;
    }


    /**
     * get next schedule date
     *
     * @param string $fromDateStr \Datetime object valid date string
     * @return \Datetime or null
     */
    public function getNextSchedule( $fromDateStr = 'now' )
    {
        $dates = $this->getNextSchedules($fromDateStr, 1);
        return count($dates) ? $dates[0] : null;
    }


    /**
     * get a number of next schedule dates
     *
     * @param string $fromDateStr \Datetime object valid date string
     * @param int $limit number of dates to return
     * @return array
     */
    public function getNextSchedules( $fromDateStr = 'now', $limit = 5 )
    {
        $dates = [];

        foreach ($this->oneTimeEvents as $evt) {
            if ($this->isInTimeFrame($evt, $fromDateStr)) {
                $dates[] = $evt;
            }
        }

        foreach ($this->schedules as $schedule) {
            $d = new \DateTime($fromDateStr);

            for ($i=0, $maxRecursion = 10 * $limit; $i < $limit && $maxRecursion > 0; ++$i, --$maxRecursion) {

                if (strpos($schedule, '+') === false) {
                    $d->modify($schedule);
                    if ($this->isInTimeFrame($d, $fromDateStr)) {
                        $dates[] = clone $d;
                    }
                }
                else {
                    if ($this->startTime instanceof \DateTime && $d < $this->startTime->modify($d->format('Y-m-d'))) {
                        $d->modify($this->startTime->format('H:i:s'));
                    }

                    if ($this->endTime instanceof \DateTime && $d > $this->endTime->modify($d->format('Y-m-d'))) {
                        $d->modify("next day 00:00:00");
                    }

                    if ($this->isInTimeFrame($d, $fromDateStr)) {
                        $dates[] = clone $d;
                    }
                    $d->modify($schedule);
                }
            }
        }

        $this->orderDates($dates);
        return array_slice($dates, 0, $limit);
    }


    /**
     * @param array $dates
     */
    private function orderDates( &$dates )
    {
        uasort($dates, function($a, $b) {
            return strtotime($a->format('Y-m-d H:i:s')) > strtotime($b->format('Y-m-d H:i:s')) ? 1 : -1;
        });
    }

    /**
     * @param \DateTime $date
     */
    private function isInTimeFrame(\DateTime $date, $fromDateStr = 'now')
    {
        if ($date < new \DateTime($fromDateStr)) {
            return false;
        }

        $dtStart = $this->startTime instanceof \DateTime ? $this->startTime->modify($date->format('Y-m-d')) : null;
        $dtEnd = $this->endTime instanceof \DateTime ? $this->endTime->modify($date->format('Y-m-d')) : null;

        if ($this->startTime && $date < $this->startTime->modify($date->format('Y-m-d'))) {
            return false;
        }
        if ($this->endTime && $date > $this->endTime->modify($date->format('Y-m-d'))) {
            return false;
        }

        return true;
    }

}
