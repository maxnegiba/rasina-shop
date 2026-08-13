<!DOCTYPE html>
<html lang="ro">
<head><meta charset="utf-8"><title>Cerere primită</title></head>
<body>
    <h1>Am primit cererea ta</h1>
    <p>Bună, {{ $customRequest->customer_name }}!</p>
    <p>Îți confirmăm că cererea ta personalizată a fost înregistrată cu succes.</p>
    @if($customRequest->product)
        <p><strong>Piesă de referință:</strong> {{ $customRequest->product->name }}</p>
    @endif
    @if($customRequest->special_message)
        <p><strong>Mesajul tău:</strong><br>{{ $customRequest->special_message }}</p>
    @endif
    <p>Vom reveni după ce analizăm detaliile cererii.</p>
</body>
</html>
