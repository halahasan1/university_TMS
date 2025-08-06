<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Models\Task;
use Filament\Actions;
use App\Filament\Resources\TaskResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;


    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if (auth()->user()->hasRole('super_admin')) {
            return $query;
        }

        return $query->where('created_by', auth()->id());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


}
