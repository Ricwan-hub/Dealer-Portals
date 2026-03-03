<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $recordTitleAttribute = 'sku';
    protected static ?string $slug = 'manage/parts';
    protected static ?string $navigationGroup = 'Parts Management';
    protected static ?string $navigationLabel = 'All Parts';
    protected static ?string $title = 'Parts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->label('Part Number')
                    ->required()
                    ->unique(Product::class, 'sku', ignoreRecord: true),
                Forms\Components\TextInput::make('description')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                    ->required(),
                Forms\Components\TextInput::make('qty')
                    ->label('Quantity')
                    ->numeric()
                    ->rules(['integer', 'min:0'])
                    ->required(),
                Forms\Components\Toggle::make('published')
                    ->label('Visible')
                    ->helperText('This product will be hidden to users')
                    ->default(true),
                    
                                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(fn (Product $record) => match ($record->qty) {
                0 => 'text-gray-400 opacity-50',
                default => null,
            })
            ->emptyStateDescription('When you add products, they will appear here.')
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->weight(FontWeight::SemiBold)
                    ->label('Part Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->weight(FontWeight::SemiBold)
                    ->money('KES')
                    ->sortable(),

                Tables\Columns\TextColumn::make('qty')
                    ->weight(FontWeight::SemiBold)
                    ->sortable(),

                Tables\Columns\IconColumn::make('published')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->weight(FontWeight::SemiBold)
                    ->date('d/m/y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('created_at')
                    ->weight(FontWeight::SemiBold)
                    ->date('d/m/y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                
            ])
            ->filters([
                Filter::make('in_stock')
                    ->query(fn (Builder $query): Builder => $query->where('qty', '>', 0)),
                Filter::make('out_of_stock')
                    ->query(fn (Builder $query): Builder => $query->where('qty', '=', 0)),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description', 'sku'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }
}
