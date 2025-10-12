<?php

namespace App\Filament\Tenant\Resources\CurrencyResource\Pages;

use App\Filament\Tenant\Resources\CurrencyResource;
use App\Models\Tenants\Currency;
use App\Models\Tenants\Setting;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function afterCreate(): void
    {
        // If this is set as default, update the setting
        if ($this->record->is_default) {
            Setting::set('currency', $this->record->code);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If this is set as default, unset all other currencies
        if ($data['is_default'] ?? false) {
            Currency::where('id', '!=', null)->update(['is_default' => false]);
        }
        
        return $data;
    }
}
