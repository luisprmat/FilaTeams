<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use LaravelDaily\FilaTeams\Enums\TeamRole;

class Membership extends Pivot
{
    public $incrementing = true;

    protected $table = 'team_members';

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'));
    }

    protected function casts(): array
    {
        return [
            'role' => TeamRole::class,
        ];
    }
}
