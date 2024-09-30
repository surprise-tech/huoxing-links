<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $config;

    public $title;

    public $code;

    public function __construct($code, $title)
    {
        $this->title = $title;
        $this->code = $code;
    }

    public function build()
    {
        $this->text('email.send', ['code' => $this->code])
            ->subject($this->title);

        return $this;
    }
}
