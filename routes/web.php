<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LaravelDaily\FilaTeams\Http\Controllers\AcceptInvitationController;

Route::get('/team-invitations/{code}/accept', AcceptInvitationController::class)
    ->middleware(['web', 'signed'])
    ->name('filateams.invitations.accept');
