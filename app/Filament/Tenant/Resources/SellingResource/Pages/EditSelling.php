<?php

namespace App\Filament\Tenant\Resources\SellingResource\Pages;

use App\Filament\Tenant\Resources\SellingResource;
use App\Models\Tenants\Selling;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditSelling extends EditRecord
{
    protected static string $resource = SellingResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Edit') . ' ' . $this->getRecord()->code;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Selling Information'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(__('Code'))
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('date')
                            ->label(__('Date'))
                            ->required(),
                        Forms\Components\Select::make('member_id')
                            ->label(__('Member'))
                            ->relationship('member', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('customer_number')
                            ->label(__('Customer number'))
                            ->maxLength(255),
                        Forms\Components\Select::make('payment_method_id')
                            ->label(__('Payment method'))
                            ->relationship('paymentMethod', 'name')
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label(__('Note'))
                            ->rows(3)
                            ->maxLength(65535),
                    ])
                    ->columns(2),
                Forms\Components\Section::make(__('Payment Information'))
                    ->schema([
                        Forms\Components\TextInput::make('total_price')
                            ->label(__('Total price'))
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('tax_price')
                            ->label(__('Tax price'))
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('discount_price')
                            ->label(__('Discount price'))
                            ->numeric(),
                        Forms\Components\TextInput::make('payed_money')
                            ->label(__('Payed money'))
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('money_changes')
                            ->label(__('Money changes'))
                            ->numeric()
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading(__('Delete Selling'))
                ->modalDescription(__('Are you sure you want to delete this selling record? This action cannot be undone.'))
                ->action(function (Selling $record) {
                    // Delete all selling details first
                    $record->sellingDetails()->delete();
                    // Delete the selling record
                    $record->delete();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return SellingResource::getUrl('view', ['record' => $this->record]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate money changes
        $data['money_changes'] = $data['payed_money'] - ($this->record->total_price - $this->record->tax_price - $data['discount_price']);
        
        return $data;
    }
}
