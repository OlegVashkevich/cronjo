<?php

use OlegV\Cronjo\Crontab;
use OlegV\Cronjo\Job;

require __DIR__.'/../../vendor/autoload.php';

try {
    $crontab = new Crontab('TEST');
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
    ); //30 19 1 1 2,5 echo "Hello World6"
    $crontab->save();
} catch (Exception $e) {
    print_r($e->getMessage());
}
