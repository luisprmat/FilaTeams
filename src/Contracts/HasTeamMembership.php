<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Contracts;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;

interface HasTeamMembership extends FilamentUser, HasTenants {}
