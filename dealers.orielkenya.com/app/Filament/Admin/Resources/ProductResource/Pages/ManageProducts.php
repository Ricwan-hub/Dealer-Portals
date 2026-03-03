<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Filament\Admin\Imports\ProductImporter;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('flush')
                ->label('Flush Products')
                ->modalHeading('Remove All Products')
                ->color('danger')
                ->requiresConfirmation()
                ->disabled(fn () => Product::count() === 0)
                ->action(function () {
                    $deleteAll = Product::truncate();
                    if($deleteAll) {
                        Notification::make()
                        ->success()
                        ->body('All products have been deleted successfully')
                        ->send();
                    }
                }),
            Actions\ImportAction::make()
                ->importer(ProductImporter::class)
                ->maxRows(10000),
            Actions\CreateAction::make(),
        ];
    }
}
