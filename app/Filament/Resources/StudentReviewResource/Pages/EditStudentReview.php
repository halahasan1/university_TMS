<?php

namespace App\Filament\Resources\StudentReviewResource\Pages;

use App\Filament\Resources\StudentReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentReview extends EditRecord
{
    protected static string $resource = StudentReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
