<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Vânzări & Ofertare';
    protected static ?string $modelLabel = 'Comandă';
    protected static ?string $pluralModelLabel = 'Comenzi Standard';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('payment_status', 'paid')
            ->where('shipping_status', 'processing')
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
                        Forms\Components\Textarea::make('customer_details.address')
                            ->label('Adresa de Livrare')
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(3),

                    Forms\Components\Section::make('Informații Plată & Stripe')->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Încasat (RON)')
                            ->numeric()
                            ->prefix('RON')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_transaction_id')
                            ->label('ID Tranzacție Stripe')
                            ->disabled(),
                    ])->columns(2),
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
                            ->native(false),
                    ]),

                    Forms\Components\Section::make('Facturare Oblio')->schema([
                        Forms\Components\TextInput::make('invoice_series')->label('Serie')->disabled(),
                        Forms\Components\TextInput::make('invoice_number')->label('Număr')->disabled(),
                        Forms\Components\Placeholder::make('oblio_info')
                            ->label('Status')
                            ->content('Facturarea se face automat prin Oblio API la confirmarea plății.'),
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
                    ->label('Total')
                    ->money('RON')
                    ->sortable(),

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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(['pending' => 'Neplătită', 'paid' => 'Plătită']),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Detalii'),
                Tables\Actions\Action::make('download_invoice')
                    ->label('Factură')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Order $record): string => '#')
                    ->visible(fn (Order $record): bool => $record->payment_status === 'paid'),
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