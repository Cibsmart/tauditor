<?php

namespace App\Notifications;

use App\Models\LoanMandate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MandateCancelled extends Notification implements ShouldQueue
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
            ->subject("Fidelity Loan Mandate Cancelled - Mandate Reference: {$this->mandate->reference}")
            ->greeting('Hi Admin,')
            ->line('A loan mandate from Fidelity Bank has been cancelled and awaits your action.')
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
