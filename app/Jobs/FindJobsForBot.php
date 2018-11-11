<?php

namespace App\Jobs;

use App\Action\AssignJobToBot;
use App\Bot;
use App\Cluster;
use App\Enums\BotStatusEnum;
use App\Enums\JobStatusEnum;
use App\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FindJobsForBot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Bot
     */
    private $bot;

    /**
     * Create a new job instance.
     *
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \App\Exceptions\BotIsNotIdle
     * @throws \App\Exceptions\BotIsNotValidWorker
     * @throws \App\Exceptions\JobAssignmentFailed
     * @throws \App\Exceptions\JobIsNotQueued
     */
    public function handle()
    {
        if($this->bot->status != BotStatusEnum::IDLE) {
            return;
        }

        $jobForBotWorker = Job::whereStatus(JobStatusEnum::QUEUED)
            ->where('worker_id', $this->bot->id)
            ->where('worker_type', $this->bot->getMorphClass())
            ->orderBy('created_at')
            ->first();

        if($jobForBotWorker != null) {
            $assign = new AssignJobToBot($this->bot);
            $assign->fromJob($jobForBotWorker);

            return;
        }

        /** @var Cluster $cluster */
        $cluster = $this->bot->cluster;
        $jobForCluster = Job::whereStatus(JobStatusEnum::QUEUED)
            ->where('worker_id', $cluster->id)
            ->where('worker_type', $cluster->getMorphClass())
            ->orderBy('created_at')
            ->first();

        if($jobForCluster != null) {
            $assign = new AssignJobToBot($this->bot);
            $assign->fromJob($jobForCluster);
        }
    }
}