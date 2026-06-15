<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Hash;
use Filament\Infolists\Infolist;


class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $slug = 'manage/users';
    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->placeholder('Enter dealer\'s full name')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('email')
                    ->placeholder('Enter dealer\'s email address')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('New Password')
                    ->helperText('Leave blank to keep current password')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                    ->nullable()
                    ->hiddenOn('create')
                    ->columnSpan('full'),
                Forms\Components\Select::make('roles')
                     ->relationship('roles', 'name', function ($query) {
                        return $query->whereIn('name', ['administrator', 'dealer']);
                    })
                    ->required()
                    ->preload()
                    ->live()
                    ->native(false)
                    ->hiddenOn('edit')
                    ->helperText(function ($get) {
                        $selectedRole = $get('roles');
                        if ($selectedRole == '2') {
                            return 'You will receive notification with the password after this';
                        } elseif ($selectedRole == '1') {
                            return 'An generated password will be emailed to this user';
                        }
                        return ;
                    })
                    ->columnSpan('full'),
                Forms\Components\Toggle::make('active')
                    ->label('Activate')
                    ->default(false)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn (User $record): string => $record->email) 
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->weight(FontWeight::SemiBold)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'gray',
                        'administrator' => 'primary',
                        'dealer' => 'info',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->weight(FontWeight::SemiBold)
                    ->label('Last Login')
                    ->sortable()
                    ->since(), 
                Tables\Columns\TextColumn::make('created_at')
                    ->weight(FontWeight::SemiBold)
                    ->label('Registered')
                    ->sortable()
                    ->datetime('m/d/y h:i'),
                Tables\Columns\IconColumn::make('email_verified')
                    ->boolean()
                    ->state(function (User $record): bool {
                        return $record->email_verified_at != null;
                    })
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('last_login_ip')
                    ->weight(FontWeight::SemiBold)
                    ->label('Last login IP')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->weight(FontWeight::SemiBold)
                    ->label('Last update')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->datetime('m/d/y h:i'), 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->modalWidth('md'),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        if ($record->hasDealerProfile){
                            $record->profile->delete();
                        }
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    Fieldset::make('Account Information')
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('email'),
                            TextEntry::make('roles.name')
                                ->label('Role'),
                            TextEntry::make('last_login_ip')
                                ->label('last login IP'),
                            TextEntry::make('last_login_at')
                                ->since(),
                            TextEntry::make('created_at')
                                ->label('Registered on')
                                ->date(),
                            TextEntry::make('updated_at')
                                ->label('Last updated')
                                ->date(),
                        ])->columns(4),

                    Fieldset::make('Business Information')
                        ->visible(fn ($record) => $record->hasDealerProfile)
                        ->relationship('profile')
                        ->schema([
                            TextEntry::make('business_name'),
                            TextEntry::make('phone_no'),
                            TextEntry::make('address_street')
                                ->label('Street address'),
                            TextEntry::make('address_city')
                                ->label('City'),
                            TextEntry::make('address_county')
                                ->label('County'),
                        ])->columns(4),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }
}
