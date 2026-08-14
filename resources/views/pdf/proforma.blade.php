<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Proforma {{ $order->proforma_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; margin-bottom: 30px; }
        .header td { vertical-align: top; }
        .logo { font-size: 24px; font-weight: bold; color: #2C1E16; }
        .title { text-align: right; font-size: 20px; font-weight: bold; color: #2C1E16; }
        .details-section { width: 100%; margin-bottom: 30px; }
        .details-section td { width: 50%; vertical-align: top; }
        .section-title { font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.items th { background-color: #f9f9f9; font-weight: bold; }
        table.items td.right, table.items th.right { text-align: right; }
        .total-section { width: 100%; }
        .total-section td { text-align: right; }
        .total-row { font-weight: bold; font-size: 14px; }
        .delivery-note { margin-top: 18px; padding: 10px; background-color: #faf8f0; border: 1px solid #e7dfbd; text-align: center; color: #5b4a32; }
        .payment-status { margin-top: 18px; padding: 10px; background-color: #f9f9f9; border: 1px solid #eee; text-align: center; line-height: 20px; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <div class="logo">{{ config('shop.brand_name') }}</div>
                <div><strong>{{ config('shop.legal.business_name') }}</strong></div>
                <div>CUI: {{ config('shop.legal.tax_id') }}</div>
                <div>Nr. Reg. Com.: {{ config('shop.legal.trade_register') }}</div>
                @if(config('shop.legal.euid'))<div>EUID: {{ config('shop.legal.euid') }}</div>@endif
                <div>{{ config('shop.legal.address') }}</div>
                <div>{{ config('shop.legal.email') }} · {{ config('shop.legal.phone') }}</div>
                @if(config('shop.legal.iban'))<div>IBAN: {{ config('shop.legal.iban') }}</div>@endif
                @if(config('shop.legal.bank'))<div>Banca: {{ config('shop.legal.bank') }}</div>@endif
            </td>
            <td>
                <div class="title">PROFORMA</div>
                <div style="text-align: right; margin-top: 10px;">
                    <strong>Seria/Nr:</strong> {{ $order->proforma_number }}<br>
                    <strong>Data:</strong> {{ $order->created_at->format('d.m.Y') }}<br>
                    <strong>Nr. comandă:</strong> {{ $order->order_number }}
                </div>
            </td>
        </tr>
    </table>

    <table class="details-section">
        <tr>
            <td style="padding-right: 20px;">
                <div class="section-title">Client / Facturare</div>
                <strong>Nume:</strong> {{ $order->customer_details['name'] ?? '-' }}<br>
                <strong>Email:</strong> {{ $order->customer_details['email'] ?? '-' }}<br>
                <strong>Telefon:</strong> {{ $order->customer_details['phone'] ?? '-' }}<br>
            </td>
            <td>
                <div class="section-title">Adresă de livrare</div>
                {{ $order->customer_details['address']['line1'] ?? '' }} {{ $order->customer_details['address']['line2'] ?? '' }}<br>
                {{ $order->customer_details['address']['city'] ?? '' }}, {{ $order->customer_details['address']['state'] ?? '' }} {{ $order->customer_details['address']['postal_code'] ?? '' }}<br>
                {{ $order->customer_details['address']['country'] ?? '' }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Nr. crt.</th><th>Denumire produs</th><th class="right">Cantitate</th><th class="right">Preț unitar (RON)</th><th class="right">Valoare (RON)</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($order->items) && $order->items->count() > 0)
                @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->displayName() }}{{ $item->product_code ? ' ('.$item->product_code.')' : '' }}</td>
                        <td class="right">{{ $item->quantity }}</td>
                        <td class="right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="5" style="text-align:center;">Comanda nu are produse detaliate.</td></tr>
            @endif
        </tbody>
    </table>

    <table class="total-section">
        <tr>
            <td style="width: 70%;"></td>
            <td style="width: 30%;">
                <table style="width: 100%;">
                    <tr><td style="text-align: right; padding-right: 10px;">Subtotal produse:</td><td style="text-align: right;">{{ number_format($order->subtotal_amount, 2, ',', '.') }}</td></tr>
                    @if((float) $order->discount_amount > 0)
                        <tr><td style="text-align: right; padding-right: 10px;">Reducere:</td><td style="text-align: right;">-{{ number_format($order->discount_amount, 2, ',', '.') }}</td></tr>
                    @endif
                    <tr class="total-row"><td style="text-align: right; padding-right: 10px;">TOTAL PRODUSE (RON):</td><td style="text-align: right;">{{ number_format($order->total_amount, 2, ',', '.') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="delivery-note">Livrarea nu este inclusă în preț.</div>

    <div class="payment-status">
        @if($order->isCashOnDelivery())
            <strong>Metodă de plată:</strong> Ramburs la curier<br>
            <strong>Status plată:</strong> {{ $order->payment_status === 'paid' ? 'Încasat' : 'De achitat la livrare' }}
        @else
            <strong>Metodă de plată:</strong> Online (Stripe)<br>
            <strong>Status plată:</strong> {{ $order->payment_status === 'paid' ? 'Plătit' : 'Neplătit' }}
        @endif
    </div>

    <div class="footer">
        Document comercial nefiscal, generat automat. Nu reprezintă factură fiscală și nu poate fi folosit pentru deducerea TVA.
    </div>

</body>
</html>
