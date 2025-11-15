<?php

namespace App\Filament\Widgets;

use App\Models\Credential;
use App\Models\CredentialViewLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CredentialStatsOverview extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $credentials = Credential::query()->count();
        $logs = CredentialViewLog::query()->count();

        return [
            Stat::make('Credenciais cadastradas', number_format($credentials, 0, ',', '.'))
                ->icon('heroicon-o-key')
                ->description('Total de credenciais no cofre')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('primary'),
            Stat::make('Logs de visualização', number_format($logs, 0, ',', '.'))
                ->icon('heroicon-o-shield-exclamation')
                ->description('Eventos registrados no histórico')
                ->descriptionIcon('heroicon-o-eye')
                ->color('warning'),
        ];
    }
}
