<?php

namespace Tests\Cron;

use Exception;
use OlegV\Cronjo\Crontab;
use OlegV\Cronjo\Job;
use PHPUnit\Framework\TestCase;

class CrontabTest extends TestCase
{
    private string $app_name = 'TEST';

    /**
     * @throws Exception
     */
    public function testConstructor(): void
    {
        $crontab = new Crontab($this->app_name);
        $this->assertObjectHasProperty('jobs_block_start', $crontab);
        $this->assertObjectHasProperty('jobs_block_end', $crontab);
        $this->assertObjectHasProperty('hidden_job_comment', $crontab);
    }

    /**
     * @throws Exception
     */
    public function testAdd(): void
    {
        $crontab = new Crontab($this->app_name);
        $crontab->removeJobs();
        $crontab->addJob((new Job('php job1.php'))->daily());
        $crontab->addJob(new Job('php job2.php'));
        $crontab->addJob(new Job('php job2.php'));
        $crontab->save();

        $jobs = $crontab->getJobs();
        foreach ($jobs as &$job) {
            $job = (string)$job;
        }
        $crontab->removeJobs();
        $this->assertSame([
            '0 0 * * * php job1.php',
            '* * * * * php job2.php',
        ], $jobs);
    }

    /**
     * @throws Exception
     */
    public function testInitAndRebuild(): void
    {
        $crontab = new Crontab($this->app_name);
        $crontab->init(__DIR__.'/../tests/data/schedule.php');

        $crontab->rebuild();

        $crontab = new Crontab($this->app_name);
        $jobs = $crontab->getJobs();
        foreach ($jobs as &$job) {
            $job = (string)$job;
        }
        $crontab->removeJobs();
        $this->assertSame([
            '0 0 * * * echo "Hello World"',
            '0 * * * * echo "Hello World2"',
            '*/30 * * * * echo "Hello World3"',
            '1,3,6 * * * * echo "Hello World4"',
            '30 19 7,17 * * echo "Hello World5"',
            '30 19 1 1 2,5 echo "Hello World6"',
        ], $jobs);
    }

    /**
     * @throws Exception
     */
    public function testInitException(): void
    {
        $this->expectExceptionMessage('The schedule file does not exist.');
        $crontab = new Crontab($this->app_name);
        $crontab->init('bad_file_path');
        $crontab->removeJobs();
    }

    /**
     * @throws Exception
     */
    public function testRebuildException(): void
    {
        $this->expectExceptionMessage('The schedule file does not exist.');
        $path = 'tests/data/bad_schedule.php';
        file_put_contents($path, '');
        $crontab = new Crontab($this->app_name);
        $crontab->init('tests/data/schedule.php');
        $crontab->init($path);
        //check rewrite hidden job
        $crontab->init($path);
        unlink($path);
        $crontab->rebuild();
        $crontab->removeJobs();
    }

    /**
     * @throws Exception
     */
    public function testEmptyJobsException(): void
    {
        //clear previous tests data
        $crontab = new Crontab($this->app_name);
        $crontab->removeJobs();
        $this->expectExceptionMessage('The job list is empty. Add jobs and try again.');
        $crontab = new Crontab($this->app_name);
        $crontab->save();
    }

    /**
     * @throws Exception
     */
    public function testEmptyCommandJobsException(): void
    {
        $this->expectExceptionMessage('The command must not be empty. Enter and try again.');
        $crontab = new Crontab($this->app_name);
        $crontab->addJob(new Job(''));
        $crontab->save();
    }

    /**
     * @throws Exception
     */
    public function testShow(): void
    {
        $crontab = new Crontab($this->app_name);
        $crontab->removeJobs();
        $crontab->addJob((new Job('job1.php'))->daily());
        $crontab->addJob(new Job('job2.php'));
        $crontab->addJob(new Job('job3.php'));
        $crontab->save();
        //echo $crontab->show();
        $out = PHP_EOL;
        $out .= '1 | 0 0 * * * job1.php'.PHP_EOL;
        $out .= '2 | * * * * * job2.php'.PHP_EOL;
        $out .= '3 | * * * * * job3.php'.PHP_EOL;
        $out .= PHP_EOL;
        $this->assertEquals($out, $crontab->show());
        $crontab->removeJobs();
    }
}