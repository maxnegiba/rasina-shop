<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Actions\ProductBulkActions;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use RalphJSmit\Filament\SEO\SEO;

class ProductResource extends Resource
{
    use Translatable;

    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Catalog de Artă';
    protected static ?string $modelLabel = 'Produs / Operă';
    protected static ?string $pluralModelLabel = 'Galerie Produse';
    protected static ?int $navigationSort = 2;

    public static function productTypeOptions(): array
    {
        return [
            'cruce' => 'Cruce',
            'batic_scurt' => 'Batic scurt',
            'batic_lung' => 'Batic lung',
            'buton' => 'Buton decorativ',
            'scrumiera_zimtata' => 'Scrumieră zimțată',
            'platou_rotund' => 'Platou rotund tip pizza',
            'tava_alungita' => 'Tavă / platou alungit',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Detalii Principale')->schema([
                    Forms\Components\TextInput::make('product_code')
                        ->label('Cod produs')
                        ->maxLength(32)
                        ->unique(ignoreRecord: true)
                        ->helperText('Exemple: CR-001, BS-001, BL-001, BT-001, OD-SZ-001.'),
                    Forms\Components\Select::make('product_type')
                        ->label('Tip produs')
                        ->options(self::productTypeOptions())
                        ->searchable()
                        ->native(false),
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

                Forms\Components\Section::make('Galerie Imagini')->schema([
                    Forms\Components\Repeater::make('images')
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->schema([
                            Forms\Components\FileUpload::make('image_path')
                                ->label('Imagine')
                                ->image()
                                ->directory('products')
                                ->required(),
                            Forms\Components\Toggle::make('is_featured')
                                ->label('Imagine Principală (Featured)')
                                ->default(false),
                            Forms\Components\TextInput::make('alt_text.ro')
                                ->label('Text alternativ RO')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('alt_text.en')
                                ->label('Text alternativ EN')
                                ->maxLength(255),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->createItemButtonLabel('Adaugă o nouă imagine')
                        ->columnSpanFull(),
                ]),

                Forms\Components\Section::make('SEO bilingv')->schema([
                    Forms\Components\TextInput::make('seo_translations.ro.title')
                        ->label('Titlu SEO RO')->maxLength(60),
                    Forms\Components\TextInput::make('seo_translations.en.title')
                        ->label('SEO Title EN')->maxLength(60),
                    Forms\Components\Textarea::make('seo_translations.ro.description')
                        ->label('Descriere SEO RO')->maxLength(160)->rows(3),
                    Forms\Components\Textarea::make('seo_translations.en.description')
                        ->label('SEO Description EN')->maxLength(160)->rows(3),
                    Forms\Components\TextInput::make('seo_translations.author')
                        ->label('Autor SEO')->default('MTD ART')->maxLength(100),
                    Forms\Components\TextInput::make('seo_translations.robots')
                        ->label('Robots')->default('index, follow')->maxLength(100),
                ])->columns(2)->collapsed(),
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
                        ->relationship(
                            name: 'category',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('name->ro', 'asc')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('related_post_id')
                        ->label('Articol asociat')
                        ->relationship(
                            name: 'relatedPost',
                            titleAttribute: 'title',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('title->ro', 'asc')
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),

                Forms\Components\Section::make('Comercial')->schema([
                    Forms\Components\Toggle::make('is_custom')
                        ->label('Piesă Unicat')
                        ->helperText('Piesa păstrează prețul, stocul și fluxul normal de cumpărare.')
                        ->default(false),
                    Forms\Components\TextInput::make('price')
                        ->label('Preț (RON)')
                        ->numeric()
                        ->minValue(0.01)
                        ->prefix('RON')
                        ->required(),
                    Forms\Components\TextInput::make('stock')
                        ->label('Stoc disponibil')
                        ->numeric()
                        ->minValue(0)
                        ->default(1)
                        ->required(),
                ]),

                Forms\Components\Section::make('SEO principal / fallback')
                    ->schema([SEO::make()])
                    ->collapsed(),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('product_code')->label('Cod')->searchable()->sortable(),
            TextColumn::make('name')->label('Numele Piesei')->searchable()->weight('bold'),
            TextColumn::make('product_type')
                ->label('Tip')
                ->formatStateUsing(fn (?string $state): string => self::productTypeOptions()[$state] ?? ($state ?: '—'))
                ->badge(),
            TextColumn::make('category.name')->label('Categorie')->sortable(false),
            TextColumn::make('price')->label('Preț')->money('RON')->sortable()->placeholder('La cerere'),
            IconColumn::make('is_custom')->label('Unicat')->boolean(),
            TextColumn::make('status')->label('Status')->badge()
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
            TextColumn::make('created_at')->label('Data Adăugării')->dateTime('d M Y')->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('product_code')->filters([
            Tables\Filters\SelectFilter::make('status')->options([
                'draft' => 'Ciorne',
                'published' => 'Publicate',
            ]),
            Tables\Filters\SelectFilter::make('product_type')
                ->label('Tip produs')
                ->options(self::productTypeOptions()),
            Tables\Filters\TernaryFilter::make('is_custom')
                ->label('Piesă unicat')
                ->trueLabel('Doar piese unicat')
                ->falseLabel('Doar produse standard'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make(
                ProductBulkActions::make()
            )
                ->label('Acțiuni pentru selecție')
                ->icon('heroicon-o-adjustments-horizontal'),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
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
