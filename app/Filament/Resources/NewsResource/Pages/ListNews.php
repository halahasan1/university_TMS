<?php

namespace App\Filament\Resources\NewsResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\ListRecords;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create New Post')
                ->icon('heroicon-o-plus-circle')
                ->button()
                ->color('primary'),
        ];
    }
}
