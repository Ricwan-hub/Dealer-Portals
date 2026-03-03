<?php

namespace App\Filament\Dealer\Resources;

use App\Filament\Dealer\Resources\ProductResource\Pages;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'parts';

    protected static ?string $navigationLabel = 'Parts';

    protected ?string $title = 'Vehicle Parts';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(fn (Product $record) => match ($record->qty) {
                0 => 'opacity-30 text-gray-800',
                default => null,
            })
            ->emptyStateDescription('There are no products listed')
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                ->label('Part Number')
                ->weight(FontWeight::SemiBold)
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
                Tables\Columns\TextColumn::make('price_inc_vat')
                    ->label('Price VAT')
                    ->weight(FontWeight::SemiBold)
                    ->state(function (Product $record): float {
                        return $record->price * 1.16;
                    })
                    ->money('KES')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->weight(FontWeight::SemiBold)
                    ->sortable(),
            ])
            ->filters([
                Filter::make('in_stock')
                    ->query(fn (Builder $query): Builder => $query->where('qty', '>', 0))
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }
}
