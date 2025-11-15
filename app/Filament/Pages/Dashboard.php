<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CredentialStatsOverview;
use App\Filament\Widgets\CredentialsPerMonthChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Visão geral';

    protected static ?string $title = 'Painel de controle';

    public function getHeaderWidgets(): array
    {
        return [
            CredentialStatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            CredentialsPerMonthChart::class,
        ];
    }
}
