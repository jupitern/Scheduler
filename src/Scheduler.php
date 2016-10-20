<?php

namespace Jupitern\Scheduler;

/*
 * Simple Scheduler class
 *
 * Author: Nuno Chaves
 * */

class Scheduler {

    private $schedules = array();
    private $oneTimeEvents = array();
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
     * @param string $startTime \Datetime start time string compatible with php Datetime class
     * @param string $endTime \Datetime end time string compatible with php Datetime class
     */
    public function setTimeFrame( $startTime = null, $endTime = null )
    {
        if ($startTime != null ){
            $this->startTime = new \DateTime($startTime);
        }
        if ($endTime != null) {
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
        $dates = array();
        foreach ($this->oneTimeEvents as $evt) {
            if ($this->isInTimeFrame($evt)) {
                $dates[] = $evt;
            }
        }

        foreach ($this->schedules as $schedule) {
            $d = new \DateTime($fromDateStr);
            for ($i=0; $i < $limit; ++$i) {
                $newDate = clone $d;
                if ($newDate->modify($schedule) > $d && $this->isInTimeFrame($newDate)) {
                    $dates[] = $newDate;
                }
                $d->modify($schedule);
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
    private function isInTimeFrame(\DateTime $date)
    {
        $dtStart = $this->startTime->modify($date->format('Y-m-d'));
        $dtEnd = $this->endTime->modify($date->format('Y-m-d'));

        if ($this->startTime instanceof \DateTime && $date < $this->startTime->modify($date->format('Y-m-d'))) {
            return false;
        }
        if ($this->endTime instanceof \DateTime && $date > $this->endTime->modify($date->format('Y-m-d'))) {
            return false;
        }

        return true;
    }

}
