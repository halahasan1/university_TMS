<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\ViewRecord;

class ViewNews extends ViewRecord
{
    protected static string $resource = NewsResource::class;
    protected static string $view = 'filament.resources.news-resource.view-news';

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }
    public function getViewData(): array
    {
        return [
            'news' => $this->record,
        ];
    }
}
