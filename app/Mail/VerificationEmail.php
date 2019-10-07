<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Consts;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;
    protected $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $code)
    {
        $this->email = $email;
        $this->code = $code;
        $this->onQueue(Consts::QUEUE_SMS);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('auth.verification_email')
                    ->subject("The Test")
                    ->to($this->email)
                    ->with([
                      'email' => $this->email,
                      'code' => $this->code
                    ]);
    }
}
