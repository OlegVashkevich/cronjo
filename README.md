# Cronjo
Library for scheduling your job by cron

## Features
- lightweight
- dependency-free
- 90+% test coverage
- phpstan max lvl
- phpstan full strict rules

## Install
```shell
composer require olegv/cronjo
```
## Usage
### Schedule
1. create schedule file, e.g. - `path_to/schedule.php`
2. script your task schedule into a file
    ```php
    <?php
    
    use OlegV\Cronjo\Crontab;
    use OlegV\Cronjo\Job;
    
    require __DIR__.'/../../vendor/autoload.php';
    
    try {
        $crontab = new Crontab('APP_NAME');
        $crontab->addJob((new Job('echo "Hello World"'))->daily());
        $crontab->addJob((new Job('echo "Hello World2"'))->hourly());
        $crontab->addJob(
            (new Job('echo "Hello World3"'))->everyThirtyMinutes(),
        ); //*/30 * * * * echo "Hello World3"
        $crontab->addJob(
            (new Job('echo "Hello World4"'))->hourlyAt([1, 3, 6]),
        ); //1,3,6 * * * * echo "Hello World4"
        $crontab->addJob(
            (new Job('echo "Hello World5"'))->twiceMonthly(7, 17, '19:30'),
        ); //30 19 7,17 * * echo "Hello World5"
        $crontab->addJob(
            (new Job('echo "Hello World6"'))
                ->yearly()
                ->days([Job::TUESDAY, Job::FRIDAY])
                ->at('19:30'),
        ); //30 19 1 1 2,5 echo "Hello World6" - WARNING!!! - read bottom[^1]
        $crontab->save();
    } catch (Exception $e) {
        print_r($e->getMessage());
    }
    ```
> All cron conditions (start time) are checked by "logical AND", 
> except for the conditions "day of the week" and "day of the month" - specified together, 
> they are processed by "logical OR", that is, "by any day", 
> which is reflected in the documentation (Ubuntu, Debian, FreeBSD).
> 
> `30 19 1 1 2,5` - At 19:30 on day-of-month 1 + Tuesday + Friday in January


### Frequency Options
| Method                           | Description                                              |
|----------------------------------|----------------------------------------------------------|
| ->cron('* * * * *');             | Run the task on a custom cron schedule.                  |
| ->everyMinute();                 | Run the task every minute.                               |
| ->everyTwoMinutes();             | Run the task every two minutes.                          |
| ->everyThreeMinutes();           | Run the task every three minutes.                        |
| ->everyFourMinutes();            | Run the task every four minutes.                         |
| ->everyFiveMinutes();            | Run the task every five minutes.                         |
| ->everyTenMinutes();             | Run the task every ten minutes.                          |
| ->everyFifteenMinutes();         | Run the task every fifteen minutes.                      |
| ->everyThirtyMinutes();          | Run the task every thirty minutes.                       |
| ->hourly();                      | Run the task every hour.                                 |
| ->hourlyAt(17);                  | Run the task every hour at 17 minutes past the hour.     |
| ->everyOddHour($minutes = 0);    | Run the task every odd hour.                             |
| ->everyTwoHours($minutes = 0);   | Run the task every two hours.                            |
| ->everyThreeHours($minutes = 0); | Run the task every three hours.                          |
| ->everyFourHours($minutes = 0);  | Run the task every four hours.                           |
| ->everySixHours($minutes = 0);   | Run the task every six hours.                            |
| ->daily();                       | Run the task every day at midnight.                      |
| ->dailyAt('13:00');              | Run the task every day at 13:00.                         |
| ->twiceDaily(1, 13);             | Run the task daily at 1:00 & 13:00.                      |
| ->twiceDailyAt(1, 13, 15);       | Run the task daily at 1:15 & 13:15.                      |
| ->weekly();                      | Run the task every Sunday at 00:00.                      |
| ->weeklyOn(1, '8:00');           | Run the task every week on Monday at 8:00.               |
| ->monthly();                     | Run the task on the first day of every month at 00:00.   |
| ->monthlyOn(4, '15:00');         | Run the task every month on the 4th at 15:00.            |
| ->twiceMonthly(1, 16, '13:00');  | Run the task monthly on the 1st and 16th at 13:00.       |
| ->lastDayOfMonth('15:00');       | Run the task on the last day of the month at 15:00.      |
| ->quarterly();                   | Run the task on the first day of every quarter at 00:00. |
| ->quarterlyOn(4, '14:00');       | Run the task every quarter on the 4th at 14:00.          |
| ->yearly();                      | Run the task on the first day of every year at 00:00.    |
| ->yearlyOn(6, 1, '17:00');       | Run the task every year on June 1st at 17:00.            |
| ->weekdays();                    | Limit the task to weekdays.                              |
| ->weekends();                    | Limit the task to weekends.                              |
| ->sundays();                     | Limit the task to Sunday.                                |
| ->mondays();                     | Limit the task to Monday.                                |
| ->tuesdays();                    | Limit the task to Tuesday.                               |
| ->wednesdays();                  | Limit the task to Wednesday.                             |
| ->thursdays();                   | Limit the task to Thursday.                              |
| ->fridays();                     | Limit the task to Friday.                                |
| ->saturdays();                   | Limit the task to Saturday.                              |
| ->days(array\|mixed);            | Limit the task to specific days.                         |
| ->at('13:00');                   | Run the task at 13:00.                                   |

### Crontab management
now you can manage cron jobs for your app(APP_NAME) in file `path_to/schedule.php`
manually `php path_to/schedule.php` 
```php
crontab -l: 
#~~~ APP_NAME START ~~~
0 0 * * * echo "Hello World"
0 * * * * echo "Hello World2"
*/30 * * * * echo "Hello World3"
1,3,6 * * * * echo "Hello World4"
30 19 7,17 * * echo "Hello World5"
30 19 1 1 2,5 echo "Hello World6"
#~~~ APP_NAME END ~~~
```

or auto 
```php
//add init job auto for rebuild schedule
$crontab = new Crontab('APP_NAME');
$crontab->init('path_to/schedule.php');

//crontab -l:
//#~~~ APP_NAME START ~~~
//0 * * * * php path_to/schedule.php #~~~ APP_NAME SYSTEM ~~~ init
//#~~~ APP_NAME END ~~~

//after if you need rebuild manually jobs for app  - APP_NAME
$crontab = new Crontab('APP_NAME');
$crontab->rebuild();

//remove all jobs for  APP_NAME
$crontab = new Crontab('APP_NAME');
$crontab->removeJobs();
```
