<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Ichtrojan\Otp\Otp;

class EmailVerificationNotification extends Notification
{
    use Queueable;
    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    private $otp;
    public $email;


    /**
     * Create a new notification instance.
     */
    public function __construct(string $email)
    {
        $this->email=$email;
        $this->subject='Verification Needed';
        $this->message='Use The Below Code For Verification Process';
        $this->mailer='smtp';
        $this->otp=new otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $otp=$this->otp->generate($this->email,'numeric',5,60);
        return (new MailMessage)
                    ->mailer($this->mailer)
                    ->subject($this->subject)
                    ->greeting('Hello '.$notifiable->username)
                    ->line($this->message)
                    ->line('Code '.$otp->token);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
