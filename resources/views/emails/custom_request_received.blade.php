@extends('emails.layout')

@section('title', 'Cerere personalizată primită')
@section('preheader', 'Am primit cererea ta personalizată și o vom analiza cu atenție.')
@section('eyebrow', 'Cerere personalizată înregistrată')

@section('content')
    <h1 class="email-title" style="margin:0 0 18px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:38px; line-height:43px; font-weight:400;">
        Mulțumim, {{ $customRequest->customer_name }}.
    </h1>

    <p style="margin:0 0 24px; color:#5d3d2b; font-size:15px; line-height:25px;">
        Cererea ta a ajuns cu bine la atelier. Fiecare piesă personalizată începe cu o conversație, iar noi vom analiza detaliile înainte de a reveni cu următorii pași.
    </p>

    @if($customRequest->product || $customRequest->dimensions_requested || $customRequest->color_preferences)
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:26px; background:#fffdf4; border:1px solid #e6d9b6;">
            @if($customRequest->product)
                <tr>
                    <td style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Piesă de referință</td>
                    <td align="right" style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px; font-weight:700;">{{ $customRequest->product->name }}</td>
                </tr>
            @endif
            @if($customRequest->dimensions_requested)
                <tr>
                    <td style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Dimensiuni dorite</td>
                    <td align="right" style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px;">{{ $customRequest->dimensions_requested }}</td>
                </tr>
            @endif
            @if($customRequest->color_preferences)
                <tr>
                    <td style="padding:16px 20px; color:#70513e; font-size:11px; line-height:18px;">Preferințe cromatice</td>
                    <td align="right" style="padding:16px 20px; color:#2c1e16; font-size:13px; line-height:18px;">{{ $customRequest->color_preferences }}</td>
                </tr>
            @endif
        </table>
    @endif

    @if($customRequest->special_message)
        <div style="margin:0 0 9px; color:#8a641f; font-size:9px; line-height:13px; letter-spacing:1.8px; text-transform:uppercase; font-weight:700;">Mesajul tău</div>
        <div style="margin-bottom:26px; padding:20px 22px; background:#f7f0dc; border-left:3px solid #cfb53b; color:#3b2818; font-family:Georgia, 'Times New Roman', serif; font-size:15px; line-height:25px; white-space:pre-wrap;">{{ $customRequest->special_message }}</div>
    @endif

    <p style="margin:0 0 26px; color:#5d3d2b; font-size:14px; line-height:23px;">
        Revenim după ce evaluăm fezabilitatea, materialele și detaliile necesare realizării. Dacă vrei să completezi cererea cu informații suplimentare, ne poți scrie oricând.
    </p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="background:#2c1e16;">
                <a href="{{ route('contact') }}" style="display:inline-block; padding:14px 22px; color:#ffffff; text-decoration:none; font-size:10px; line-height:14px; letter-spacing:1.6px; text-transform:uppercase; font-weight:700;">Contactează atelierul</a>
            </td>
        </tr>
    </table>
@endsection
