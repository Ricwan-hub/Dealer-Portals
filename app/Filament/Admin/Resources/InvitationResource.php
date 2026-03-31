<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InvitationResource\Pages;
use Illuminate\Support\Facades\Notification as EmailNotification;
use Filament\Notifications\Notification;
use App\Notifications\RegisterInviteNotification;
use App\Models\Invitation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $slug = 'user/invitations';

    protected static ?string $navigationLabel = 'Invitations';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $recordTitleAttribute = 'email';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('When you send any invite, it will appear here.')
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('accepted')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->weight(FontWeight::SemiBold)
                    ->label('Sent')
                    ->sortable()
                    ->date('d/m/y'),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->weight(FontWeight::SemiBold)
                    ->label('Joined')
                    ->sortable()
                    ->date('d/m/y'), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Resend invitation')
                ->requiresConfirmation()
                ->action(function ($record){
                    EmailNotification::route('mail', $record->email)->notify(new RegisterInviteNotification());
                    Notification::make('invitedSuccess')
                        ->body('User invitation resent')
                        ->success()
                        ->send();
                })
                ->visible(fn ($record): bool => $record->active === false)
                ->visible(fn ($record): bool => now()->diffInDays($record->created_at) > 1),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvitations::route('/'),
        ];
    }
}
