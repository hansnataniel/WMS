<?php

namespace App\Mail\Front;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Setting;

class Transactiontoadmin extends Mailable
{
    public $token;
    
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $subject2, $product, $transaction)
    {
        $this->email = $email;
        $this->subject2 = $subject2;
        $this->product = $product;
        $this->transaction = $transaction;
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
        $data['subject2'] = $this->subject2;
        $data['product'] = $this->product;
        $data['transaction'] = $this->transaction;
        return $this->subject($this->subject2)
                    ->from($setting->sender_email, $setting->sender_email_name)
                    ->view('mails.front.transactiontoadmin', $data);
    }
}
