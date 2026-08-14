<!doctype html>
<html lang="ro" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', config('shop.brand_name', 'MTD Art'))</title>
    <style>
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: #f5f5dc;
        }

        table {
            border-collapse: collapse !important;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        a {
            color: #8a641f;
        }

        .email-container {
            width: 100%;
            max-width: 640px;
        }

        .content-pad {
            padding: 44px 52px;
        }

        .mobile-stack {
            display: table-cell;
        }

        @media screen and (max-width: 680px) {
            .email-container {
                width: 100% !important;
            }

            .content-pad {
                padding: 32px 24px !important;
            }

            .mobile-stack {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .mobile-stack + .mobile-stack {
                padding-top: 12px !important;
            }

            .mobile-full {
                width: 100% !important;
            }

            .mobile-center {
                text-align: center !important;
            }

            .email-title {
                font-size: 31px !important;
                line-height: 36px !important;
            }
        }
    </style>
</head>
@php
    $brandName = (string) config('shop.brand_name', 'MTD Art');
    $contactEmail = (string) config('shop.legal.email', 'contact@mtdart.ro');
    $logoUrl = asset('img/logo.png');
    $homeUrl = route('home');
    $shopUrl = route('shop.index');
    $aboutUrl = route('about');
    $contactUrl = route('contact');
@endphp
<body style="margin:0; padding:0; background-color:#f5f5dc; color:#2c1e16; font-family:Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent; mso-hide:all;">
        @yield('preheader')
        &#847; &zwnj; &nbsp; &#847; &zwnj; &nbsp; &#847; &zwnj; &nbsp;
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background-color:#f5f5dc;">
        <tr>
            <td align="center" style="padding:30px 12px 42px;">
                <table role="presentation" class="email-container" width="640" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:640px;">
                    <tr>
                        <td align="center" style="padding:0 20px 22px;">
                            <a href="{{ $homeUrl }}" style="display:inline-block; text-decoration:none;" aria-label="{{ $brandName }}">
                                <img src="{{ $logoUrl }}" width="112" alt="{{ $brandName }}" style="display:block; width:112px; max-width:112px; height:auto; margin:0 auto 10px;">
                            </a>
                            <div style="font-family:Georgia, 'Times New Roman', serif; color:#2c1e16; font-size:16px; line-height:20px; letter-spacing:5px; text-transform:uppercase;">
                                MTD ART
                            </div>
                            <div style="margin-top:7px; color:#8a641f; font-size:9px; line-height:14px; letter-spacing:2.4px; text-transform:uppercase; font-weight:700;">
                                Artă unicat &middot; Lemn &amp; rășină
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:3px; background-color:#cfb53b; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="background-color:#fffff0; border-left:1px solid #e9dfc2; border-right:1px solid #e9dfc2;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td class="content-pad" style="padding:44px 52px;">
                                        @hasSection('eyebrow')
                                            <div style="margin:0 0 12px; color:#8a641f; font-size:10px; line-height:15px; letter-spacing:2.2px; text-transform:uppercase; font-weight:700;">
                                                @yield('eyebrow')
                                            </div>
                                        @endif

                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#2c1e16; border:1px solid #2c1e16; padding:34px 38px 30px; text-align:center;">
                            <div style="font-family:Georgia, 'Times New Roman', serif; color:#ffffff; font-size:22px; line-height:27px; letter-spacing:3.5px; text-transform:uppercase;">
                                MTD Art
                            </div>
                            <div style="width:34px; height:1px; margin:14px auto 16px; background:#cfb53b; font-size:0; line-height:0;">&nbsp;</div>
                            <p style="margin:0 auto 18px; max-width:470px; color:#e7ded8; font-size:12px; line-height:20px;">
                                Piese de artă unicat, lucrate manual în România, într-un dialog dintre materia naturală și transparența rășinii.
                            </p>
                            <p style="margin:0 0 18px; color:#d9c15a; font-size:11px; line-height:18px; letter-spacing:1.1px; text-transform:uppercase;">
                                <a href="{{ $shopUrl }}" style="color:#d9c15a; text-decoration:none;">Galerie</a>
                                <span style="color:#796858; padding:0 8px;">&middot;</span>
                                <a href="{{ $aboutUrl }}" style="color:#d9c15a; text-decoration:none;">Poveste</a>
                                <span style="color:#796858; padding:0 8px;">&middot;</span>
                                <a href="{{ $contactUrl }}" style="color:#d9c15a; text-decoration:none;">Contact</a>
                            </p>
                            <p style="margin:0 0 5px; color:#cfc3bc; font-size:11px; line-height:18px;">
                                <a href="mailto:{{ $contactEmail }}" style="color:#ffffff; text-decoration:none;">{{ $contactEmail }}</a>
                            </p>
                            <p style="margin:0; color:#9f9189; font-size:10px; line-height:17px;">
                                &copy; {{ date('Y') }} {{ $brandName }}. Toate drepturile rezervate.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 26px 0; text-align:center; color:#7d6c60; font-size:10px; line-height:16px;">
                            Acest mesaj a fost trimis de {{ $brandName }} prin mtdart.ro.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
