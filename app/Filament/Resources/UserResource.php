<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'User';
    protected static ?string $navigationLabel = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view-users');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Credentials')
                    ->visible(fn (): bool => auth()->user()->hasRole('super_admin'))
                    ->schema([
                        TextInput::make('name')
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->required(),

                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                    ]),

                Section::make('Roles')
                    ->visible(fn (): bool => auth()->user()->hasRole('super_admin'))
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                    ]),


            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('profile.image_path')
                ->label('image')
                ->formatStateUsing(function ($state) {
                    $url = $state
                        ? asset('storage/' . $state)
                        : asset('images/default-avatar.jpg');

                    return "<img src='{$url}' style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover;' />";
                })
                ->html(),


                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable(),

                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Verified Users Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }




    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' =>CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function afterCreate(Forms\Contracts\HasForms $livewire, Model $record): void
    {
        $record->sendPasswordResetNotification(
            \Illuminate\Support\Facades\Password::broker()->createToken($record)
        );
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('profile');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view-users');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('manage-users');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('manage-users');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('manage-users');
    }


}
