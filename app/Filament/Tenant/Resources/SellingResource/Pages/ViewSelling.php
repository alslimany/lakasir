<?php

namespace App\Filament\Tenant\Resources\SellingResource\Pages;

use App\Features\PrintSellingA5;
use App\Filament\Tenant\Resources\SellingDetailResource\RelationManagers\SellingDetailsRelationManager;
use App\Filament\Tenant\Resources\SellingResource;
use App\Models\Tenants\About;
use App\Models\Tenants\Selling;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\Support\Htmlable;

class ViewSelling extends ViewRecord
{
    protected static string $resource = SellingResource::class;

    public ?About $about = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->about = About::first();
    }

    public function getTitle(): string|Htmlable
    {
        return 'View '.$this->getRecord()->code;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make(__('Print invoice'))
                ->icon('heroicon-s-printer')
                ->extraAttributes([
                    'id' => 'printInvoice',
                ])
                ->color(Color::Teal)
                ->visible(can('can print selling') && feature(PrintSellingA5::class)),
            Action::make(__('Print receipt'))
                ->icon('heroicon-s-printer')
                ->extraAttributes([
                    'id' => 'printButton',
                ])
                ->visible(can('can print selling')),
            \Filament\Actions\EditAction::make()
                ->visible(can('can edit selling'))
                ->icon('heroicon-o-pencil'),
            \Filament\Actions\DeleteAction::make()
                ->visible(can('can delete selling'))
                ->requiresConfirmation()
                ->modalHeading(__('Delete Selling'))
                ->modalDescription(__('Are you sure you want to delete this selling record? This action cannot be undone.'))
                ->before(function (Selling $record) {
                    // Delete all selling details first
                    $record->sellingDetails()->delete();
                }),
        ];
    }

    public function getView(): string
    {
        return 'filament.tenant.resources.sellings.pages.view-selling';
    }

    public function getRecord(): Selling
    {
        return $this->record->load('sellingDetails.product');
    }

    public function getRelationManagers(): array
    {
        return [
            SellingDetailsRelationManager::make(),
        ];
    }
}
