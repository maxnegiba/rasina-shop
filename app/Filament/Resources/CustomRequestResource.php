?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Vânzări & Ofertare';
    protected static ?string $modelLabel = 'Comandă';
    protected static ?string $pluralModelLabel = 'Comenzi Standard';
    
    // Notificare vizuală pentru comenzile care așteaptă să fie expediate
    protected static ?string $navigationBadgeTooltip = 'Comenzi de expediat';

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
                        // Folosim "dot notation" pentru a citi din coloana JSON customer_details
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
                            ->helperText('Folosește acest ID în contul tău Stripe pentru rambursări (refunds).')
                            ->disabled(),
                    ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Gestiune Livrare')->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Număr Comandă')
                            ->disabled()
                            ->dehydrated(false), // Să nu încerce să-l salveze accidental

                        Forms\Components\Select::make('payment_status')
                            ->label('Status Plată')
                            ->options([
                                'pending' => 'În așteptare (Neplătit)',
                                'paid' => 'Plătit (Confirmat)',
                                'failed' => 'Plată Eșuată',
                            ])
                            ->disabled() // Doar Stripe schimbă asta via Webhook
                            ->native(false),

                        Forms\Components\Select::make('shipping_status')
                            ->label('Status Livrare')
                            ->options([
                                'processing' => 'În Procesare (Pregătire colet)',
                                'shipped' => 'Expediat (Predat curierului)',
                                'delivered' => 'Livrat',
                            ])
                            ->native(false)
                            ->required(),
                    ]),

                    Forms\Components\Section::make('Facturare Oblio')->schema([
                        Forms\Components\TextInput::make('invoice_series')
                            ->label('Serie Factură')
                            ->disabled(),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Număr Factură')
                            ->disabled(),
                        
                        // Un Placeholder (un mesaj vizual) care îți amintește că factura e automată
                        Forms\Components\Placeholder::make('oblio_info')
                            ->label('Sistem Automatizat')
                            ->content('Factura este generată și trimisă automat prin API-ul Oblio în momentul confirmării plății.'),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_number')
                    ->label('Nr. Comandă')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_details.name')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('RON')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Plată')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Neplătită',
                        'paid' => 'Plătită',
                        'failed' => 'Eșuată',
                        default => $state,
                    }),

                Tables\Columns\BadgeColumn::make('shipping_status')
                    ->label('Livrare')
                    ->colors([
                        'warning' => 'processing',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                    ])
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
                    ->label('Filtrează după Plată')
                    ->options([
                        'pending' => 'Neplătită',
                        'paid' => 'Plătită',
                    ]),
                Tables\Filters\SelectFilter::make('shipping_status')
                    ->label('Filtrează după Livrare')
                    ->options([
                        'processing' => 'În Pregătire',
                        'shipped' => 'Expediată',
                        'delivered' => 'Livrată',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Gestionează'),
                
                // Buton custom pe care îl vom lega mai târziu de PDF-ul din Oblio
                Tables\Actions\Action::make('download_invoice')
                    ->label('Factură')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Order $record): string => '#') // Aici vom pune linkul către PDF-ul generat
                    ->openUrlInNewTab()
                    ->visible(fn (Order $record): bool => $record->payment_status === 'paid'), // Apare doar dacă e plătită
            ])
            ->bulkActions([
                // Fără ștergere în masă la comenzi, pentru siguranța datelor contabile!
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Aici vom putea adăuga un RelationManager pentru a vedea exact ce produse a cumpărat clientul în comanda respectivă
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            // Nu avem ruta 'create' pentru că tu nu creezi comenzi manual din panou. Clienții le creează de pe site.
        ];
    }
}
