@extends('emails.layout')

@section('title', 'Mesaj nou de pe site')
@section('preheader', 'Ai primit un mesaj nou prin formularul de contact MTD Art.')
@section('eyebrow', 'Mesaj din formularul de contact')

@section('content')
    <h1 class="email-title" style="margin:0 0 16px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:36px; line-height:42px; font-weight:400;">
        Cineva dorește să ia legătura cu atelierul.
    </h1>
    <p style="margin:0 0 26px; color:#5d3d2b; font-size:14px; line-height:23px;">
        Mesajul de mai jos a fost trimis prin formularul de contact de pe mtdart.ro. Poți răspunde direct acestui email.
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px; background:#fffdf4; border:1px solid #e6d9b6;">
        <tr>
            <td style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Nume</td>
            <td align="right" style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px; font-weight:700;">{{ $messageData['name'] }}</td>
        </tr>
        <tr>
            <td style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#70513e; font-size:11px; line-height:18px;">Email</td>
            <td align="right" style="padding:16px 20px; border-bottom:1px solid #eee4ca; color:#2c1e16; font-size:13px; line-height:18px;"><a href="mailto:{{ $messageData['email'] }}" style="color:#8a641f;">{{ $messageData['email'] }}</a></td>
        </tr>
        <tr>
            <td style="padding:16px 20px; color:#70513e; font-size:11px; line-height:18px;">Subiect</td>
            <td align="right" style="padding:16px 20px; color:#2c1e16; font-size:13px; line-height:18px;">{{ ($messageData['subject'] ?? null) ?: 'Nespecificat' }}</td>
        </tr>
    </table>

    <div style="margin:0 0 9px; color:#8a641f; font-size:9px; line-height:13px; letter-spacing:1.8px; text-transform:uppercase; font-weight:700;">Mesaj</div>
    <div style="padding:22px; background:#f7f0dc; border-left:3px solid #cfb53b; color:#3b2818; font-family:Georgia, 'Times New Roman', serif; font-size:16px; line-height:27px; white-space:pre-wrap;">{{ $messageData['message'] }}</div>
@endsection
