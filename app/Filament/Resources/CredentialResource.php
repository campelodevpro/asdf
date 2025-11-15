<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CredentialResource\Pages;
use App\Filament\Resources\CredentialResource\RelationManagers;
use App\Models\Credential;
use App\Models\CredentialViewLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\{Section, TextInput, Textarea};
use Filament\Infolists\Components\{Section as InfolistSection, TextEntry};
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\{Alignment, FontFamily, FontWeight, MaxWidth};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CredentialResource extends Resource
{
    protected static ?string $model = Credential::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados principais')
                ->schema([
                    TextInput::make('name')
                        ->label('Nome da credencial')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('system')
                        ->label('Sistema / Plataforma')
                        ->maxLength(255),

                    TextInput::make('url')
                        ->label('URL de acesso')
                        ->url()
                        ->maxLength(255),

                    TextInput::make('username')
                        ->label('Usuário / Login')
                        ->maxLength(255),
                ])->columns(2),

            Section::make('Segurança')
                ->schema([
                    TextInput::make('password')
                        ->label('Senha')
                        ->password()
                        ->revealable() // Filament 3: mostra/esconde
                        ->required()
                        // IMPORTANTÍSSIMO: não preencher automaticamente na edição
                        ->dehydrateStateUsing(fn ($state, $record) =>
                            filled($state)
                                ? $state // se o usuário digitou algo, criptografa via mutator
                                : ($record?->password ?? $state) // mantém a atual se vazio
                        )
                        ->dehydrated(fn ($state) => filled($state)), // só manda pro model se não vazio
                ]),

               

            Section::make('Observações')
                ->schema([
                    Textarea::make('notes')
                        ->label('Observações')
                        ->rows(3),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
           ->columns([
            TextColumn::make('name')
                ->label('Credencial')
                ->searchable()
                ->sortable(),

            TextColumn::make('system')
                ->label('Sistema')
                ->searchable()
                ->sortable(),

            TextColumn::make('username')
                ->label('Usuário')
                ->searchable()
                ->toggleable(),

            TextColumn::make('url')
                ->label('URL')
                ->url(fn ($record) => $record->url, true)
                ->limit(30)
                ->toggleable(),

            TextColumn::make('created_at')
                ->label('Criado em')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Ex: filtro por sistema
            SelectFilter::make('system')
                ->label('Sistema')
                ->options(
                    Credential::query()
                        ->select('system')
                        ->whereNotNull('system')
                        ->distinct()
                        ->pluck('system', 'system')
                        ->toArray()
                ),
        ])
        ->actions([
            Tables\Actions\ViewAction::make()
                ->modalHeading(fn (Credential $record): string => "Senha da credencial \"{$record->name}\"")
                ->modalDescription('Clique ou use o botão de copiar para obter rapidamente a senha.')
                ->modalIcon('heroicon-o-key')
                ->modalIconColor('primary')
                ->modalAlignment(Alignment::Center)
                ->modalWidth(MaxWidth::Small)
                ->modalCancelActionLabel('Fechar')
                ->mountUsing(function (?ComponentContainer $form, Credential $record): void {
                    CredentialViewLog::create([
                        'credential_id' => $record->id,
                        'user_id' => Auth::id(),
                        'ip_address' => request()->ip(),
                        'user_agent' => Str::limit((string) request()->userAgent(), 1024),
                        'request_path' => request()->path(),
                        'meta' => array_filter([
                            'referer' => request()->headers->get('referer'),
                            'session_id' => optional(request()->session())->getId(),
                        ]),
                    ]);
                })
                ->infolist(fn (Infolist $infolist): Infolist => $infolist
                    ->schema([
                        InfolistSection::make()
                            ->compact()
                            ->columns(1)
                            ->extraAttributes([
                                'class' => 'bg-gray-50/90 dark:bg-gray-900/40 rounded-2xl px-5 py-4 shadow-sm border border-gray-100 dark:border-gray-800',
                            ])
                            ->schema([
                                TextEntry::make('password')
                                    ->label('Senha')
                                    ->state(fn (Credential $record): ?string => $record->password)
                                    ->placeholder('Nenhuma senha cadastrada')
                                    ->copyable(fn (?string $state): bool => filled($state))
                                    ->copyMessage('Senha copiada!')
                                    ->copyMessageDuration(1500)
                                    ->fontFamily(FontFamily::Mono)
                                    ->weight(FontWeight::SemiBold)
                                    ->size(TextEntrySize::Large)
                                    ->icon('heroicon-o-lock-closed')
                                    ->iconColor('primary')
                                    ->helperText('Valor protegido – copie apenas quando necessário.')
                                    ->extraAttributes([
                                        'class' => 'text-xl tracking-wide text-gray-900 dark:text-gray-50 flex items-center gap-3',
                                    ]),
                            ]),
                    ])
                ),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCredentials::route('/'),
            'create' => Pages\CreateCredential::route('/create'),
            'edit' => Pages\EditCredential::route('/{record}/edit'),
        ];
    }
}
