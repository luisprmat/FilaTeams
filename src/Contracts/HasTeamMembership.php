<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Contracts;

use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;

interface HasTeamMembership extends FilamentUser, HasTenants {}
