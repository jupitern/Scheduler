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
     * @param string $startTime time string compatible with \Datetime object. example: '08:00'
     * @param string $endTime time string compatible with \Datetime object. example: '17:00'
     */
    public function setTimeFrame( $startTime = null, $endTime = null )
    {
        if ($startTime !== null && !empty($startTime)) {
            $this->startTime = new \DateTime($startTime);
        }
        if ($endTime !== null && !empty($endTime)) {
            $this->endTime = new \DateTime($endTime);
        }

        return $this;
    }


    /**
     * add a one time occurring date
     *
     * @param string $dateTimeStr date string compatible with \Datetime object
     */
    public function add( $dateTimeStr )
    {
        $this->oneTimeEvents[] = $dateTimeStr;
        return $this;
    }

    /**
     * add a recurring date
     *
     * @param string $dateTimeStr date string compatible with \Datetime object
     */
    public function addRecurring( $dateTimeStr )
    {
        $this->schedules[] = $dateTimeStr;
        return $this;
    }


    /**
     * get next schedule date
     *
     * @param string $fromDateStr date string compatible with \Datetime object
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
     * @param string $fromDateStr date string compatible with \Datetime object
     * @param int $limit number of dates to return
     * @return array
     */
    public function getNextSchedules( $fromDateStr = 'now', $limit = 5 )
    {
        $dates = [];

        foreach ($this->oneTimeEvents as $schedule) {
			$dt = new \DateTime($fromDateStr);
			$dt->modify($schedule);
			
            if ($this->isInTimeFrame($dt, $fromDateStr)) {
                $dates[] = $dt;
            }
        }

        foreach ($this->schedules as $schedule) {
            $d = new \DateTime($fromDateStr);

            for ($i=0, $maxRecursion = 100 * $limit; $i < $limit && $maxRecursion > 0; ++$i, --$maxRecursion) {

                if ($this->isDateRelative($schedule)) {
                    if ($this->startTime instanceof \DateTime && $d < $this->startTime->modify($d->format('Y-m-d'))) {
                        $d->modify($this->startTime->format('H:i:s'));
                    }
                    elseif ($this->endTime instanceof \DateTime && $d > $this->endTime->modify($d->format('Y-m-d'))) {
                        $d->modify('next day')->modify($this->startTime->format('H:i:s'));
                    }

                    $dates[] = clone $d;
                    $d->modify($schedule);
                }
                elseif ($this->isInTimeFrame($d->modify($schedule), $fromDateStr)) {
                    $dates[] = clone $d;
                }
                else {
                    --$i;
                }
            }
        }

        $this->orderDates($dates);
        return array_slice($dates, 0, $limit);
    }


    /**
     * @param array $dates
	 * @return array
     */
    private function orderDates( &$dates )
    {
        uasort($dates, function($a, $b) {
            return strtotime($a->format('Y-m-d H:i:s')) > strtotime($b->format('Y-m-d H:i:s')) ? 1 : -1;
        });
    }

    /**
     * @param \DateTime $date
	 * @return bool
     */
    private function isInTimeFrame(\DateTime $date, $fromDateStr = 'now')
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
	private function isDateRelative($dateStr)
	{
		return strpos($dateStr, '+') !== false;
	}

}