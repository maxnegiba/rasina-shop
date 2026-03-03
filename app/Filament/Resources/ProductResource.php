<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Catalog de Artă';
    protected static ?string $modelLabel = 'Produs / Operă';
    protected static ?string $pluralModelLabel = 'Galerie Produse';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Detalii Principale')->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Numele Piesei')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Prietenos (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\RichEditor::make('description')
                            ->label('Povestea / Descrierea')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Section::make('Imagini')->schema([
                        // Aici presupunem că ai un câmp image direct pe produs 
                        // sau folosești un tabel separat. Ca placeholder standard pentru Filament:
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagine Principală')
                            ->image()
                            ->directory('products')
                            ->columnSpanFull(),
                    ]),
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Organizare')->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Vizibilitate')
                            ->options([
                                'draft' => 'Ciornă (Ascuns)',
                                'published' => 'Publicat (Vizibil pe site)',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('category_id')
                            ->label('Categorie')
                            // FIX-UL MAGIC PENTRU POSTGRESQL & JSON
                            ->relationship(
                                name: 'category', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('name->ro', 'asc')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                    Forms\Components\Section::make('Comercial')->schema([
                        Forms\Components\Toggle::make('is_custom')
                            ->label('Piesă Unicat (La Comandă)')
                            ->helperText('Dacă este activat, prețul va fi înlocuit cu "Preț la cerere", iar clientul va completa un formular în loc să cumpere direct.')
                            ->default(false)
                            ->live(),

                        Forms\Components\TextInput::make('price')
                            ->label('Preț (RON)')
                            ->numeric()
                            ->prefix('RON')
                            ->hidden(fn (Forms\Get $get): bool => $get('is_custom') === true)
                            ->required(fn (Forms\Get $get): bool => $get('is_custom') === false),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stoc disponibil')
                            ->numeric()
                            ->default(1)
                            ->hidden(fn (Forms\Get $get): bool => $get('is_custom') === true)
                            ->required(fn (Forms\Get $get): bool => $get('is_custom') === false),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Numele Piesei')
                    ->searchable()
                    ->weight('bold'),

                // Dezactivăm sortarea pe categorie ca să evităm eroarea Postgres în tabel
                TextColumn::make('category.name')
                    ->label('Categorie')
                    ->sortable(false),

                TextColumn::make('price')
                    ->label('Preț')
                    ->money('RON')
                    ->sortable()
                    ->placeholder('La cerere'),

                IconColumn::make('is_custom')
                    ->label('Unicat')
                    ->boolean(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Ciornă',
                        'published' => 'Publicat',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Data Adăugării')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Ciorne',
                        'published' => 'Publicate',
                    ]),
                Tables\Filters\TernaryFilter::make('is_custom')
                    ->label('Tip Produs')
                    ->trueLabel('Doar Unicat / La Comandă')
                    ->falseLabel('Doar Standard (În Stoc)'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            // Aici vei putea adăuga RelationManager-ul pentru Galeria de Imagini (product_images)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}