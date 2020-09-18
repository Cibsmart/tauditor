<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\PotentialUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var PotentialUser
     */
    public PotentialUser $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(PotentialUser $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Anambra State Payroll System (Account Created)')
                    ->greeting("Hi {$this->user->first_name},")
                    ->line('An account has been created for you on Anambra State payroll management platform (HRMEdge).')
                    ->line('Click on the button below to complete your registration')
                    ->action('Register', route('register.form', ['registration_token' => (string) $this->user->uuid]))
                    ->line('Note: Only password is required to complete registration');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
