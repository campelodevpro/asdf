<?php

namespace App\Filament\Widgets;

use App\Models\Credential;
use Filament\Widgets\ChartWidget;

class CredentialsPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Credenciais por mês';

    protected static ?string $pollingInterval = '120s';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        $counts = Credential::query()
            ->where('created_at', '>=', $start)
            ->get(['id', 'created_at'])
            ->groupBy(fn ($record) => $record->created_at->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $labels = [];
        $data = [];

        for ($i = 0; $i < 12; $i++) {
            $month = (clone $start)->addMonths($i);
            $key = $month->format('Y-m');

            $labels[] = $month->locale(app()->getLocale())->translatedFormat('M/Y');
            $data[] = $counts[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Credenciais criadas',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.20)', // amber 500
                    'borderColor' => '#f59e0b',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
