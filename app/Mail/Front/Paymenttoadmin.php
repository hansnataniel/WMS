<?php

namespace App\Mail\Front;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Setting;

class Paymenttoadmin extends Mailable
{
    public $token;
    
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $payment)
    {
        $this->subject = $subject;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $setting = Setting::first();

        $data['subject'] = $this->subject;
        $data['payment'] = $this->payment;
        return $this->subject($this->subject)
                    ->from($setting->sender_email, $setting->sender_email_name)
                    ->view('mails.front.payment', $data);
    }
}
