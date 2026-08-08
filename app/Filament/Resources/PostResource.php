<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use RalphJSmit\Filament\SEO\SEO;

class PostResource extends Resource
{
    use Translatable;

    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Conținut & Marketing';
    protected static ?string $modelLabel = 'Articol';
    protected static ?string $pluralModelLabel = 'Jurnal de Atelier';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Conținutul Articolului')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titlu Articol (RO/EN)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('author')
                        ->label('Autor Articol')
                        ->default('MTD ART')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->label('URL Prietenos (Slug)')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\RichEditor::make('content')
                        ->label('Conținut (RO/EN)')
                        ->required()
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3', 'bulletList', 'orderedList',
                            'link', 'blockquote', 'undo', 'redo',
                        ])
                        ->columnSpanFull(),
                ])->columns(2),

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
                Forms\Components\Section::make('Imagine & Publicare')->schema([
                    Forms\Components\FileUpload::make('featured_image')
                        ->label('Imagine Reprezentativă')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(10240)
                        ->disk('public')
                        ->visibility('public')
                        ->directory('blog')
                        ->required(),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Data Publicării')
                        ->helperText('Gol = ciornă; dată viitoare = publicare programată.'),
                ]),
                Forms\Components\Section::make('SEO principal / fallback')
                    ->schema([SEO::make()])
                    ->collapsed(),
                Forms\Components\Section::make('Optimizare SEO veche')->schema([
                    Forms\Components\Textarea::make('seo_meta_description')
                        ->label('Meta Descriere (Google)')
                        ->rows(3)
                        ->maxLength(255),
                ])->hidden(),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('featured_image')->label('Imagine')->circular(),
            Tables\Columns\TextColumn::make('title')
                ->label('Titlu')->searchable()->sortable()->limit(40)->weight('bold'),
            Tables\Columns\BadgeColumn::make('published_at')
                ->label('Status')
                ->colors([
                    'warning' => fn ($state): bool => $state === null || Carbon::parse($state)->isFuture(),
                    'success' => fn ($state): bool => $state !== null && Carbon::parse($state)->isPast(),
                ])
                ->formatStateUsing(function ($state): string {
                    if ($state === null) {
                        return 'Ciornă';
                    }
                    if (Carbon::parse($state)->isFuture()) {
                        return 'Programat';
                    }
                    return 'Publicat';
                }),
            Tables\Columns\TextColumn::make('published_at')->label('Data')->date('d M Y')->sortable(),
        ])->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
