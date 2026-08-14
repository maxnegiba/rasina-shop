<?php

namespace App\Filament\Resources\ProductResource\Actions;

use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class ProductBulkActions
{
    /**
     * @return array<int, Tables\Actions\BulkAction|Tables\Actions\DeleteBulkAction>
     */
    public static function make(): array
    {
        return [
            self::publish(),
            self::makeDraft(),
            self::markSold(),
            self::relistForSale(),
            self::setStock(),
            self::setPrice(),
            self::changeCategory(),
            self::markUnique(),
            self::markStandard(),
            Tables\Actions\DeleteBulkAction::make()
                ->label('Șterge definitiv')
                ->modalHeading('Ștergere definitivă')
                ->modalDescription(
                    'Produsele selectate vor fi șterse din baza de date. Folosește „Mută în ciornă” dacă dorești doar să le ascunzi.'
                )
                ->requiresConfirmation(),
        ];
    }

    private static function publish(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('publish')
            ->label('Publică produsele')
            ->icon('heroicon-o-eye')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Publică produsele selectate')
            ->modalDescription(
                'Se publică numai produsele care au categorie, preț valid și cel puțin o imagine. Produsele incomplete sunt omise și raportate.'
            )
            ->action(function (Collection $records): void {
                $published = 0;
                $skipped = [];

                DB::transaction(function () use ($records, &$published, &$skipped): void {
                    foreach ($records as $record) {
                        /** @var Product $record */
                        $record->loadMissing('images');

                        $problems = [];

                        if (! $record->category_id) {
                            $problems[] = 'categorie lipsă';
                        }

                        if ($record->price === null || (float) $record->price <= 0) {
                            $problems[] = 'preț invalid';
                        }

                        if ($record->images->isEmpty()) {
                            $problems[] = 'fără imagini';
                        }

                        if ($problems !== []) {
                            $skipped[] = self::productName($record) . ': ' . implode(', ', $problems);

                            continue;
                        }

                        $record->update(['status' => 'published']);
                        $published++;
                    }
                });

                Notification::make()
                    ->title("{$published} produse publicate")
                    ->success()
                    ->send();

                if ($skipped !== []) {
                    $preview = array_slice($skipped, 0, 8);
                    $body = implode("\n", $preview);

                    if (count($skipped) > 8) {
                        $body .= "\n... și încă " . (count($skipped) - 8) . ' produse.';
                    }

                    Notification::make()
                        ->title(count($skipped) . ' produse nu au fost publicate')
                        ->body($body)
                        ->warning()
                        ->persistent()
                        ->send();
                }
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function makeDraft(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('make_draft')
            ->label('Mută în ciornă')
            ->icon('heroicon-o-eye-slash')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Ascunde produsele selectate')
            ->modalDescription('Produsele dispar din magazin, dar nu sunt șterse.')
            ->action(function (Collection $records): void {
                self::updateRecords($records, ['status' => 'draft']);

                Notification::make()
                    ->title($records->count() . ' produse mutate în ciornă')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function markSold(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('mark_sold')
            ->label('Marchează ca vândute')
            ->icon('heroicon-o-check-badge')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Marchează produsele ca vândute')
            ->modalDescription(
                'Stocul devine 0. Produsele publicate rămân vizibile în secțiunea produselor vândute.'
            )
            ->action(function (Collection $records): void {
                self::updateRecords($records, ['stock' => 0]);

                Notification::make()
                    ->title($records->count() . ' produse marcate ca vândute')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function relistForSale(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('relist_for_sale')
            ->label('Pune din nou la vânzare')
            ->icon('heroicon-o-arrow-path')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Pune produsele din nou la vânzare')
            ->modalDescription(
                'Produsele vândute selectate vor primi stoc 1 și vor fi publicate. Comenzile și plățile anterioare rămân neschimbate în istoric.'
            )
            ->action(function (Collection $records): void {
                $relisted = 0;
                $skipped = [];

                foreach ($records as $record) {
                    /** @var Product $record */
                    if (! $record->isSold()) {
                        $skipped[] = self::productName($record) . ': este deja în stoc';
                        continue;
                    }

                    try {
                        $record->relistForSale();
                        $relisted++;
                    } catch (\LogicException|\InvalidArgumentException $exception) {
                        $skipped[] = self::productName($record) . ': ' . $exception->getMessage();
                    }
                }

                if ($relisted > 0) {
                    Notification::make()
                        ->title("{$relisted} produse sunt din nou la vânzare")
                        ->body('Stoc: 1 · Status: Publicat')
                        ->success()
                        ->send();
                }

                if ($skipped !== []) {
                    $preview = array_slice($skipped, 0, 8);
                    $body = implode("\n", $preview);

                    if (count($skipped) > 8) {
                        $body .= "\n... și încă " . (count($skipped) - 8) . ' produse.';
                    }

                    Notification::make()
                        ->title(count($skipped) . ' produse nu au fost repuse la vânzare')
                        ->body($body)
                        ->warning()
                        ->persistent()
                        ->send();
                }
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function setStock(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('set_stock')
            ->label('Setează stocul')
            ->icon('heroicon-o-cube')
            ->color('info')
            ->form([
                Forms\Components\TextInput::make('stock')
                    ->label('Stoc nou')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->default(1)
                    ->required()
                    ->helperText('Valoarea 0 marchează produsul ca vândut sau epuizat.'),
            ])
            ->action(function (Collection $records, array $data): void {
                $stock = (int) $data['stock'];
                self::updateRecords($records, ['stock' => $stock]);

                Notification::make()
                    ->title('Stoc actualizat pentru ' . $records->count() . ' produse')
                    ->body("Stoc nou: {$stock}")
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function setPrice(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('set_price')
            ->label('Schimbă prețul')
            ->icon('heroicon-o-banknotes')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Aplică același preț produselor selectate')
            ->form([
                Forms\Components\TextInput::make('price')
                    ->label('Preț nou')
                    ->numeric()
                    ->minValue(0.01)
                    ->prefix('RON')
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $price = round((float) $data['price'], 2);
                self::updateRecords($records, ['price' => $price]);

                Notification::make()
                    ->title('Preț actualizat pentru ' . $records->count() . ' produse')
                    ->body(number_format($price, 2, ',', '.') . ' RON')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function changeCategory(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('change_category')
            ->label('Schimbă categoria')
            ->icon('heroicon-o-folder')
            ->color('primary')
            ->requiresConfirmation()
            ->form([
                Forms\Components\Select::make('category_id')
                    ->label('Categoria nouă')
                    ->options(fn (): array => Category::query()
                        ->orderBy('name->ro')
                        ->get()
                        ->mapWithKeys(fn (Category $category): array => [
                            $category->getKey() => $category->getTranslation('name', 'ro', false) ?: $category->slug,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $categoryId = (int) $data['category_id'];

                if (! Category::query()->whereKey($categoryId)->exists()) {
                    Notification::make()
                        ->title('Categoria selectată nu mai există')
                        ->danger()
                        ->send();

                    return;
                }

                self::updateRecords($records, ['category_id' => $categoryId]);

                Notification::make()
                    ->title('Categoria a fost schimbată pentru ' . $records->count() . ' produse')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function markUnique(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('mark_unique')
            ->label('Marchează ca unicat')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function (Collection $records): void {
                self::updateRecords($records, ['is_custom' => true]);

                Notification::make()
                    ->title($records->count() . ' produse marcate ca piese unicat')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function markStandard(): Tables\Actions\BulkAction
    {
        return Tables\Actions\BulkAction::make('mark_standard')
            ->label('Marchează ca produs standard')
            ->icon('heroicon-o-square-3-stack-3d')
            ->color('gray')
            ->requiresConfirmation()
            ->action(function (Collection $records): void {
                self::updateRecords($records, ['is_custom' => false]);

                Notification::make()
                    ->title($records->count() . ' produse marcate ca standard')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function updateRecords(Collection $records, array $attributes): void
    {
        DB::transaction(function () use ($records, $attributes): void {
            foreach ($records as $record) {
                /** @var Product $record */
                $record->update($attributes);
            }
        });
    }

    private static function productName(Product $product): string
    {
        $name = $product->getTranslation('name', 'ro', false);

        return $name !== '' ? $name : (string) $product->slug;
    }
}
