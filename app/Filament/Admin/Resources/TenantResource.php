<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TenantResource\Pages;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\TenantUsersRelationManager;
use App\Models\SubscriptionPlan;
use App\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tenant Information')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Tenant ID')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?Tenant $record) => $record !== null),
                        Forms\Components\TextInput::make('tenancy_db_name')
                            ->label('Database Name')
                            ->required(),
                        Forms\Components\TextInput::make('tenancy_email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Subscription')
                    ->schema([
                        Forms\Components\Select::make('subscription_plan_id')
                            ->label('Subscription Plan')
                            ->relationship('subscriptionPlan', 'name')
                            ->options(SubscriptionPlan::active()->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends At')
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Domains')
                    ->schema([
                        Forms\Components\Repeater::make('domains')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('domain')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Domain')
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancy_email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domains.domain')
                    ->label('Domain')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('subscriptionPlan.name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Basic' => 'gray',
                        'Pro' => 'primary',
                        'Enterprise' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Trial Ends')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('No trial'),
                Tables\Columns\TextColumn::make('usage.product_count')
                    ->label('Products')
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage.user_count')
                    ->label('Users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_plan_id')
                    ->label('Plan')
                    ->relationship('subscriptionPlan', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\Filter::make('on_trial')
                    ->label('On Trial')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('applyPlan')
                    ->label('Apply Plan')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        Forms\Components\Select::make('subscription_plan_id')
                            ->label('Subscription Plan')
                            ->options(fn () => SubscriptionPlan::active()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('trial_ends_at')
                            ->label('Trial Ends At')
                            ->helperText('Leave empty to start immediately')
                            ->native(false),
                    ])
                    ->action(function (Tenant $record, array $data) {
                        $plan = SubscriptionPlan::find($data['subscription_plan_id']);
                        if (!$plan) {
                            Notification::make()
                                ->title('Subscription plan not found')
                                ->danger()
                                ->send();
                            return;
                        }

                        $onTrial = filled($data['trial_ends_at']);
                        $startsAt = $onTrial ? null : now();
                        $expiresAt = $onTrial ? null : match ($plan->interval) {
                            'year' => now()->addYear(),
                            default => now()->addMonth(),
                        };

                        $record->update([
                            'subscription_plan_id' => $plan->id,
                            'is_active' => true,
                            'trial_ends_at' => $data['trial_ends_at'] ?? null,
                            'subscription_started_at' => $startsAt,
                            'subscription_expires_at' => $expiresAt,
                        ]);
                    }),
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record) => $record->is_active)
                    ->action(fn (Tenant $record) => $record->update(['is_active' => false])),
                Tables\Actions\Action::make('activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record) => !$record->is_active)
                    ->action(fn (Tenant $record) => $record->update(['is_active' => true])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TenantUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
            'view' => Pages\ViewTenant::route('/{record}'),
        ];
    }
}
