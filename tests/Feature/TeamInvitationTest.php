<?php

declare(strict_types=1);

use Tests\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\URL;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Models\TeamInvitation;
use LaravelDaily\FilaTeams\Notifications\TeamInvitationNotification;

beforeEach(function (): void {
    $this->inviter = User::factory()->create();
    $this->team    = Team::factory()->create();
});

it('notification generates a valid signed url', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'team_id'    => $this->team->id,
        'invited_by' => $this->inviter->id,
        'email'      => 'invitee@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $mail = (new TeamInvitationNotification($invitation))->toMail($invitation);

    expect($mail->actionUrl)
        ->toContain('signature=')
        ->toContain('expires=')
        ->and(URL::hasValidSignature(Request::create($mail->actionUrl)))->toBeTrue();
});

it('user can accept invitation with a valid signed url', function (): void {
    $user       = User::factory()->create(['email' => 'invitee@example.com']);
    $invitation = TeamInvitation::factory()->create([
        'team_id'    => $this->team->id,
        'invited_by' => $this->inviter->id,
        'email'      => $user->email,
    ]);

    $signedUrl = URL::signedRoute('filateams.invitations.accept', ['code' => $invitation->code]);

    Filament::shouldReceive('getUrl')->withAnyArgs()->andReturn('/dashboard');

    $this->actingAs($user)
        ->get($signedUrl)
        ->assertRedirect('/dashboard');

    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

it('expired invitation redirects to the filament panel login page', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'team_id'    => $this->team->id,
        'invited_by' => $this->inviter->id,
        'email'      => 'invitee@example.com',
        'expires_at' => now()->subDay(),
    ]);

    $signedUrl = URL::signedRoute('filateams.invitations.accept', ['code' => $invitation->code]);

    Filament::shouldReceive('getLoginUrl')->once()->andReturn('/custom-panel/login');

    $this->get($signedUrl)
        ->assertRedirect('/custom-panel/login');
});

it('unauthenticated user is redirected to the filament panel login page', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'team_id'    => $this->team->id,
        'invited_by' => $this->inviter->id,
        'email'      => 'invitee@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $signedUrl = URL::signedRoute('filateams.invitations.accept', ['code' => $invitation->code]);

    Filament::shouldReceive('getLoginUrl')->once()->andReturn('/custom-panel/login');

    $this->get($signedUrl)
        ->assertRedirect('/custom-panel/login');
});

it('user cannot accept invitation with an expired signed url', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'team_id'    => $this->team->id,
        'invited_by' => $this->inviter->id,
        'email'      => 'invitee@example.com',
    ]);

    $expiredUrl = URL::temporarySignedRoute(
        'filateams.invitations.accept',
        now()->subMinute(),
        ['code' => $invitation->code]
    );

    $this->get($expiredUrl)->assertStatus(403);
});
