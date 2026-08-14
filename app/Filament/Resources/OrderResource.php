<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Vânzări & Ofertare';
    protected static ?string $modelLabel = 'Comandă';
    protected static ?string $pluralModelLabel = 'Comenzi Standard';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->whereNull('cancelled_at')
            ->where('shipping_status', 'processing')
            ->where(function (Builder $query): void {
                $query->where('payment_status', 'paid')
                    ->orWhere('payment_method', 'cash_on_delivery');
            })
            ->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Detalii Client (Facturare & Livrare)')->schema([
                        Forms\Components\TextInput::make('customer_details.name')
                            ->label('Nume Complet')
                            ->disabled(),
                        Forms\Components\TextInput::make('customer_details.email')
                            ->label('Email')
                            ->disabled(),
                        Forms\Components\TextInput::make('customer_details.phone')
                            ->label('Telefon')
                            ->disabled(),
                        Forms\Components\Placeholder::make('shipping_address')
                            ->label('Adresa de Livrare')
                            ->content(fn (?Order $record): string => collect($record?->customer_details['address'] ?? [])
                                ->filter()
                                ->implode(', ') ?: '—')
                            ->columnSpanFull(),
                    ])->columns(3),

                    Forms\Components\Section::make('Informații Plată')->schema([
                        Forms\Components\Placeholder::make('payment_method_info')
                            ->label('Metodă de plată')
                            ->content(fn (?Order $record): string => $record?->isCashOnDelivery()
                                ? 'Ramburs la curier'
                                : 'Online (Stripe)'),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total produse (RON)')
                            ->numeric()
                            ->prefix('RON')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_transaction_id')
                            ->label('ID Tranzacție Stripe')
                            ->disabled()
                            ->visible(fn (?Order $record): bool => ! $record?->isCashOnDelivery()),
                    ])->columns(3),

                    Forms\Components\Section::make('Anulare')
                        ->schema([
                            Forms\Components\Placeholder::make('cancelled_at_info')
                                ->label('Anulată la')
                                ->content(fn (?Order $record): string => $record?->cancelled_at?->format('d.m.Y H:i') ?? '—'),
                            Forms\Components\Placeholder::make('cancellation_reason_info')
                                ->label('Motiv')
                                ->content(fn (?Order $record): string => $record?->cancellation_reason ?: '—'),
                        ])
                        ->columns(2)
                        ->visible(fn (?Order $record): bool => (bool) $record?->cancelled_at),
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Gestiune Livrare')->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Număr Comandă')
                            ->disabled(),

                        Forms\Components\Select::make('payment_status')
                            ->label('Status Plată')
                            ->options([
                                'pending' => 'În așteptare',
                                'paid' => 'Plătit',
                                'failed' => 'Eșuat',
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('shipping_status')
                            ->label('Status Livrare')
                            ->options([
                                'processing' => 'În Procesare',
                                'shipped' => 'Expediat',
                                'delivered' => 'Livrat',
                            ])
                            ->required()
                            ->native(false)
                            ->disabled(fn (?Order $record): bool => (bool) $record?->cancelled_at),
                    ]),

                    Forms\Components\Section::make('Document Proforma')->schema([
                        Forms\Components\TextInput::make('proforma_number')->label('Număr Proforma')->disabled(),
                        Forms\Components\Placeholder::make('proforma_info')
                            ->label('Status')
                            ->content(fn (?Order $record): string => $record?->isCashOnDelivery()
                                ? 'Proforma nefiscală este generată la plasarea comenzii ramburs.'
                                : 'Proforma nefiscală este generată automat după confirmarea plății online.'),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d M Y')
                    ->sortable(),

                TextColumn::make('order_number')
                    ->label('Nr. Comandă')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('customer_details.name')
                    ->label('Client')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Total produse')
                    ->money('RON')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metodă')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'cash_on_delivery' ? 'warning' : 'info')
                    ->formatStateUsing(fn (string $state): string => $state === 'cash_on_delivery' ? 'Ramburs' : 'Stripe'),

                TextColumn::make('payment_status')
                    ->label('Plată')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Neplătită',
                        'paid' => 'Plătită',
                        'failed' => 'Eșuată',
                        default => $state,
                    }),

                TextColumn::make('shipping_status')
                    ->label('Livrare')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'processing' => 'În Pregătire',
                        'shipped' => 'Expediată',
                        'delivered' => 'Livrată',
                        default => $state,
                    }),

                TextColumn::make('cancelled_at')
                    ->label('Status')
                    ->badge()
                    ->placeholder('Activă')
                    ->formatStateUsing(fn ($state): string => $state ? 'Anulată' : 'Activă')
                    ->color(fn ($state): string => $state ? 'danger' : 'success'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metodă de plată')
                    ->options([
                        'stripe' => 'Online (Stripe)',
                        'cash_on_delivery' => 'Ramburs',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(['pending' => 'Neplătită', 'paid' => 'Plătită']),
                Tables\Filters\TernaryFilter::make('cancelled_at')
                    ->label('Comenzi anulate')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Detalii'),

                Tables\Actions\Action::make('download_proforma')
                    ->label('Descarcă Proforma')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->visible(fn (Order $record): bool => $record->payment_status === 'paid' || $record->isCashOnDelivery())
                    ->url(fn (Order $record): string => URL::temporarySignedRoute(
                        'order.proforma.download',
                        now()->addMinutes(15),
                        ['order' => $record->public_token],
                    ))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('mark_cod_paid')
                    ->label('Marchează încasată')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->isCashOnDelivery()
                        && $record->payment_status === 'pending'
                        && ! $record->isCancelled())
                    ->requiresConfirmation()
                    ->modalHeading('Confirmă încasarea ramburs')
                    ->modalDescription('Folosește această acțiune numai după ce plata ramburs a fost confirmată ca încasată.')
                    ->action(function (Order $record): void {
                        $record->update(['payment_status' => 'paid']);

                        Notification::make()
                            ->title('Plata ramburs a fost marcată ca încasată')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel_order')
                    ->label('Anulează')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (Order $record): bool => ! $record->isCancelled() && $record->shipping_status !== 'delivered')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Motiv anulare')
                            ->placeholder('Opțional: motivul anulării')
                            ->maxLength(1000),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Anulează comanda')
                    ->modalDescription(fn (Order $record): string => $record->payment_status === 'paid'
                        ? 'Comanda este plătită. Anularea NU efectuează automat o rambursare Stripe și nu readaugă automat produsele în stoc.'
                        : 'Comanda va fi marcată ca anulată. Dacă are stoc rezervat, acesta va fi eliberat automat.')
                    ->action(function (Order $record, array $data): void {
                        try {
                            $record->cancel($data['reason'] ?? null);

                            Notification::make()
                                ->title('Comanda a fost anulată')
                                ->success()
                                ->send();
                        } catch (\LogicException $exception) {
                            Notification::make()
                                ->title('Comanda nu poate fi anulată')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('delete_order')
                    ->label('Șterge')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (Order $record): bool => $record->payment_status !== 'paid')
                    ->requiresConfirmation()
                    ->modalHeading('Șterge definitiv comanda')
                    ->modalDescription('Această acțiune șterge definitiv comanda. Dacă există stoc rezervat, acesta va fi eliberat înainte de ștergere.')
                    ->action(function (Order $record): void {
                        try {
                            $record->deleteSafelyFromAdmin();

                            Notification::make()
                                ->title('Comanda a fost ștearsă')
                                ->success()
                                ->send();
                        } catch (\LogicException $exception) {
                            Notification::make()
                                ->title('Comanda nu poate fi ștearsă')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
