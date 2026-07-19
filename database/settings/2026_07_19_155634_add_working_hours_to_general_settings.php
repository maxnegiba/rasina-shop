<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.working_hours', [
            ['day' => 'Luni - Vineri', 'hours' => '10:00 - 18:00', 'note' => ''],
            ['day' => 'Sâmbătă', 'hours' => '10:00 - 14:00', 'note' => 'Doar programări'],
            ['day' => 'Duminică', 'hours' => 'Închis', 'note' => ''],
        ]);
    }
};
