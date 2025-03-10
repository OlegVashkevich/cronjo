<?php

namespace OlegV\Cronjo;

use Exception;
use Stringable;

class Job implements Stringable
{
    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    /**
     * @param  string  $command  shell command or php script
     * @param  string  $expression  cron expression representing the frequency of job execution
     */
    public function __construct(
        public string $command = '',
        public string $expression = '* * * * *',
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->expression.' '.$this->command;
    }

    /**
     * Parses the job received from crontab
     *
     * @param  string  $job
     * @return $this
     * @throws Exception
     */
    public function parseJob(string $job): static
    {
        $space = ' ';
        $parts = preg_split('/\s+/', $job);
        if (is_array($parts)) {
            $this->expression = implode($space, array_slice($parts, 0, 5));
            $this->command = implode($space, array_splice($parts, 5));
        } else {
            throw new Exception('Job parse error');
        }

        return $this;
    }

    /**
     * Sets a cron expression representing how often a job is executed.
     *
     * @param  string  $expression
     * @return $this
     */
    public function cron(string $expression): static
    {
        $this->expression = $expression;

        return $this;
    }


    /**
     * Run job every minute
     *
     * @return $this
     */
    public function everyMinute(): static
    {
        return $this->spliceIntoPosition(1, '*');
    }

    /**
     * Run job every 2 minutes
     *
     * @return $this
     */
    public function everyTwoMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/2');
    }

    /**
     * Run job every 3 minutes
     *
     * @return $this
     */
    public function everyThreeMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/3');
    }

    /**
     * Run job every 4 minutes
     *
     * @return $this
     */
    public function everyFourMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/4');
    }

    /**
     * Run job every 5 minutes
     *
     * @return $this
     */
    public function everyFiveMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/5');
    }

    /**
     * Run job every 10 minutes
     *
     * @return $this
     */
    public function everyTenMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/10');
    }

    /**
     * Run job every 15 minutes
     *
     * @return $this
     */
    public function everyFifteenMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/15');
    }

    /**
     * Run job every half hour
     *
     * @return $this
     */
    public function everyThirtyMinutes(): static
    {
        return $this->spliceIntoPosition(1, '*/30');
    }

    /**
     * Run job every hour
     *
     * @return $this
     */
    public function hourly(): static
    {
        return $this->spliceIntoPosition(1, "0");
    }

    /**
     * The job will run every hour with the specified offset during the hour
     *
     * @param  int<0, 59>|array<int,int<0, 59>>|string  $minutes
     * @return $this
     */
    public function hourlyAt(int|array|string $minutes): static
    {
        return $this->hourBasedSchedule($minutes, '*');
    }

    /**
     * The job will be run every odd hour
     *
     * @param  int<0, 59>|array<int,int<0, 59>>|string  $minutes
     * @return $this
     */
    public function everyOddHour(array|int|string $minutes = 0): static
    {
        return $this->hourBasedSchedule($minutes, '1-23/2');
    }

    /**
     * The job will be run every 2 hours
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $minutes
     * @return $this
     */
    public function everyTwoHours(array|int|string $minutes = 0): static
    {
        return $this->hourBasedSchedule($minutes, '*/2');
    }

    /**
     * The job will be run every 3 hours
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $minutes
     * @return $this
     */
    public function everyThreeHours(array|int|string $minutes = 0): static
    {
        return $this->hourBasedSchedule($minutes, '*/3');
    }

    /**
     * The job will be run every 4 hours
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $minutes
     * @return $this
     */
    public function everyFourHours(array|int|string $minutes = 0): static
    {
        return $this->hourBasedSchedule($minutes, '*/4');
    }

    /**
     * The job will be run every 6 hours.
     *
     * @param  int<0, 59>|string|array<int,int<0, 59>>  $minutes
     * @return $this
     */
    public function everySixHours(array|int|string $minutes = 0): static
    {
        return $this->hourBasedSchedule($minutes, '*/6');
    }

    /**
     * The job will be run daily
     *
     * @return $this
     */
    public function daily(): static
    {
        return $this->hourBasedSchedule(0, 0);
    }

    /**
     * The job will be run at a given time(10:00, 19:30, etc.)
     *
     * @param  string  $time  etc.: 19:30
     * @return $this
     */
    public function at(string $time): static
    {
        return $this->dailyAt($time);
    }

    /**
     * The job will be run daily at the specified time (10:00, 19:30, etc.)
     *
     * @param  string  $time  etc.: 19:30
     * @return $this
     */
    public function dailyAt(string $time): static
    {
        $segments = explode(':', $time);

        return $this->hourBasedSchedule(
            count($segments) === 2 ? $segments[1] : '0',
            $segments[0],
        );
    }

    /**
     * The job will be run twice a day
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @return $this
     */
    public function twiceDaily(int $first = 1, int $second = 13): static
    {
        return $this->twiceDailyAt($first, $second);
    }

    /**
     * The job will be run twice a day with the specified offset in minutes
     *
     * @param  int<0, 23>  $first
     * @param  int<0, 23>  $second
     * @param  int<0, 59>  $minutes
     * @return $this
     */
    public function twiceDailyAt(int $first = 1, int $second = 13, int $minutes = 0): static
    {
        $hours = $first.','.$second;

        return $this->hourBasedSchedule($minutes, $hours);
    }

    /**
     * The job will be run on working days
     *
     * @return $this
     */
    public function weekdays(): static
    {
        return $this->days(self::MONDAY.'-'.self::FRIDAY);
    }

    /**
     * The job will be run on weekends.
     *
     * @return $this
     */
    public function weekends(): static
    {
        return $this->days(self::SATURDAY.','.self::SUNDAY);
    }

    /**
     * The job will be run on Monday
     *
     * @return $this
     */
    public function mondays(): static
    {
        return $this->days(self::MONDAY);
    }

    /**
     * The job will be run on Tuesday
     *
     * @return $this
     */
    public function tuesdays(): static
    {
        return $this->days(self::TUESDAY);
    }

    /**
     * The job will be run on Wednesday
     *
     * @return $this
     */
    public function wednesdays(): static
    {
        return $this->days(self::WEDNESDAY);
    }

    /**
     * The job will be run on Thursday
     *
     * @return $this
     */
    public function thursdays(): static
    {
        return $this->days(self::THURSDAY);
    }

    /**
     * The job will be run on Friday
     *
     * @return $this
     */
    public function fridays(): static
    {
        return $this->days(self::FRIDAY);
    }

    /**
     * The job will be run on Saturday
     *
     * @return $this
     */
    public function saturdays(): static
    {
        return $this->days(self::SATURDAY);
    }

    /**
     * The job will be run on Sunday
     *
     * @return $this
     */
    public function sundays(): static
    {
        return $this->days(self::SUNDAY);
    }

    /**
     * The job will be run weekly
     *
     * @return $this
     */
    public function weekly(): static
    {
        return $this
            ->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(5, "0");
    }

    /**
     * The job will run weekly on a specific day and time
     *
     * @param  int<0, 6>|string|array<int,int<0, 6>>  $dayOfWeek
     * @param  string  $time
     * @return $this
     */
    public function weeklyOn(int|string|array $dayOfWeek, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->days($dayOfWeek);
    }

    /**
     * The job will run monthly
     *
     * @return $this
     */
    public function monthly(): static
    {
        return $this
            ->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(3, "1");
    }

    /**
     * The job will be run monthly on a specific day and time
     *
     * @param  int<1, 31>  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function monthlyOn(int $dayOfMonth = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, (string)$dayOfMonth);
    }

    /**
     * The job will be run twice a month on a specific day and time
     *
     * @param  int<1, 31>  $first
     * @param  int<1, 31>  $second
     * @param  string  $time
     * @return $this
     */
    public function twiceMonthly(int $first = 1, int $second = 16, string $time = '0:0'): static
    {
        $daysOfMonth = $first.','.$second;

        $this->dailyAt($time);

        return $this->spliceIntoPosition(3, $daysOfMonth);
    }

    /**
     * The job will be run quarterly
     *
     * @return $this
     */
    public function quarterly(): static
    {
        return $this
            ->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(3, "1")
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * The job will be run quarterly on a specific day and time
     *
     * @param  int  $dayOfQuarter
     * @param  string  $time
     * @return $this
     */
    public function quarterlyOn(int $dayOfQuarter = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this
            ->spliceIntoPosition(3, (string)$dayOfQuarter)
            ->spliceIntoPosition(4, '1-12/3');
    }

    /**
     * The job will be run yearly
     *
     * @return $this
     */
    public function yearly(): static
    {
        return $this
            ->spliceIntoPosition(1, "0")
            ->spliceIntoPosition(2, "0")
            ->spliceIntoPosition(3, "1")
            ->spliceIntoPosition(4, "1");
    }

    /**
     * The job will be run annually on a specific month, day and time
     *
     * @param  int  $month
     * @param  int<1, 31>|string  $dayOfMonth
     * @param  string  $time
     * @return $this
     */
    public function yearlyOn(int $month = 1, int|string $dayOfMonth = 1, string $time = '0:0'): static
    {
        $this->dailyAt($time);

        return $this
            ->spliceIntoPosition(3, (string)$dayOfMonth)
            ->spliceIntoPosition(4, (string)$month);
    }

    /**
     * Specify the days of the week on which the job should be executed
     *
     * @param  int<0, 6>|string|array<int,int<0, 6>>  $days
     * @return $this
     */
    public function days(int|string|array $days): static
    {
        $days = is_array($days) ? $days : func_get_args();

        return $this->spliceIntoPosition(5, implode(',', $days));
    }

    /**
     * Run a job at the specified minutes and hours
     *
     * @param  int<0, 59>|array<int, int<0, 59>>|string  $minutes
     * @param  int<0, 23>|array<int, int<0, 23>>|string  $hours
     * @return $this
     */
    protected function hourBasedSchedule(int|array|string $minutes, int|array|string $hours): static
    {
        $minutes = is_array($minutes) ? implode(',', $minutes) : $minutes;

        $hours = is_array($hours) ? implode(',', $hours) : $hours;

        return $this
            ->spliceIntoPosition(1, (string)$minutes)
            ->spliceIntoPosition(2, (string)$hours);
    }

    /**
     * Inserts the given value into the given position of the expression
     *
     * @param  int  $position
     * @param  string  $value
     * @return $this
     */
    protected function spliceIntoPosition(int $position, string $value): static
    {
        $segments = preg_split("/\s+/", $this->expression);
        if (is_array($segments)) {
            $segments[$position - 1] = $value;
            return $this->cron(implode(' ', $segments));
        }

        return $this->cron($this->expression);
    }
}