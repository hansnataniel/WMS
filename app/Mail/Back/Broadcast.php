<?php

namespace App\Mail\Back;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Setting;

class Broadcast extends Mailable
{
    public $token;
    
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $title, $description)
    {
        $this->email = $email;
        $this->title = $title;
        $this->description = $description;
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
        $data['title'] = $this->title;
        $data['description'] = $this->description;
        return $this->subject($this->title)
                    ->from($setting->sender_email)
                    ->view('mails.back.newsletter', $data);
    }
}
