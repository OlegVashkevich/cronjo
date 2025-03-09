<?php

namespace Tests;

use OlegV\Cronjo\Job;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    public function testConstructor(): void
    {
        $Job = new Job();
        $this->assertObjectHasProperty('expression', $Job);
        $this->assertObjectHasProperty('command', $Job);
    }

    public function testParser(): void
    {
        $Job = new Job();
        $Job->parseJob('* * * * * '.PHP_BINARY.' test');
        $this->assertEquals('* * * * *', $Job->expression);
        $this->assertEquals('test', $Job->command);
    }

    public function testEveryMinute(): void
    {
        $Job = new Job();
        $Job->everyMinute();
        $this->assertEquals('* * * * *', $Job->expression);
    }

    public function testEveryTwoMinutes(): void
    {
        $Job = new Job();
        $Job->everyTwoMinutes();
        $this->assertEquals('*/2 * * * *', $Job->expression);
    }

    public function testEveryThreeMinutes(): void
    {
        $Job = new Job();
        $Job->everyThreeMinutes();
        $this->assertEquals('*/3 * * * *', $Job->expression);
    }

    public function testEveryFourMinutes(): void
    {
        $Job = new Job();
        $Job->everyFourMinutes();
        $this->assertEquals('*/4 * * * *', $Job->expression);
    }

    public function testEveryFiveMinutes(): void
    {
        $Job = new Job();
        $Job->everyFiveMinutes();
        $this->assertEquals('*/5 * * * *', $Job->expression);
    }

    public function testEveryTenMinutes(): void
    {
        $Job = new Job();
        $Job->everyTenMinutes();
        $this->assertEquals('*/10 * * * *', $Job->expression);
    }

    public function testEveryFifteenMinutes(): void
    {
        $Job = new Job();
        $Job->everyFifteenMinutes();
        $this->assertEquals('*/15 * * * *', $Job->expression);
    }

    public function testEveryThirtyMinutes(): void
    {
        $Job = new Job();
        $Job->everyThirtyMinutes();
        $this->assertEquals('*/30 * * * *', $Job->expression);
    }

    public function testHourly(): void
    {
        $Job = new Job();
        $Job->hourly();
        $this->assertEquals('0 * * * *', $Job->expression);
    }

    public function testHourlyAtInt(): void
    {
        $Job = new Job();
        $Job->hourlyAt(6);
        $this->assertEquals('6 * * * *', $Job->expression);
    }

    public function testHourlyAtArray(): void
    {
        $Job = new Job();
        $Job->hourlyAt([1, 3, 6]);
        $this->assertEquals('1,3,6 * * * *', $Job->expression);
    }

    public function testHourlyAtString(): void
    {
        $Job = new Job();
        $Job->hourlyAt('1,3,5');
        $this->assertEquals('1,3,5 * * * *', $Job->expression);
    }

    public function testEveryOddHourInt(): void
    {
        $Job = new Job();
        $Job->everyOddHour(6);
        $this->assertEquals('6 1-23/2 * * *', $Job->expression);
    }

    public function testEveryOddHourArray(): void
    {
        $Job = new Job();
        $Job->everyOddHour([1, 3, 6]);
        $this->assertEquals('1,3,6 1-23/2 * * *', $Job->expression);
    }

    public function testEveryOddHourString(): void
    {
        $Job = new Job();
        $Job->everyOddHour('1,3,5');
        $this->assertEquals('1,3,5 1-23/2 * * *', $Job->expression);
    }

    public function testEveryTwoHoursInt(): void
    {
        $Job = new Job();
        $Job->everyTwoHours(6);
        $this->assertEquals('6 */2 * * *', $Job->expression);
    }

    public function testEveryTwoHoursArray(): void
    {
        $Job = new Job();
        $Job->everyTwoHours([1, 3, 6]);
        $this->assertEquals('1,3,6 */2 * * *', $Job->expression);
    }

    public function testEveryTwoHoursString(): void
    {
        $Job = new Job();
        $Job->everyTwoHours('1,3,5');
        $this->assertEquals('1,3,5 */2 * * *', $Job->expression);
    }

    public function testEveryThreeHoursInt(): void
    {
        $Job = new Job();
        $Job->everyThreeHours(6);
        $this->assertEquals('6 */3 * * *', $Job->expression);
    }

    public function testEveryThreeHoursArray(): void
    {
        $Job = new Job();
        $Job->everyThreeHours([1, 3, 6]);
        $this->assertEquals('1,3,6 */3 * * *', $Job->expression);
    }

    public function testEveryThreeHoursString(): void
    {
        $Job = new Job();
        $Job->everyThreeHours('1,3,5');
        $this->assertEquals('1,3,5 */3 * * *', $Job->expression);
    }

    public function testEveryFourHoursInt(): void
    {
        $Job = new Job();
        $Job->everyFourHours(6);
        $this->assertEquals('6 */4 * * *', $Job->expression);
    }

    public function testEveryFourHoursArray(): void
    {
        $Job = new Job();
        $Job->everyFourHours([1, 3, 6]);
        $this->assertEquals('1,3,6 */4 * * *', $Job->expression);
    }

    public function testEveryFourHoursString(): void
    {
        $Job = new Job();
        $Job->everyFourHours('1,3,5');
        $this->assertEquals('1,3,5 */4 * * *', $Job->expression);
    }

    public function testEverySixHoursInt(): void
    {
        $Job = new Job();
        $Job->everySixHours(6);
        $this->assertEquals('6 */6 * * *', $Job->expression);
    }

    public function testEverySixHoursArray(): void
    {
        $Job = new Job();
        $Job->everySixHours([1, 3, 6]);
        $this->assertEquals('1,3,6 */6 * * *', $Job->expression);
    }

    public function testEverySixHoursString(): void
    {
        $Job = new Job();
        $Job->everySixHours('1,3,5');
        $this->assertEquals('1,3,5 */6 * * *', $Job->expression);
    }

    public function testDaily(): void
    {
        $Job = new Job();
        $Job->daily();
        $this->assertEquals('0 0 * * *', $Job->expression);
    }

    public function testDailyAt(): void
    {
        $Job = new Job();
        $Job->dailyAt('19:30');
        $this->assertEquals('30 19 * * *', $Job->expression);
    }

    public function testTwiceDaily(): void
    {
        $Job = new Job();
        $Job->twiceDaily(11, 23);
        $this->assertEquals('0 11,23 * * *', $Job->expression);
    }

    public function testTwiceDailyAt(): void
    {
        $Job = new Job();
        $Job->twiceDailyAt(11, 23, 54);
        $this->assertEquals('54 11,23 * * *', $Job->expression);
    }

    public function testWeekdays(): void
    {
        $Job = new Job();
        $Job->weekdays();
        $this->assertEquals('* * * * 1-5', $Job->expression);
    }

    public function testWeekends(): void
    {
        $Job = new Job();
        $Job->weekends();
        $this->assertEquals('* * * * 6,0', $Job->expression);
    }

    public function testMondays(): void
    {
        $Job = new Job();
        $Job->mondays();
        $this->assertEquals('* * * * 1', $Job->expression);
    }

    public function testTuesdays(): void
    {
        $Job = new Job();
        $Job->tuesdays();
        $this->assertEquals('* * * * 2', $Job->expression);
    }

    public function testWednesdays(): void
    {
        $Job = new Job();
        $Job->wednesdays();
        $this->assertEquals('* * * * 3', $Job->expression);
    }

    public function testThursdays(): void
    {
        $Job = new Job();
        $Job->thursdays();
        $this->assertEquals('* * * * 4', $Job->expression);
    }

    public function testFridays(): void
    {
        $Job = new Job();
        $Job->fridays();
        $this->assertEquals('* * * * 5', $Job->expression);
    }

    public function testSaturdays(): void
    {
        $Job = new Job();
        $Job->saturdays();
        $this->assertEquals('* * * * 6', $Job->expression);
    }

    public function testSundays(): void
    {
        $Job = new Job();
        $Job->sundays();
        $this->assertEquals('* * * * 0', $Job->expression);
    }

    public function testWeekly(): void
    {
        $Job = new Job();
        $Job->weekly();
        $this->assertEquals('0 0 * * 0', $Job->expression);
    }

    public function testWeeklyOnInt(): void
    {
        $Job = new Job();
        $Job->weeklyOn(6, '19:30');
        $this->assertEquals('30 19 * * 6', $Job->expression);
    }

    public function testWeeklyOnArray(): void
    {
        $Job = new Job();
        $Job->weeklyOn([1, 3, 6], '19:30');
        $this->assertEquals('30 19 * * 1,3,6', $Job->expression);
    }

    public function testWeeklyOnString(): void
    {
        $Job = new Job();
        $Job->weeklyOn('1,3,5', '19:30');
        $this->assertEquals('30 19 * * 1,3,5', $Job->expression);
    }

    public function testMonthly(): void
    {
        $Job = new Job();
        $Job->monthly();
        $this->assertEquals('0 0 1 * *', $Job->expression);
    }

    public function testMonthlyOn(): void
    {
        $Job = new Job();
        $Job->monthlyOn(17, '19:30');
        $this->assertEquals('30 19 17 * *', $Job->expression);
    }

    public function testTwiceMonthly(): void
    {
        $Job = new Job();
        $Job->twiceMonthly(7, 17, '19:30');
        $this->assertEquals('30 19 7,17 * *', $Job->expression);
    }

    public function testQuarterly(): void
    {
        $Job = new Job();
        $Job->quarterly();
        $this->assertEquals('0 0 1 1-12/3 *', $Job->expression);
    }

    public function testQuarterlyOn(): void
    {
        $Job = new Job();
        $Job->quarterlyOn(17, '19:30');
        $this->assertEquals('30 19 17 1-12/3 *', $Job->expression);
    }

    public function testYearly(): void
    {
        $Job = new Job();
        $Job->yearly();
        $this->assertEquals('0 0 1 1 *', $Job->expression);
    }

    public function testYearlyOn(): void
    {
        $Job = new Job();
        $Job->yearlyOn(7, 17, '19:30');
        $this->assertEquals('30 19 17 7 *', $Job->expression);
    }

    public function testBinary(): void
    {
        $Job = new Job('test');
        $Job->setBinary('bin/sh');
        $this->assertEquals('* * * * * bin/sh test', (string)$Job);
    }
}