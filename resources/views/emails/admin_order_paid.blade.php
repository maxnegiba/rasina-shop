@extends('emails.layout')

@section('title', 'Comandă nouă plătită')
@section('preheader', 'Comanda '.$order->order_number.' a fost plătită și necesită procesare.')
@section('eyebrow', 'Notificare atelier')

@section('content')
    @php
        $customerName = data_get($order->customer_details, 'name', '—');
        $customerEmail = data_get($order->customer_details, 'email', '—');
        $customerPhone = data_get($order->customer_details, 'phone', '—');
    @endphp

    <h1 class="email-title" style="margin:0 0 16px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:36px; line-height:42px; font-weight:400;">
        O comandă nouă este achitată.
    </h1>
    <p style="margin:0 0 26px; color:#5d3d2b; font-size:14px; line-height:23px;">
        Plata a fost confirmată. Comanda poate fi preluată pentru pregătire și procesare în panoul de administrare.
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px; background:#fffdf4; border:1px solid #e6d9b6;">
        <tr>
            <td style="padding:18px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:12px; line-height:19px;">Comandă</td>
            <td align="right" style="padding:18px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-weight:700; font-size:13px; line-height:19px;">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:18px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:12px; line-height:19px;">Total achitat</td>
            <td align="right" style="padding:18px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:18px; line-height:22px;">{{ number_format((float) $order->total_amount, 2, ',', '.') }} RON</td>
        </tr>
        <tr>
            <td colspan="2" style="padding:18px 20px;">
                <div style="margin-bottom:5px; color:#8a641f; font-size:9px; line-height:13px; letter-spacing:1.7px; text-transform:uppercase; font-weight:700;">Client</div>
                <div style="color:#2c1e16; font-size:14px; line-height:22px;"><strong>{{ $customerName }}</strong></div>
                <div style="color:#70513e; font-size:12px; line-height:20px;">{{ $customerEmail }} &middot; {{ $customerPhone }}</div>
            </td>
        </tr>
    </table>

    @if($order->items->isNotEmpty())
        <div style="margin:0 0 10px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:19px; line-height:25px;">Conținut comandă</div>
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:26px;">
            @foreach($order->items as $item)
                <tr>
                    <td style="padding:11px 0; border-bottom:1px solid #e9dfc2; color:#5d3d2b; font-size:12px; line-height:19px;">
                        <strong style="color:#2c1e16;">{{ $item->displayName() }}</strong> &times; {{ $item->quantity }}
                    </td>
                    <td align="right" style="padding:11px 0 11px 12px; border-bottom:1px solid #e9dfc2; color:#2c1e16; font-size:12px; line-height:19px; white-space:nowrap;">
                        {{ number_format((float) $item->subtotal, 2, ',', '.') }} RON
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="background:#2c1e16;">
                <a href="{{ url('/admin') }}" style="display:inline-block; padding:14px 22px; color:#ffffff; text-decoration:none; font-size:10px; line-height:14px; letter-spacing:1.6px; text-transform:uppercase; font-weight:700;">Deschide panoul de administrare</a>
            </td>
        </tr>
    </table>
@endsection
