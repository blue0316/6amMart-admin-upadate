<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundRejected extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $o_id;

    public function __construct($o_id)
    {
        $this->o_id = $o_id;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $o_id = $this->o_id;
        return $this->view('email-templates.refund_rejected',['id'=>$o_id]);
    }

}
