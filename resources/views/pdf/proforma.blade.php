<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Factura Proforma {{ $order->proforma_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .header td {
            vertical-align: top;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2C1E16; /* Dark Brown */
        }
        .title {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #2C1E16;
        }
        .details-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .details-section td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items th, table.items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.items th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        table.items td.right, table.items th.right {
            text-align: right;
        }
        .total-section {
            width: 100%;
        }
        .total-section td {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td>
                <div class="logo">Ivory Vintage Art Gallery</div>
                <div>[Nume Firma SRL]</div>
                <div>CUI: [RO...]</div>
                <div>J.../.../...</div>
                <div>[Adresa Sediu]</div>
            </td>
            <td>
                <div class="title">FACTURĂ PROFORMA</div>
                <div style="text-align: right; margin-top: 10px;">
                    <strong>Seria/Nr:</strong> {{ $order->proforma_number }}<br>
                    <strong>Data:</strong> {{ $order->created_at->format('d.m.Y') }}<br>
                    <strong>Nr. Comandă:</strong> {{ $order->order_number }}
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
                <div class="section-title">Adresă Livrare</div>
                {{ $order->customer_details['address']['line1'] ?? '' }}
                {{ $order->customer_details['address']['line2'] ?? '' }}<br>
                {{ $order->customer_details['address']['city'] ?? '' }},
                {{ $order->customer_details['address']['state'] ?? '' }}
                {{ $order->customer_details['address']['postal_code'] ?? '' }}<br>
                {{ $order->customer_details['address']['country'] ?? '' }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Nr. crt.</th>
                <th>Denumire produs</th>
                <th class="right">Cantitate</th>
                <th class="right">Preț unitar (RON)</th>
                <th class="right">Valoare (RON)</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($order->items) && $order->items->count() > 0)
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product ? $item->product->name : 'Produs #' . $item->product_id }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="text-align:center;">Comanda nu are produse detaliate (Custom Request)</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="total-section">
        <tr>
            <td style="width: 70%;"></td>
            <td style="width: 30%;">
                <table style="width: 100%;">
                    <tr class="total-row">
                        <td style="text-align: right; padding-right: 10px;">TOTAL (RON):</td>
                        <td style="text-align: right;">{{ number_format($order->total_amount, 2, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; padding: 10px; background-color: #f9f9f9; border: 1px solid #eee; text-align: center;">
        <p style="margin: 0;"><strong>Status plată:</strong> {{ $order->payment_status === 'paid' ? 'Plătit (Stripe)' : 'Neplătit' }}</p>
    </div>

    <div class="footer">
        Document generat automat. Nu necesită semnătură și ștampilă.
    </div>

</body>
</html>
