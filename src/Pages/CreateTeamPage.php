<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Pages;

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use LaravelDaily\FilaTeams\Rules\TeamName;
use LaravelDaily\FilaTeams\Actions\CreateTeam;

class CreateTeamPage extends RegisterTenant
{
    protected static ?string $slug = 'new';

    public static function getLabel(): string
    {
        return 'Create Team';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Team Name')
                    ->required()
                    ->maxLength(255)
                    ->rules([new TeamName])
                    ->autofocus(),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $action = new CreateTeam;

        return $action(auth()->user(), $data);
    }
}
