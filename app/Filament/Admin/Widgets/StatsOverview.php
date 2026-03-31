<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Product;

class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        
        $dealers = User::role('dealer')->count();
        $parts = Product::count();
        $noStock = Product::where('qty', 0)->count();
        
        return [
            Stat::make('Total Dealers', $dealers),
            Stat::make('Total Parts', $parts),
            Stat::make('Out of Stock Parts', $noStock),
        ];
    }
}
