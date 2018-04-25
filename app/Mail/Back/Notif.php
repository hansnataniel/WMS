<?php

namespace App\Mail\Back;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Setting;

class Notif extends Mailable
{
    public $token;
    
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $product)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->product = $product;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $setting = Setting::first();

        $data['email'] = $this->email;
        $data['subject'] = $this->subject;
        $data['product'] = $this->product;
        return $this->subject($this->subject)
                    ->from($setting->sender_email, $setting->sender_email_name)
                    ->view('mails.back.notif', $data);
    }
}
