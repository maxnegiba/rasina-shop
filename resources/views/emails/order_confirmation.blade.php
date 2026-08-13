<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Confirmare Comandă</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #2C1E16;">Vă mulțumim pentru comandă!</h2>
    <p>Bună ziua,</p>
    <p>Comanda dumneavoastră cu numărul <strong>{{ $order->order_number }}</strong> a fost plasată cu succes și a fost achitată.</p>
    <p>Total achitat: <strong>{{ number_format($order->total_amount, 2, ',', '.') }} RON</strong>.</p>
    <p>Atașat acestui email veți găsi documentul proforma corespunzător. Proforma este un document comercial nefiscal și nu înlocuiește documentele fiscale prevăzute de lege.</p>
    <p>Echipa noastră va procesa comanda cu grijă și vă va contacta dacă sunt necesare informații suplimentare.</p>
    <br>
    <p>Cu respect,</p>
    <p>Echipa {{ config('shop.brand_name') }}</p>
</body>
</html>
