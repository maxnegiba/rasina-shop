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
                        Forms\Components\Tabs\Tab::make('Contact Information')
                            ->schema([
                                Forms\Components\TextInput::make('contact_whatsapp_number')
                                    ->label('WhatsApp Number')
                                    ->helperText('Include country code, e.g., +407...'),
                                Forms\Components\TextInput::make('contact_phone')
                                    ->label('Phone Number'),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email Address')
                                    ->email(),
                                Forms\Components\Textarea::make('default_whatsapp_greeting_text')
                                    ->label('Default WhatsApp Greeting Text'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->schema([
                                Forms\Components\TextInput::make('facebook_url')
                                    ->label('Facebook URL')
                                    ->url(),
                                Forms\Components\TextInput::make('instagram_url')
                                    ->label('Instagram URL')
                                    ->url(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Company Details')
                            ->schema([
                                Forms\Components\Textarea::make('company_address')
                                    ->label('Company Address'),
                                Forms\Components\Repeater::make('working_hours')
                                    ->label('Working Hours')
                                    ->schema([
                                        Forms\Components\TextInput::make('day')
                                            ->label('Zi (ex: Luni - Vineri)')
                                            ->required(),
                                        Forms\Components\TextInput::make('hours')
                                            ->label('Ore (ex: 10:00 - 18:00)')
                                            ->required(),
                                        Forms\Components\TextInput::make('note')
                                            ->label('Notă (ex: Doar programări)'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0),
                            ]),
                    ])->columnSpanFull()
            ]);
    }
}
