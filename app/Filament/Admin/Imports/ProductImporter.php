<?php

namespace App\Filament\Admin\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('sku')
                ->label('Part Number')
                ->requiredMapping()
                ->rules(['required'])
                ->guess(['No.'])
                ->fillRecordUsing(function (Product $record, string $state): void {
                    $record->sku = trim(strtoupper($state));
                })
                ->example('ABC123'),
            ImportColumn::make('description')
                ->requiredMapping()
                ->rules(['required'])
                ->guess(['Description',])
                ->fillRecordUsing(function (Product $record, string $state): void {
                    $record->description = trim(strtoupper($state));
                })
                ->example('Gasket'),
            ImportColumn::make('qty')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0'])
                ->guess(['Inventory',])
                ->castStateUsing(function (string $state): ?int {
                    if (blank($state)) {
                        return null;
                    }
                    
                    $state = preg_replace('/[^0-9.]/', '', $state);
                    $state = intval($state);
                
                    return $state;
                })
                ->example('100'),
            ImportColumn::make('price')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0'])
                ->guess(['Unit Price',])
                ->castStateUsing(function (string $state): ?float {
                    if (blank($state)) {
                        return null;
                    }
                    
                    $state = preg_replace('/[^0-9.]/', '', $state);
                    $state = floatVal($state);
                
                    return round($state, precision: 0);
                })
                ->example('1800'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        return Product::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'sku' => $this->data['sku'],
        ]);

        return new Product();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
