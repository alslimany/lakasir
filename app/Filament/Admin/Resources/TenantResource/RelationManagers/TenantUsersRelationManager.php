<?php

namespace App\Filament\Admin\Resources\TenantResource\RelationManagers;

use App\TenantUser as TenantUserModel;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;

class TenantUsersRelationManager extends RelationManager
{
    // Tenant users are stored in the tenant database. We use the central
    // `App\TenantUser` proxy (which maps to the `users` table) and run
    // all DB ops inside `tenant()->run(...)` so the operations target the
    // tenant database.
    protected static string $relationship = 'users';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                    ->required(fn (?TenantUserModel $record) => $record === null)
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Build and return a tenant builder. If tenancy is already
                // initialized, use the helper; otherwise initialize tenancy
                // for this tenant so the returned builder uses the tenant DB.
                $tenant = $this->getOwnerRecord();

                // If tenant() helper returns an active tenant, run in its context.
                if (function_exists('tenant') && tenant()) {
                    return tenant()->run(fn () => TenantUserModel::query());
                }

                // Otherwise initialize tenancy for this tenant and return
                // a builder bound to the tenant connection. We deliberately
                // do not call tenancy()->end() here because Filament will
                // continue to use the builder for the current request.
                try {
                    tenancy()->initialize($tenant->id);
                    return TenantUserModel::query();
                } catch (\Throwable $e) {
                    // If initialization fails, return an empty builder.
                    return TenantUserModel::query()->whereRaw('0 = 1');
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime()->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create')
                    ->modalHeading('Create Tenant User')
                    ->modalButton('Create')
                    ->authorize(fn () => true)
                    ->action(function (array $data) {
                        $tenant = $this->getOwnerRecord();
                        try {
                            tenancy()->initialize($tenant->id);
                            return TenantUserModel::create([
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'password' => $data['password'] ? Hash::make($data['password']) : null,
                            ]);
                        } finally {
                            try { tenancy()->end(); } catch (\Throwable $_) { }
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')->password()->required()->maxLength(255),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Tenant User')
                    ->modalButton('Save')
                    ->authorize(fn () => true)
                    ->action(function ($record, array $data) {
                        $tenant = $this->getOwnerRecord();
                        try {
                            tenancy()->initialize($tenant->id);
                            $model = TenantUserModel::query()->find($record->getKey());
                            if ($model) {
                                if (isset($data['password']) && $data['password']) {
                                    $data['password'] = Hash::make($data['password']);
                                } else {
                                    unset($data['password']);
                                }
                                $model->update($data);
                            }
                            return $model;
                        } finally {
                            try { tenancy()->end(); } catch (\Throwable $_) { }
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')->password()->required(false)->maxLength(255),
                    ]),
                Tables\Actions\DeleteAction::make()->action(function ($record) {
                    $tenant = $this->getOwnerRecord();
                    try {
                        tenancy()->initialize($tenant->id);
                        return TenantUserModel::whereKey($record->getKey())->delete();
                    } finally {
                        try { tenancy()->end(); } catch (\Throwable $_) { }
                    }
                })->authorize(fn () => true),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->action(function ($records) {
                    $tenant = $this->getOwnerRecord();
                    try {
                        tenancy()->initialize($tenant->id);
                        $ids = collect($records)->pluck('id')->toArray();
                        return TenantUserModel::whereIn('id', $ids)->delete();
                    } finally {
                        try { tenancy()->end(); } catch (\Throwable $_) { }
                    }
                })->authorize(fn () => true),
            ]);
    }
}

