<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · MTD Art</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #fdfbf7; color: #2c1e16; font-family: Georgia, serif; text-align: center; }
        main { width: min(100%, 640px); padding: clamp(32px, 7vw, 64px); border: 1px solid rgba(44, 30, 22, .12); background: #fff; box-shadow: 0 12px 40px rgba(44, 30, 22, .07); }
        .code { color: #a88a3d; font-family: Arial, sans-serif; font-size: 12px; font-weight: 700; letter-spacing: .28em; }
        h1 { margin: 18px 0; font-size: clamp(34px, 7vw, 54px); font-weight: 400; }
        p { margin: 0 auto; max-width: 470px; color: rgba(44, 30, 22, .68); font-family: Arial, sans-serif; font-size: 15px; line-height: 1.7; }
        a { display: inline-block; margin-top: 30px; padding: 15px 24px; background: #2c1e16; color: #fff; font-family: Arial, sans-serif; font-size: 11px; font-weight: 700; letter-spacing: .15em; text-decoration: none; text-transform: uppercase; }
        a:hover, a:focus-visible { background: #a88a3d; }
    </style>
</head>
<body>
    <main>
        <div class="code">@yield('code')</div>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        <a href="{{ route('home') }}">Înapoi la pagina principală</a>
    </main>
</body>
</html>
