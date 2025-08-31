<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Profile;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\ProfileResource\Pages\EditProfile;
use App\Filament\Resources\ProfileResource\Pages\ViewProfile;
use App\Filament\Resources\ProfileResource\Pages\ListProfiles;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $modelLabel = 'Profile';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                        ->directory('profile-images')
                        ->image()
                        ->avatar()
                        ->preserveFilenames()
                        ->maxSize(2048)
                            ,

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Info')
                    ->schema([
                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn () => !auth()->user()?->hasRole('super_admin')),

                        Forms\Components\Textarea::make('bio')
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ])
            ]);
    }



    public static function getPages(): array
    {
        return [
            'index' => ListProfiles::route('/'),
            'view' => ViewProfile::route('/{record}'),
            'edit' =>EditProfile::route('/{record}/edit'),
        ];
    }
    public static function getNavigationUrl(): string
    {
        return static::getUrl('view', [
            'record' => auth()->user()?->profile?->id,
        ]);
    }

}
