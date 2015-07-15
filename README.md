# jupitern/scheduler
#### PHP Scheduler.

add one time event dates or recurring event dates
get next event date or next X event dates from a given date

## Requirements

PHP 5.4 or higher.

## Installation

Include jupitern/datatables in your project, by adding it to your composer.json file.
```javascript
{
    "require": {
        "jupitern/scheduler": "0.*"
    }
}
```

## Usage
```php
// instance Scheduler
$schedules = \Jupitern\Scheduler::instance()

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
->getNextSchedules('2020-01-01 00:00:00', 5);

// display schedules
foreach ($schedules as $schedule) {
    echo $schedule->format('Y-m-d H:i').PHP_EOL;
}

/*
output:
2020-01-01 08:00
2020-01-01 12:35
2020-01-01 16:00
2020-01-01 17:50
2020-01-01 00:00
*/

```

## Contributing

 - welcome to discuss a bugs, features and ideas.

## License

jupitern/table is release under the MIT license.

You are free to use, modify and distribute this software, as long as the copyright header is left intact
