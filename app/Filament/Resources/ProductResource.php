<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ProductResource extends Resource
{
    // Activăm funcționalitatea bilingvă pentru această resursă
    use Translatable;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Catalog de Artă';
    protected static ?string $modelLabel = 'Produs';
    protected static ?string $pluralModelLabel = 'Produse';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Detalii Principale')->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nume Produs (RO/EN)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true) // Când ieși din câmp, generează automat slug-ul
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Prietenos (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('category_id')
                            ->label('Categorie')
                            ->relationship('category', 'name')
                            ->required(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Descriere (RO/EN)')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Section::make('Galerie Foto')->schema([
                        Forms\Components\Repeater::make('images')
                            ->relationship('images')
                            ->label('Imagini Produs')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Imagine')
                                    ->image()
                                    ->directory('products')
                                    ->required()
                                    ->columnSpan(3),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Imagine Principală')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->reorderableWithButtons(),
                    ]),
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Tip Produs & Vânzare')->schema([
                        Forms\Components\Toggle::make('is_custom')
                            ->label('Produs Unicat / Personalizat')
                            ->helperText('Dacă bifezi, butonul de "Adaugă în coș" va fi înlocuit cu formularul "Cere Ofertă".')
                            ->live() // Asta face ca interfața să reacționeze în timp real
                            ->default(false),

                        Forms\Components\TextInput::make('price')
                            ->label('Preț (RON)')
                            ->numeric()
                            ->prefix('RON')
                            ->hidden(fn (Get $get): bool => $get('is_custom') === true), // Se ascunde dacă e unicat

                        Forms\Components\TextInput::make('stock')
                            ->label('Stoc Disponibil')
                            ->numeric()
                            ->default(1)
                            ->hidden(fn (Get $get): bool => $get('is_custom') === true), // Se ascunde dacă e unicat
                    ]),

                    Forms\Components\Section::make('Stare Produs')->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Ciornă (Ascuns)',
                                'published' => 'Publicat (Vizibil pe site)',
                                'archived' => 'Arhivat',
                            ])
                            ->default('draft')
                            ->required(),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nume Produs')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categorie')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Preț')
                    ->formatStateUsing(function ($state, $record) {
                        // Dacă e produs custom, afișăm "La cerere" în loc de preț
                        return $record->is_custom ? 'La cerere' : $state . ' RON';
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_custom')
                    ->label('Unicat')
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'danger' => 'archived',
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),
            ])
            ->filters([
                // Aici vom putea adăuga filtre pe viitor
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
            //
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
