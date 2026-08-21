<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Vizitatori GA4 · 30 zile', 'value' => number_format($gaTotals['visitors'] ?? 0, 0, ',', '.')],
                ['label' => 'Page views GA4 · 30 zile', 'value' => number_format($gaTotals['pageViews'] ?? 0, 0, ',', '.')],
                ['label' => 'Comenzi valide', 'value' => number_format($ordersCount, 0, ',', '.')],
                ['label' => 'Valoare comenzi', 'value' => number_format($ordersValue, 2, ',', '.').' RON'],
            ];
        @endphp

        @foreach ($cards as $card)
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $card['label'] }}</div>
                <div class="mt-1 text-2xl font-semibold">{{ $card['value'] }}</div>
            </x-filament::section>
        @endforeach
    </div>

    @if (! $analyticsConfigured)
        <x-filament::section>
            <div class="text-sm">
                GA4 Data API nu este încă activ: configurează <code>ANALYTICS_PROPERTY_ID</code> și fișierul JSON al service account-ului.
                Datele UTM/comenzi de mai jos funcționează independent.
            </div>
        </x-filament::section>
    @elseif ($gaError)
        <x-filament::section>
            <div class="text-sm text-danger-600">{{ $gaError }}</div>
        </x-filament::section>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <x-filament::section heading="Atribuire campanii">
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Comenzi atribuite</div>
                    <div class="text-xl font-semibold">{{ number_format($attributedOrdersCount, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Valoare atribuită</div>
                    <div class="text-xl font-semibold">{{ number_format($attributedOrdersValue, 2, ',', '.') }} RON</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left dark:border-gray-700">
                            <th class="py-2 pr-3">Source</th>
                            <th class="py-2 pr-3">Medium</th>
                            <th class="py-2 pr-3">Campaign</th>
                            <th class="py-2 pr-3 text-right">Comenzi</th>
                            <th class="py-2 text-right">Valoare</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($campaigns as $campaign)
                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2 pr-3">{{ $campaign->utm_source }}</td>
                                <td class="py-2 pr-3">{{ $campaign->utm_medium ?: '—' }}</td>
                                <td class="py-2 pr-3">{{ $campaign->utm_campaign ?: '—' }}</td>
                                <td class="py-2 pr-3 text-right">{{ $campaign->orders_count }}</td>
                                <td class="py-2 text-right">{{ number_format((float) $campaign->revenue, 2, ',', '.') }} RON</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-gray-500">Nu există încă comenzi cu UTM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Top pagini GA4 · 30 zile">
            <div class="space-y-3">
                @forelse ($topPages as $page)
                    <div class="flex items-start justify-between gap-4 border-b pb-2 dark:border-gray-800">
                        <div class="min-w-0">
                            <div class="truncate font-medium">{{ $page['pageTitle'] ?? $page['fullPageUrl'] ?? 'Pagină' }}</div>
                            <div class="truncate text-xs text-gray-500">{{ $page['fullPageUrl'] ?? '' }}</div>
                        </div>
                        <div class="shrink-0 font-semibold">{{ number_format((int) ($page['screenPageViews'] ?? 0), 0, ',', '.') }}</div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500">Nu sunt date GA4 disponibile încă.</div>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    <x-filament::section heading="Top referrers GA4 · 30 zile">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($topReferrers as $referrer)
                <div class="rounded-lg border p-3 dark:border-gray-700">
                    <div class="truncate text-sm font-medium">{{ $referrer['pageReferrer'] ?: '(direct)' }}</div>
                    <div class="mt-1 text-xs text-gray-500">{{ number_format((int) ($referrer['screenPageViews'] ?? 0), 0, ',', '.') }} views</div>
                </div>
            @empty
                <div class="text-sm text-gray-500">Nu sunt date GA4 disponibile încă.</div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
