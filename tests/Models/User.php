<?php

declare(strict_types=1);

namespace Tests\Models;

use Tests\Database\Factories\UserFactory;
use LaravelDaily\FilaTeams\Concerns\HasTeams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use HasTeams;

    protected $fillable = ['name', 'email', 'password'];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
