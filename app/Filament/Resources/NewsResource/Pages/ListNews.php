<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Models\News;
use Filament\Actions\CreateAction;
use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


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


    protected function getTableQuery(): Builder
    {
        $user = auth()->user();
        $departmentId = $user->profile?->department_id;

        return News::query()
            ->where(function ($query) use ($departmentId) {
                $query->where('audience_type', 'global')
                      ->orWhere(function ($query) use ($departmentId) {
                          $query->where('audience_type', 'department_only')
                                ->whereHas('user.profile', function ($q) use ($departmentId) {
                                    $q->where('department_id', $departmentId);
                                });
                      });
            });
    }
}
