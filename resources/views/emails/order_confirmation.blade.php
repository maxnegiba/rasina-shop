@extends('emails.layout')

@php
    $isCod = $order->isCashOnDelivery();
@endphp

@section('title', 'Confirmare comandă')
@section('preheader', $isCod
    ? 'Comanda '.$order->order_number.' a fost înregistrată cu plata ramburs.'
    : 'Comanda '.$order->order_number.' a fost achitată cu succes.')
@section('eyebrow', 'Comandă confirmată')

@section('content')
    @php
        $customerName = trim((string) data_get($order->customer_details, 'name', ''));
        $firstName = $customerName !== '' ? explode(' ', $customerName)[0] : null;
        $items = $order->items;
    @endphp

    <h1 class="email-title" style="margin:0 0 18px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:38px; line-height:43px; font-weight:400;">
        Vă mulțumim pentru comandă@if($firstName), {{ $firstName }}@endif.
    </h1>

    <p style="margin:0 0 24px; color:#5d3d2b; font-size:15px; line-height:25px;">
        @if($isCod)
            Comanda a fost înregistrată cu plata ramburs. Produsele se achită curierului la livrare, iar livrarea nu este inclusă în preț. Mai jos găsiți rezumatul comenzii.
        @else
            Plata a fost confirmată, iar piesele dumneavoastră intră acum în grija atelierului MTD Art. Mai jos găsiți rezumatul comenzii.
        @endif
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 28px; background:#fffdf4; border:1px solid #e6d9b6;">
        <tr>
            <td class="mobile-stack" width="50%" style="padding:18px 20px; border-bottom:1px solid #eee4ca; vertical-align:top;">
                <div style="color:#8a641f; font-size:9px; line-height:13px; letter-spacing:1.8px; text-transform:uppercase; font-weight:700;">Număr comandă</div>
                <div style="margin-top:6px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:17px; line-height:22px;">{{ $order->order_number }}</div>
            </td>
            <td class="mobile-stack" width="50%" style="padding:18px 20px; border-bottom:1px solid #eee4ca; vertical-align:top;">
                <div style="color:#8a641f; font-size:9px; line-height:13px; letter-spacing:1.8px; text-transform:uppercase; font-weight:700;">{{ $isCod ? 'Total produse' : 'Total achitat' }}</div>
                <div style="margin-top:6px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:17px; line-height:22px;">{{ number_format((float) $order->total_amount, 2, ',', '.') }} RON</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:15px 20px; color:#70513e; font-size:12px; line-height:19px;">
                <strong style="color:#2c1e16;">Metodă de plată:</strong> {{ $isCod ? 'Ramburs la curier' : 'Online, prin Stripe' }}
            </td>
        </tr>
        @if($order->proforma_number)
            <tr>
                <td colspan="2" style="padding:15px 20px; color:#70513e; font-size:12px; line-height:19px; border-top:1px solid #eee4ca;">
                    <strong style="color:#2c1e16;">Proformă:</strong> {{ $order->proforma_number }} &mdash; documentul PDF este atașat acestui email.
                </td>
            </tr>
        @endif
    </table>

    @if($items->isNotEmpty())
        <div style="margin:0 0 12px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:20px; line-height:26px;">Piesele din comandă</div>
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
            @foreach($items as $item)
                <tr>
                    <td style="padding:13px 0; border-bottom:1px solid #e9dfc2; color:#5d3d2b; font-size:13px; line-height:20px;">
                        <strong style="color:#2c1e16; font-weight:600;">{{ $item->displayName() }}</strong><br>
                        Cantitate: {{ $item->quantity }}
                    </td>
                    <td align="right" style="padding:13px 0 13px 14px; border-bottom:1px solid #e9dfc2; color:#2c1e16; font-size:13px; line-height:20px; white-space:nowrap;">
                        {{ number_format((float) $item->subtotal, 2, ',', '.') }} RON
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 28px;">
        <tr>
            <td style="padding:20px 22px; background:#f7f0dc; border-left:3px solid #cfb53b; color:#5d3d2b; font-size:13px; line-height:22px;">
                <strong style="color:#2c1e16;">Ce urmează?</strong><br>
                Pregătim comanda cu atenție și vă vom contacta dacă avem nevoie de informații suplimentare. {{ $isCod ? 'Valoarea produselor se achită curierului la livrare. ' : '' }}Proforma atașată este un document comercial nefiscal și nu înlocuiește documentele fiscale prevăzute de lege.
            </td>
        </tr>
    </table>

    <p style="margin:0; color:#5d3d2b; font-size:14px; line-height:23px;">
        Cu apreciere,<br>
        <strong style="color:#2c1e16;">Echipa {{ config('shop.brand_name', 'MTD Art') }}</strong>
    </p>
@endsection
