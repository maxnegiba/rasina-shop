@extends('emails.layout')

@section('title', 'Cerere personalizată nouă')
@section('preheader', 'O nouă cerere personalizată a fost trimisă atelierului MTD Art.')
@section('eyebrow', 'Notificare atelier')

@section('content')
    <h1 class="email-title" style="margin:0 0 16px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:36px; line-height:42px; font-weight:400;">
        Cerere personalizată nouă.
    </h1>
    <p style="margin:0 0 26px; color:#5d3d2b; font-size:14px; line-height:23px;">
        Un client dorește o piesă realizată sau adaptată special. Poți răspunde direct acestui email pentru a continua conversația.
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:26px; background:#fffdf4; border:1px solid #e6d9b6;">
        <tr>
            <td style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Client</td>
            <td align="right" style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px; font-weight:700;">{{ $customRequest->customer_name }}</td>
        </tr>
        <tr>
            <td style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Email</td>
            <td align="right" style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px;"><a href="mailto:{{ $customRequest->customer_email }}" style="color:#8a641f;">{{ $customRequest->customer_email }}</a></td>
        </tr>
        <tr>
            <td style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Telefon</td>
            <td align="right" style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px;">{{ $customRequest->customer_phone ?: '—' }}</td>
        </tr>
        @if($customRequest->product)
            <tr>
                <td style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Piesă de referință</td>
                <td align="right" style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px;">{{ $customRequest->product->name }}</td>
            </tr>
        @endif
        @if($customRequest->dimensions_requested)
            <tr>
                <td style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Dimensiuni</td>
                <td align="right" style="padding:15px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px;">{{ $customRequest->dimensions_requested }}</td>
            </tr>
        @endif
        @if($customRequest->color_preferences)
            <tr>
                <td style="padding:15px 20px; color:#70513e; font-size:11px; line-height:18px;">Preferințe culoare</td>
                <td align="right" style="padding:15px 20px; color:#2c1e16; font-size:13px; line-height:18px;">{{ $customRequest->color_preferences }}</td>
            </tr>
        @endif
    </table>

    @if($customRequest->special_message)
        <div style="margin:0 0 9px; color:#8a641f; font-size:9px; line-height:13px; letter-spacing:1.8px; text-transform:uppercase; font-weight:700;">Mesaj client</div>
        <div style="margin-bottom:26px; padding:20px 22px; background:#f7f0dc; border-left:3px solid #cfb53b; color:#3b2818; font-family:Georgia, 'Times New Roman', serif; font-size:15px; line-height:25px; white-space:pre-wrap;">{{ $customRequest->special_message }}</div>
    @endif

    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="background:#2c1e16;">
                <a href="{{ url('/admin') }}" style="display:inline-block; padding:14px 22px; color:#ffffff; text-decoration:none; font-size:10px; line-height:14px; letter-spacing:1.6px; text-transform:uppercase; font-weight:700;">Deschide panoul de administrare</a>
            </td>
        </tr>
    </table>
@endsection
