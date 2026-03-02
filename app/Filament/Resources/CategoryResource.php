<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    // Activăm funcționalitatea bilingvă
    use Translatable;

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Catalog de Artă';
    protected static ?string $modelLabel = 'Categorie';
    protected static ?string $pluralModelLabel = 'Categorii';
    
    // O punem prima în meniu
    protected static ?int $navigationSort = 1; 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalii Categorie')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nume Categorie (RO/EN)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->label('URL Prietenos (Slug)')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Exemplu: blaturi-rasina-epoxidica'),

                    Forms\Components\Select::make('parent_id')
                        ->label('Categorie Părinte (Opțional)')
                        // FIX: Îi spunem lui Postgres să sorteze după cheia 'ro' din JSON
                        ->relationship(
                            name: 'parent', 
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('name->ro', 'asc')
                        )
                        ->searchable()
                        ->preload()
                        ->helperText('Dacă aceasta este o sub-categorie, alege categoria principală de aici.'),

                    Forms\Components\Textarea::make('description')
                        ->label('Scurtă Descriere (RO/EN)')
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Această descriere poate apărea în antetul paginii categoriei pe site, pentru a ajuta la SEO.'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nume Categorie')
                    ->searchable()
                    // FIX: Logica de sortare custom pentru coloana JSON
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('name->ro', $direction))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Categorie Părinte')
                    ->sortable(false) // Previne eroarea de sortare pe relații JSON
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Link URL')
                    ->color('gray')
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Nr. Produse')
                    ->counts('products')
                    ->badge()
                    ->color('success'),
            ])
            // FIX: Sortăm implicit după data creării, nu după nume
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), 
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
