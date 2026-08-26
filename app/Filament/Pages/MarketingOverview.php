<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class MarketingOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationLabel = 'Marketing';
    protected static ?string $title = 'Marketing & Analytics';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.marketing-overview';

    public function getViewData(): array
    {
        $period = Period::days(30);
        $gaError = null;
        $gaTotals = ['visitors' => 0, 'pageViews' => 0];
        $topPages = collect();
        $topReferrers = collect();

        if ($this->analyticsConfigured()) {
            try {
                $totals = Analytics::fetchTotalVisitorsAndPageViews($period);
                $gaTotals = [
                    // spatie/laravel-analytics 5.7 returns activeUsers and
                    // screenPageViews. Keep the old keys as fallbacks so the
                    // dashboard also remains compatible with older responses.
                    'visitors' => (int) $totals->sum(
                        fn (array $row): int => (int) ($row['activeUsers'] ?? $row['visitors'] ?? 0),
                    ),
                    'pageViews' => (int) $totals->sum(
                        fn (array $row): int => (int) ($row['screenPageViews'] ?? $row['pageViews'] ?? 0),
                    ),
                ];
                $topPages = Analytics::fetchMostVisitedPages($period, 10);
                $topReferrers = Analytics::fetchTopReferrers($period, 10);
            } catch (\Throwable $exception) {
                report($exception);
                $gaError = 'Datele GA4 nu au putut fi încărcate. Verifică Property ID și service account-ul.';
            }
        }

        $completedOrders = Order::query()
            ->whereNull('cancelled_at')
            ->where(function ($query): void {
                $query->where('payment_status', 'paid')
                    ->orWhere('payment_method', 'cash_on_delivery');
            });

        $attributedOrders = (clone $completedOrders)->whereNotNull('utm_source');

        return [
            'analyticsConfigured' => $this->analyticsConfigured(),
            'gaError' => $gaError,
            'gaTotals' => $gaTotals,
            'topPages' => $topPages,
            'topReferrers' => $topReferrers,
            'ordersCount' => (clone $completedOrders)->count(),
            'ordersValue' => (float) (clone $completedOrders)->sum('total_amount'),
            'attributedOrdersCount' => (clone $attributedOrders)->count(),
            'attributedOrdersValue' => (float) (clone $attributedOrders)->sum('total_amount'),
            'campaigns' => $this->campaigns($completedOrders),
        ];
    }

    private function analyticsConfigured(): bool
    {
        if (! class_exists(Analytics::class)) {
            return false;
        }

        $propertyId = trim((string) config('analytics.property_id'));
        $credentials = config('analytics.service_account_credentials_json');

        if ($propertyId === '') {
            return false;
        }

        return is_array($credentials)
            ? $credentials !== []
            : is_string($credentials) && $credentials !== '' && is_file($credentials);
    }

    private function campaigns($completedOrders): Collection
    {
        return (clone $completedOrders)
            ->whereNotNull('utm_source')
            ->selectRaw('utm_source, utm_medium, utm_campaign, COUNT(*) as orders_count, SUM(total_amount) as revenue')
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
            ->orderByDesc('orders_count')
            ->limit(20)
            ->get();
    }
}
