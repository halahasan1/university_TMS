<?php

namespace App\Filament\Resources\StudentReviewResource\Pages;

use App\Filament\Resources\StudentReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentReviews extends ListRecords
{
    protected static string $resource = StudentReviewResource::class;

    // protected function getHeaderActions(): array
    // {
    //     // return [
    //     //     Actions\CreateAction::make(),
    //     // ];
    // }
}
