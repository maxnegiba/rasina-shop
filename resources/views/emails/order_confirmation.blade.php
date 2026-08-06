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
    <p>Atașat acestui email veți găsi documentul proforma corespunzător. Proforma este un document comercial nefiscal și nu înlocuiește documentele fiscale prevăzute de lege.</p>
    <p>Echipa noastră pregătește piesele cu mare grijă. Veți primi o nouă notificare atunci când comanda va fi expediată.</p>
    <br>
    <p>Cu respect,</p>
    <p>Echipa {{ config('shop.brand_name') }}</p>
</body>
</html>
