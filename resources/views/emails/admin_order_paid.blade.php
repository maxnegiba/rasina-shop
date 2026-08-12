<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Comandă plătită</title>
</head>
<body>
    <h1>Comandă nouă plătită</h1>
    <p><strong>Comandă:</strong> {{ $order->order_number }}</p>
    <p><strong>Total:</strong> {{ number_format((float) $order->total_amount, 2, ',', '.') }} RON</p>
    <p><strong>Client:</strong> {{ data_get($order->customer_details, 'name', '—') }}</p>
    <p><strong>Email:</strong> {{ data_get($order->customer_details, 'email', '—') }}</p>
    <p><strong>Telefon:</strong> {{ data_get($order->customer_details, 'phone', '—') }}</p>
    <p>Comanda este marcată ca plătită în panoul de administrare.</p>
</body>
</html>
