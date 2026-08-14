<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Setări Site';
    protected static ?string $title = 'Setări Generale';
    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Date de contact')
                            ->schema([
                                Forms\Components\TextInput::make('contact_whatsapp_number')
                                    ->label('Număr WhatsApp')
                                    ->helperText('Include codul de țară, de exemplu +407...'),
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Telefon'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Adresă email')
                                    ->email(),
                                Forms\Components\Textarea::make('company_address')
                                    ->label('Adresă atelier')
                                    ->rows(3),
                                Forms\Components\Textarea::make('default_whatsapp_greeting_text')
                                    ->label('Mesaj WhatsApp implicit')
                                    ->rows(3),
                                Forms\Components\Repeater::make('working_hours')
                                    ->label('Program atelier')
                                    ->schema([
                                        Forms\Components\TextInput::make('day')
                                            ->label('Zi / interval')
                                            ->required(),
                                        Forms\Components\TextInput::make('hours')
                                            ->label('Ore')
                                            ->required(),
                                        Forms\Components\TextInput::make('note')
                                            ->label('Notă'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0),
                            ]),
                        Forms\Components\Tabs\Tab::make('Pagina Contact')
                            ->schema([
                                Forms\Components\TextInput::make('contact_eyebrow')->label('Etichetă antet'),
                                Forms\Components\TextInput::make('contact_title')->label('Titlu principal'),
                                Forms\Components\Textarea::make('contact_intro')->label('Text introductiv')->rows(4),
                                Forms\Components\TextInput::make('contact_address_label')->label('Titlu adresă'),
                                Forms\Components\TextInput::make('contact_address_note')->label('Notă adresă'),
                                Forms\Components\TextInput::make('contact_communication_label')->label('Titlu comunicare'),
                                Forms\Components\TextInput::make('contact_hours_label')->label('Titlu program'),
                                Forms\Components\TextInput::make('contact_custom_card_title')->label('Titlu card comenzi unicat'),
                                Forms\Components\Textarea::make('contact_custom_card_text')->label('Text card comenzi unicat')->rows(3),
                                Forms\Components\TextInput::make('contact_custom_card_cta')->label('Text buton card'),
                                Forms\Components\TextInput::make('contact_form_title')->label('Titlu formular contact'),
                                Forms\Components\TextInput::make('contact_form_response_note')->label('Notă sub formular'),
                                Forms\Components\TextInput::make('contact_custom_eyebrow')->label('Etichetă secțiune proiecte speciale'),
                                Forms\Components\TextInput::make('contact_custom_title')->label('Titlu secțiune proiecte speciale'),
                                Forms\Components\Textarea::make('contact_custom_intro')->label('Introducere proiecte speciale')->rows(4),
                            ])
                            ->columns(2),
                        Forms\Components\Tabs\Tab::make('Pagina Despre')
                            ->schema([
                                Forms\Components\TextInput::make('about_eyebrow')->label('Etichetă antet'),
                                Forms\Components\TextInput::make('about_title')->label('Titlu principal'),
                                Forms\Components\FileUpload::make('about_image')
                                    ->label('Imagine atelier / artist')
                                    ->image()
                                    ->disk('public')
                                    ->directory('site/about')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('about_image_alt')->label('Text alternativ imagine'),
                                Forms\Components\Textarea::make('about_quote')->label('Citat')->rows(3)->columnSpanFull(),
                                Forms\Components\Textarea::make('about_paragraph_one')->label('Paragraf 1')->rows(5)->columnSpanFull(),
                                Forms\Components\Textarea::make('about_paragraph_two')->label('Paragraf 2')->rows(5)->columnSpanFull(),
                                Forms\Components\TextInput::make('about_value_one_title')->label('Valoare 1 - titlu'),
                                Forms\Components\TextInput::make('about_value_two_title')->label('Valoare 2 - titlu'),
                                Forms\Components\Textarea::make('about_value_one_text')->label('Valoare 1 - text')->rows(3),
                                Forms\Components\Textarea::make('about_value_two_text')->label('Valoare 2 - text')->rows(3),
                                Forms\Components\TextInput::make('about_cta_label')->label('Text buton galerie'),
                            ])
                            ->columns(2),
                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->schema([
                                Forms\Components\TextInput::make('facebook_url')
                                    ->label('Facebook URL')
                                    ->url(),
                                Forms\Components\TextInput::make('instagram_url')
                                    ->label('Instagram URL')
                                    ->url(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
