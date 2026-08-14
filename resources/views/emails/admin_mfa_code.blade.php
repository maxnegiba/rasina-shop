@extends('emails.layout')

@section('title', 'Cod de securitate')
@section('preheader', 'Codul tău de securitate MTD ART este '.$code.'.')
@section('eyebrow', 'Acces securizat &middot; Panou administrare')

@section('content')
    <h1 class="email-title" style="margin:0 0 16px; color:#2c1e16; font-family:Georgia, 'Times New Roman', serif; font-size:36px; line-height:42px; font-weight:400;">
        Confirmă autentificarea.
    </h1>
    <p style="margin:0 0 26px; color:#5d3d2b; font-size:14px; line-height:23px;">
        Folosește codul de mai jos pentru a continua autentificarea în panoul de administrare MTD ART.
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td align="center" style="padding:27px 18px; background:#2c1e16; border-top:3px solid #cfb53b;">
                <div style="margin-bottom:8px; color:#d9c15a; font-size:9px; line-height:13px; letter-spacing:2px; text-transform:uppercase; font-weight:700;">Cod de securitate</div>
                <div style="color:#ffffff; font-family:'Courier New', Courier, monospace; font-size:34px; line-height:42px; letter-spacing:8px; font-weight:700;">{{ $code }}</div>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 22px; color:#5d3d2b; font-size:13px; line-height:22px;">
        Codul expiră în <strong style="color:#2c1e16;">{{ $expiresInMinutes }} minute</strong> și poate fi folosit doar pentru sesiunea de autentificare curentă.
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding:18px 20px; background:#f7f0dc; border-left:3px solid #cfb53b; color:#70513e; font-size:12px; line-height:20px;">
                <strong style="color:#2c1e16;">Nu ai solicitat acest cod?</strong><br>
                Nu îl comunica nimănui. Dacă nu ai încercat să te autentifici, schimbă parola contului de administrator și verifică activitatea contului.
            </td>
        </tr>
    </table>
@endsection
