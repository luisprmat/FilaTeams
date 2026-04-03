<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelDaily\FilaTeams\Models\TeamInvitation;
use Illuminate\Notifications\Messages\MailMessage;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TeamInvitation $invitation,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = $this->invitation->expires_at
            ? URL::temporarySignedRoute(
                'filateams.invitations.accept',
                $this->invitation->expires_at,
                ['code' => $this->invitation->code]
            )
            : URL::signedRoute('filateams.invitations.accept', ['code' => $this->invitation->code]);

        return (new MailMessage)
            ->subject("You've been invited to join " . $this->invitation->team->name)
            ->line($this->invitation->inviter->name . ' has invited you to join the ' . $this->invitation->team->name . ' team.')
            ->action('Accept Invitation', $acceptUrl)
            ->line('This invitation will expire on ' . $this->invitation->expires_at->format('F j, Y') . '.');
    }
}
