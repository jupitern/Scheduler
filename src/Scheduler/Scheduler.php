<?php

namespace Jupitern\Table;

class Scheduler {

    private $schedules = array();
    private $oneTimeEvents = array();


    /**
     * @return static
     */
    public static function instance()
    {
        return new static();
    }


    /**
     * add a one time occurring date
     *
     * @param string $dateTimeStr \Datetime object valid date string
     */
    public function add( $dateTimeStr )
    {
        $this->oneTimeEvents[] = new \DateTime($dateTimeStr);
    }

    /**
     * add a recurring date
     *
     * @param string $dateTimeStr \Datetime object valid date string
     */
    public function addRecurring( $dateTimeStr )
    {
        $this->schedules[] = $dateTimeStr;
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
        $dates = $this->oneTimeEvents;
        if (!count($this->schedules)) return $dates;

        foreach ($this->schedules as $schedule) {
            $d = new \DateTime($fromDateStr);
            for ($i=0; $i < $limit; ++$i) {
                $dates[] = clone $d->modify($schedule);
            }
        }

        $this->orderDates($dates);
        return array_slice($dates, 0, $limit);
    }


    /**
     * @param $dates
     */
    private function orderDates( &$dates )
    {
        uasort($dates, function($a, $b) {
            return strtotime($a->format('Y-m-d H:i:s')) > strtotime($b->format('Y-m-d H:i:s')) ? 1 : -1;
        });
    }

}