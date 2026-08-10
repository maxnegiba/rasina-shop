<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomRequestResource\Pages;
use App\Models\CustomRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomRequestResource extends Resource
{
    protected static ?string $model = CustomRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Vânzări & Ofertare';

    protected static ?string $modelLabel = 'Cerere personalizată';

    protected static ?string $pluralModelLabel = 'Cereri personalizate';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = CustomRequest::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client și solicitare')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Produs de referință')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nume complet')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('dimensions_requested')
                            ->label('Dimensiuni dorite')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('color_preferences')
                            ->label('Preferințe de culoare / rășină')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('special_message')
                            ->label('Mesaj / detalii')
                            ->rows(6)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('reference_image_path')
                            ->label('Imagine de referință')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->disk('local')
                            ->visibility('private')
                            ->directory('custom_requests')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make('Ofertare')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(self::statusOptions())
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('quoted_price')
                            ->label('Preț ofertat')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('RON'),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Primită la')
                    ->dateTime('d.m.Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Client')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produs de referință')
                    ->placeholder('Cerere generală')
                    ->limit(35),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'in_discussion' => 'warning',
                        'quote_sent' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quoted_price')
                    ->label('Ofertă')
                    ->money('RON')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::statusOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Gestionează'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomRequests::route('/'),
            'create' => Pages\CreateCustomRequest::route('/create'),
            'edit' => Pages\EditCustomRequest::route('/{record}/edit'),
        ];
    }

    private static function statusOptions(): array
    {
        return [
            'new' => 'Nouă',
            'in_discussion' => 'În discuție',
            'quote_sent' => 'Ofertă trimisă',
            'accepted' => 'Acceptată',
            'rejected' => 'Respinsă',
        ];
    }
}
