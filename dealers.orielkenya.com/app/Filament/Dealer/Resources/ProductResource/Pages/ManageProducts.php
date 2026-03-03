<?php

namespace App\Filament\Dealer\Resources\ProductResource\Pages;

use App\Filament\Dealer\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
