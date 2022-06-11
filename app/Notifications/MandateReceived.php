<?php

namespace App\Notifications;

use App\Models\LoanMandate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use function route;

class MandateReceived extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var LoanMandate
     */
    private LoanMandate $mandate;

    /**
     * Create a new notification instance.
     *
     * @param  LoanMandate  $mandate
     * @param  User  $user
     */
    public function __construct(LoanMandate $mandate)
    {
        $this->mandate = $mandate;
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
            ->subject("New Fidelity Loan Mandate Received - Mandate Reference: {$this->mandate->reference}")
            ->greeting('Hi Admin,')
            ->line('There is a new loan mandate from Fidelity Bank awaiting your action.')
            ->line('Click on the button below to view the mandate or use the mandate reference to view the mandate')
            ->action('View Mandate', url(route('fidelity.show', $this->mandate->id)))
            ->line('Ensure to update the mandate status after processing');
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
