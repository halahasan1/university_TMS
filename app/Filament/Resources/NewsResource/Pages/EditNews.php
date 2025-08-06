<?php

namespace App\Filament\Resources\NewsResource\Pages;

use Closure;
use App\Models\News;
use Filament\Actions;
use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


}
