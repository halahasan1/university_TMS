<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentReviewResource\Pages;
use App\Models\StudentReview;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StudentReviewResource extends Resource
{
    protected static ?string $model = StudentReview::class;

    protected static ?string $navigationGroup = 'Courses & Exams';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Student Reviews';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super_admin',
            'professor',
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('professor')) {
            return $query->whereHas('course', function (Builder $courseQuery) use ($user) {
                $courseQuery->where('owner_id', $user->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $isSuperAdmin = Auth::user()?->hasRole('super_admin');

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),

                TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),

                TextColumn::make('course.name')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('review_text')
                    ->label('Review')
                    ->limit(70)
                    ->wrap()
                    ->searchable()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),

                BadgeColumn::make('predicted_label')
                    ->label('Sentiment')
                    ->colors([
                        'success' => 'positive',
                        'danger' => 'negative',
                        'warning' => 'neutral',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'positive' => 'Positive',
                            'negative' => 'Negative',
                            'neutral' => 'Neutral',
                            default => ucfirst((string) $state),
                        };
                    }),

                TextColumn::make('confidence_score')
                    ->label('Confidence')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return '-';
                        }

                        return round($state * 100, 2) . '%';
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('predicted_label')
                    ->label('Sentiment')
                    ->options([
                        'positive' => 'Positive',
                        'negative' => 'Negative',
                        'neutral' => 'Neutral',
                    ]),

                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->hasRole('super_admin')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentReviews::route('/'),
            'edit' => Pages\EditStudentReview::route('/{record}/edit'),
        ];
    }
}
