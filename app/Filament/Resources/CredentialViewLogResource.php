<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CredentialViewLogResource\Pages;
use App\Models\CredentialViewLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CredentialViewLogResource extends Resource
{
    protected static ?string $model = CredentialViewLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Auditoria';

    protected static ?string $navigationLabel = 'Logs de acesso';

    protected static ?int $navigationSort = 90;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('credential.name')
                    ->label('Credencial')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sistema'),
                TextColumn::make('user.email')
                    ->label('E-mail')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('request_path')
                    ->label('Rota')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(40),
                TextColumn::make('event')
                    ->label('Evento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'password_view' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Visualizado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->description(fn (CredentialViewLog $record) => $record->user_agent ? Str::limit($record->user_agent, 60) : null),
            ])
            ->filters([
                SelectFilter::make('credential_id')
                    ->relationship('credential', 'name')
                    ->label('Credencial'),
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Usuário'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCredentialViewLogs::route('/'),
        ];
    }
}
