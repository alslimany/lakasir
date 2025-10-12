<?php

namespace App\Filament\Tenant\Resources\CurrencyResource\Pages;

use App\Filament\Tenant\Resources\CurrencyResource;
use App\Models\Tenants\Currency;
use App\Models\Tenants\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // If this is set as default, update the setting
        if ($this->record->is_default) {
            Setting::set('currency', $this->record->code);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If this is set as default, unset all other currencies
        if ($data['is_default'] ?? false) {
            Currency::where('id', '!=', $this->record->id)->update(['is_default' => false]);
        }
        
        return $data;
    }
}
