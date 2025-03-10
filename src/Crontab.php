<?php

namespace OlegV\Cronjo;

use Exception;

class Crontab
{
    private string $jobs_block_start;
    private string $jobs_block_end;
    private string $hidden_job_comment;

    /**
     * @var list<Job>
     */
    private array $jobs = [];
    /**
     * @var list<Job>
     */
    private array $hidden_jobs = [];

    private string $content = '';

    /**
     * @throws Exception
     */
    public function __construct(
        string $app_name = "APP_JOBS",
    ) {
        $this->jobs_block_start = '#~~~ '.$app_name.' START ~~~';
        $this->jobs_block_end = '#~~~ '.$app_name.' END ~~~';
        $this->hidden_job_comment = '#~~~ '.$app_name.' SYSTEM ~~~';
        $this->loadJobs();
    }

    /**
     * Creates a hidden job to update the scheduled job list.
     *
     * @param  string  $schedule_path
     * @return void
     * @throws Exception
     */
    public function init(string $schedule_path): void
    {
        if (file_exists($schedule_path)) {
            $this->addJob((new Job('php '.$schedule_path.' '.$this->hidden_job_comment.' init'))->hourly());
            //if (count($this->hidden_jobs) > 0) {
            $this->save();
            //}
        } else {
            throw new Exception('The schedule file does not exist.');
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function rebuild(): void
    {
        $this->loadJobs();
        foreach ($this->hidden_jobs as $job) {
            if (str_contains($job->command, $this->hidden_job_comment.' init')) {
                $path = str_replace(' '.$this->hidden_job_comment.' init', '', $job->command);
                $path = str_replace('php ', '', $path);
            }
        }
        if (isset($path) && file_exists($path)) {
            $out = exec(PHP_BINARY.' '.$path);
            if (!is_string($out)) {
                throw new Exception('Run schedule is failed.');
            }
        } else {
            throw new Exception('The schedule file does not exist.');
        }
    }

    /**
     * Displays a list of jobs
     *
     * @return string
     */
    public function show(): string
    {
        $out = PHP_EOL;
        foreach ($this->jobs as $num => $job) {
            $out .= ($num + 1).' | '.$job.PHP_EOL;
        }
        $out .= PHP_EOL;
        return $out;
    }

    /**
     * Saves the jobs section
     *
     * @return void
     * @throws Exception
     */
    public function save(): void
    {
        $jobs = array_merge($this->jobs, $this->hidden_jobs);
        if (count($jobs) < 1) {
            throw new Exception('The job list is empty. Add jobs and try again.');
        }

        $this->checkOS();

        foreach ($jobs as $job) {
            if (!isset($job->command) || strlen($job->command) === 0) {
                throw new Exception("The command must not be empty. Enter and try again.");
            }
        }

        $this->content = $this->getCrontabContent();
        $this->content = $this->cleanSection();
        $this->content = $this->generateSection();

        $this->saveTab();
    }

    /**
     * Adds a new job to the existing ones
     *
     * @param  Job  $job
     * @param  bool  $rewrite
     * @return void
     */
    public function addJob(Job $job, bool $rewrite = true): void
    {
        $rewritten = false;
        if ($rewrite) {
            foreach ($this->jobs as &$cron_job) {
                if ($cron_job->command === $job->command) {
                    $cron_job = $job;
                    $rewritten = true;
                }
            }
            //и среди системных и скрытых задач тоже ищем
            foreach ($this->hidden_jobs as &$cron_hidden_job) {
                if ($cron_hidden_job->command === $job->command) {
                    $cron_hidden_job = $job;
                    $rewritten = true;
                }
            }
        }
        if (!$rewritten) {
            if (str_contains($job->command, $this->hidden_job_comment)) {
                $this->hidden_jobs[] = $job;
            } else {
                $this->jobs[] = $job;
            }
        }
    }

    /**
     * Removes the job section
     *
     * @return void
     * @throws Exception
     */
    public function removeJobs(): void
    {
        $this->checkOS();
        $this->content = $this->getCrontabContent();
        $this->content = $this->cleanSection();
        $this->saveTab();
    }

    /**
     * @return Job[]
     * @throws Exception
     */
    public function getJobs(): array
    {
        $this->loadJobs();
        return $this->jobs;
    }

    /**
     * Checking the operating system
     *
     * @return void
     * @throws Exception
     */
    private function checkOS(): void
    {
        if (str_contains(PHP_OS, 'WIN')) {
            throw new Exception(
                'Your operating system does not support this command.',
            );
        }
    }

    /**
     * Gets the jobs section and parses them
     *
     * @throws Exception
     */
    private function loadJobs(): void
    {
        $this->checkOS();
        $content = $this->getCrontabContent();
        $pattern = '!('.$this->jobs_block_start.')(.*?)('.$this->jobs_block_end.')!s';

        $data = [];
        $hidden = [];

        if (preg_match($pattern, $content, $matches) === 1) {
            $jobs = trim($matches[2], PHP_EOL);
            $jobs = explode(PHP_EOL, $jobs);
            foreach ($jobs as $job) {
                $obj = (new Job())->parseJob($job);
                //задачи помеченные как скрытые - не трогаем
                if (str_contains($obj->command, $this->hidden_job_comment)) {
                    $hidden[] = $obj;
                } else {
                    $data[] = $obj;
                }
            }
        }
        $this->hidden_jobs = $hidden;
        $this->jobs = $data;
    }

    /**
     * Creates a task section
     *
     * @return string
     */
    private function generateSection(): string
    {
        if (count($this->jobs) > 0 || count($this->hidden_jobs) > 0) {
            if (!str_ends_with($this->content, PHP_EOL)) {
                $this->content .= PHP_EOL;
            }
            $this->content .= $this->jobs_block_start.PHP_EOL;
            //jobs
            if (count($this->jobs) > 0) {
                foreach ($this->jobs as $job) {
                    $this->content .= $job.PHP_EOL;
                }
            }
            //hidden jobs
            if (count($this->hidden_jobs) > 0) {
                foreach ($this->hidden_jobs as $job) {
                    $this->content .= $job.PHP_EOL;
                }
            }
            $this->content .= $this->jobs_block_end.PHP_EOL;
        }

        return $this->content;
    }

    /**
     * Clears the jobs section of the crontab contents
     *
     * @return string
     */
    private function cleanSection(): string
    {
        /** @var string $out */
        $out = preg_replace(
            '!'.preg_quote($this->jobs_block_start).'.*?'
            .preg_quote($this->jobs_block_end.PHP_EOL).'!s',
            '',
            $this->content,
        );
        return trim($out, PHP_EOL);
    }

    /**
     * Gets the contents of crontab
     *
     * @return string
     * @throws Exception
     */
    private function getCrontabContent(): string
    {
        try {
            $content = shell_exec('crontab -l');
            if (is_null($content)) {
                //create
                exec('echo "" | crontab -');
                return '';
            }
            $content = (string)$content;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $content;
    }

    /**
     * Saves to crontab
     *
     * @return void
     * @throws Exception
     */
    private function saveTab(): void
    {
        $this->content = str_replace(['%', '"', '$'], ['%%', '\"', '\$'], $this->content);
        try {
            exec('echo "'.$this->content.'" | crontab -');
        } catch (Exception $e) {
            $error = $e->getMessage();
            throw new Exception('Error saving crontab: '.$error, 0, $e);
        }
    }
}