<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $assignedUser = User::with('profile')->find($data['assigned_to'] ?? null);
        $data['department_id'] = $assignedUser?->profile?->department_id;

        if (($data['status'] ?? null) === 'in_progress' && empty($this->record->started_at)) {
            $data['started_at'] = now();
        }

        if (($data['status'] ?? null) === 'completed' && empty($this->record->completed_at)) {
            $data['completed_at'] = now();
        } elseif (($data['status'] ?? null) !== 'completed') {
            $data['completed_at'] = null;
        }

        return $data;
    }
}
