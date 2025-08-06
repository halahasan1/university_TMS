<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ProfileResource;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;

    protected static string $view = 'filament.pages.view-profile';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Profile'),
        ];

    }

    protected function getViewData(): array
    {
        return [
            'user' => Auth::user(),
            'profile' => Auth::user()->profile
        ];
    }
}
