<?php

namespace App\Filament\Resources\CredentialViewLogResource\Pages;

use App\Filament\Resources\CredentialViewLogResource;
use Filament\Resources\Pages\ListRecords;

class ListCredentialViewLogs extends ListRecords
{
    protected static string $resource = CredentialViewLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
