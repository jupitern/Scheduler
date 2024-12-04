[![Build Status](https://scrutinizer-ci.com/g/jupitern/Scheduler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jupitern/Scheduler/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jupitern/Scheduler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jupitern/Scheduler/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jupitern/scheduler/v/stable)](https://packagist.org/packages/jupitern/scheduler)
[![Latest Unstable Version](https://poser.pugx.org/jupitern/scheduler/v/unstable)](https://packagist.org/packages/jupitern/scheduler)
[![License](https://poser.pugx.org/jupitern/scheduler/license)](https://packagist.org/packages/jupitern/scheduler)

# jupitern/scheduler
#### PHP Scheduler.

add one time event dates or recurring event dates
get next event date or next X event dates from a given date

## Requirements

PHP 5.4 or higher.

## Installation

Include jupitern/scheduler in your project, by adding it to your composer.json file.
```javascript
{
    "require": {
        "jupitern/scheduler": "1.*"
    }
}
```

## Usage
```php
// instance Scheduler
$schedules = \Jupitern\Scheduler\Scheduler::instance()

// limit events from 08.00 am to 17.00
->setTimeFrame('08:00', '17:00')

// add a one time event date
// accepts any string compatible with php DateTime object
->add('2020-01-01 12:35')

// add another one time event date
// accepts any string compatible with php DateTime object
->add('2020-01-01 17:50')

// add a recurring date
// accepts any string compatible with php DateTime object
->addRecurring('+ 8 hours')

// get next schedule starting at 2020-01-01
->getNextSchedule('2020-01-01 00:00:00', 10);

// get next 5 schedules starting at 2020-01-01
->getNextSchedules('2020-01-01 00:00:00', 10);

// display schedules
foreach ($schedules as $schedule) {
    echo $schedule->format('Y-m-d H:i')."<br/>";
}

```

## Examples
```php

$schedules = \Jupitern\Scheduler\Scheduler::instance()
    ->add('2030-01-01 12:35')
    ->add('2030-01-01 14:50')
    ->addRecurring('+2 hours')
    ->getNextSchedules('2030-01-01 00:00:00', 10);

foreach ($schedules as $schedule) {
    echo $schedule->format('Y-m-d H:i').'<br/>';
}

/*
output:
2030-01-01 00:00
2030-01-01 02:00
2030-01-01 04:00
2030-01-01 06:00
2030-01-01 08:00
2030-01-01 10:00
2030-01-01 12:00
2030-01-01 12:35
2030-01-01 14:00
2030-01-01 14:50
*/

$schedules = \Jupitern\Scheduler\Scheduler::instance()
    ->setTimeFrame('08:00', '17:00')
    ->add('2030-01-01 12:35')
    ->add('2030-01-01 14:50')
    ->addRecurring('+2 hours')
    ->getNextSchedules('2030-01-01 00:00:00', 10);

foreach ($schedules as $schedule) {
    echo $schedule->format('Y-m-d H:i').'<br/>';
}

/*
output:
2030-01-01 08:00
2030-01-01 10:00
2030-01-01 12:00
2030-01-01 12:35
2030-01-01 14:00
2030-01-01 14:50
2030-01-01 16:00
2030-01-02 08:00
2030-01-02 10:00
2030-01-02 12:00
*/


$schedules = \Jupitern\Scheduler\Scheduler::instance()
    ->setTimeFrame('08:30', '19:00')
    ->add('+10 minutes')
    ->add('+30 minutes')	// outside time frame. will not produce any schedule
    ->add('next day 08:30')
    ->getNextSchedules('2000-12-16 18:40');

foreach ($schedules as $schedule) {
    echo $schedule->format('Y-m-d H:i')."<br/>";
}

/*
output:
2000-12-16 18:50
2000-12-17 08:30
*/

```

## ChangeLog

v1.4

- min php version updated to 8.0
- code refactor for php 8
- bug fix to avoid duplicate dates when calculated dates from different schedules match 


v1.3

 - Changed method add to allow relative one time events like "+1 hour" or "next day 17:00"
 - relative events added with method add() are relative to $fromStartDate and obey time frame if set

## Contributing

 - welcome to discuss a bugs, features and ideas.

## License

jupitern/scheduler is release under the MIT license.

You are free to use, modify and distribute this software
