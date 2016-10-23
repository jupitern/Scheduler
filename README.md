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

// get next 5 schedules starting at 2020-01-01
->getNextSchedules('2020-01-01 00:00:00', 10);

// display schedules
foreach ($schedules as $schedule) {
    echo $schedule->format('Y-m-d H:i')."<br/>";
}


examples :

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

```

## Contributing

 - welcome to discuss a bugs, features and ideas.

## License

jupitern/scheduler is release under the MIT license.

You are free to use, modify and distribute this software
