<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\CurrencyResource\Pages;
use App\Models\Tenants\Currency;
use App\Models\Tenants\Setting;
use App\Traits\HasTranslatableResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class CurrencyResource extends Resource
{
    use HasTranslatableResource;

    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Currencies';

    public static function getBreadcrumb(): string
    {
        return __('Currencies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(__('Currency Code'))
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->placeholder('USD')
                            ->helperText(__('ISO 4217 currency code (e.g., USD, EUR, IDR)')),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Currency Name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('US Dollar'),
                        Forms\Components\TextInput::make('symbol')
                            ->label(__('Symbol'))
                            ->required()
                            ->maxLength(10)
                            ->placeholder('$'),
                        Forms\Components\Select::make('symbol_position')
                            ->label(__('Symbol Position'))
                            ->options([
                                'before' => __('Before amount (e.g., $100)'),
                                'after' => __('After amount (e.g., 100$)'),
                            ])
                            ->default('before')
                            ->required(),
                        Forms\Components\TextInput::make('decimal_places')
                            ->label(__('Decimal Places'))
                            ->numeric()
                            ->default(2)
                            ->minValue(0)
                            ->maxValue(4)
                            ->required()
                            ->helperText(__('Number of decimal places to display')),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')
                            ->label(__('Default Currency'))
                            ->helperText(__('Set this as the default currency for the system'))
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label(__('Symbol'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol_position')
                    ->label(__('Position'))
                    ->formatStateUsing(fn (string $state): string => $state === 'before' ? __('Before') : __('After'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('decimal_places')
                    ->label(__('Decimals'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('Default'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active'))
                    ->placeholder(__('All'))
                    ->trueLabel(__('Active only'))
                    ->falseLabel(__('Inactive only')),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label(__('Default'))
                    ->placeholder(__('All'))
                    ->trueLabel(__('Default only'))
                    ->falseLabel(__('Non-default only')),
            ])
            ->actions([
                Tables\Actions\Action::make('set_default')
                    ->label(__('Set as Default'))
                    ->icon('heroicon-o-star')
                    ->visible(fn (Currency $record) => !$record->is_default)
                    ->requiresConfirmation()
                    ->action(function (Currency $record) {
                        $record->setAsDefault();
                        // Update the setting
                        Setting::set('currency', $record->code);
                        Notification::make()
                            ->title(__('Currency set as default'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Currency $record) {
                        if ($record->is_default) {
                            Notification::make()
                                ->title(__('Cannot delete default currency'))
                                ->danger()
                                ->send();
                            return false;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
