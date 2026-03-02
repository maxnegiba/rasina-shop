<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Carbon\Carbon;

class PostResource extends Resource
{
    // Activăm funcționalitatea bilingvă pentru blog
    use Translatable;

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Conținut & Marketing';
    protected static ?string $modelLabel = 'Articol';
    protected static ?string $pluralModelLabel = 'Jurnal de Atelier';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Conținutul Articolului')->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titlu Articol (RO/EN)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

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
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Imagine & Publicare')->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Imagine Reprezentativă')
                            ->image()
                            ->directory('blog')
                            ->required(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Data Publicării')
                            ->helperText('Dacă lași gol, articolul va fi salvat ca ciornă. Dacă pui o dată în viitor, se va publica automat atunci.'),
                    ]),

                    Forms\Components\Section::make('Optimizare SEO')->schema([
                        Forms\Components\Textarea::make('seo_meta_description')
                            ->label('Meta Descriere (Google)')
                            ->rows(3)
                            ->helperText('Un rezumat scurt (150-160 caractere) care va apărea în rezultatele căutărilor Google.')
                            ->maxLength(255),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Imagine')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titlu')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('published_at')
                    ->label('Status')
                    ->colors([
                        'warning' => fn ($state): bool => $state === null || Carbon::parse($state)->isFuture(),
                        'success' => fn ($state): bool => $state !== null && Carbon::parse($state)->isPast(),
                    ])
                    ->formatStateUsing(function ($state) {
                        if ($state === null) return 'Ciornă';
                        if (Carbon::parse($state)->isFuture()) return 'Programat';
                        return 'Publicat';
                    }),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Data')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
