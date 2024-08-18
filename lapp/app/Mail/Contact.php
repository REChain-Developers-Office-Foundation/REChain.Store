<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $form_subject;
    public $form_message;
    public $ip_address;
    public $site_title;
    public $mail_from;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $form_subject, $form_message, $ip_address, $site_title, $mail_from)
    {
        $this->name = $name;
        $this->email = $email;
        $this->form_subject = $form_subject;
        $this->form_message = $form_message;
        $this->ip_address = $ip_address;
        $this->site_title = $site_title;
        $this->mail_from = $mail_from;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->mail_from)->markdown('vendor.frontend.mail.contact')->subject(__('general.contact_message_received'));
    }
}
