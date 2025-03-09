<?php

use OlegV\Cronjo\Crontab;
use OlegV\Cronjo\Job;

require __DIR__.'/../../vendor/autoload.php';

try {
    $crontab = new Crontab('TEST');
    $crontab->addJob((new Job('echo "Hello World"'))->daily());
    $crontab->addJob((new Job('echo "Hello World2"'))->hourly());
    $crontab->addJob((new Job('echo "Hello World3"'))->everyThirtyMinutes());
    $crontab->addJob((new Job('echo "Hello World4"'))->hourlyAt([1, 3, 6]));
    $crontab->addJob((new Job('echo "Hello World5"'))->twiceMonthly(7, 17, '19:30'));
    $crontab->save();
} catch (Exception $e) {
    print_r($e->getMessage());
}
