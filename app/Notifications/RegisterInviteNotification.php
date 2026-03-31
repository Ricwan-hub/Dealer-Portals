<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class RegisterInviteNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $appName = config('app.name');

        $url = $this->generateInvitationUrl($notifiable->routes['mail']);

        return (new MailMessage)
                    ->subject('Your Invitation to Join the Dealer Portal')
                    ->greeting('Dear Member.')
                    ->line("As an authorized dealer for Oriel Limited, we are cordially inviting you to register on our Online Dealers Portal.")
                    ->line("By creating a login account, you will gain exclusive access to real-time information on the latest product availability and pricing.")
                    ->action('Accept Invitation', url($url))
                    ->line('Please note that this invite link will expire after 24 hours.');
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

    /**
     * Generates a unique signed URL that the mail receiver can user to register.
     * The URL contains the UserLevel and the receiver's email address, and will be valid for 1 day.
     *
     * @param $notifiable
     * @return string
     */
    public function generateInvitationUrl(string $email)
    {
        return URL::temporarySignedRoute('filament.dealer.auth.register', now()->addDay(), [
            'email' => $email
        ]);
    }

}
