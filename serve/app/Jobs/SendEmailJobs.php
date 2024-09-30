<?php

namespace App\Jobs;

use App\Jobs\Middleware\RateLimited;
use App\Mail\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $code;

    public $to;

    public $title;

    public $tries = 1;

    public $timeout = 60;

    public function __construct($to, $code, $title = null)
    {
        $this->to = $to;
        $this->code = $code;
        $this->title = $title ?? '邮箱验证码';
    }

    public function middleware()
    {
        return [new RateLimited];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->to)
            ->send(new SendEmail($this->code, $this->title));
    }

    public function failed(Throwable $exception)
    {
        Mail::to($this->to)
            ->send(new SendEmail($this->code, $this->title));
    }
}
