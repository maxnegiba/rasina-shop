<!DOCTYPE html>
<html lang="ro">
<head><meta charset="utf-8"><title>Cerere personalizată nouă</title></head>
<body>
    <h1>Cerere personalizată nouă</h1>
    <p><strong>Client:</strong> {{ $customRequest->customer_name }}</p>
    <p><strong>Email:</strong> {{ $customRequest->customer_email }}</p>
    <p><strong>Telefon:</strong> {{ $customRequest->customer_phone ?: '—' }}</p>
    @if($customRequest->product)
        <p><strong>Piesă de referință:</strong> {{ $customRequest->product->name }}</p>
    @endif
    @if($customRequest->dimensions_requested)
        <p><strong>Dimensiuni:</strong> {{ $customRequest->dimensions_requested }}</p>
    @endif
    @if($customRequest->color_preferences)
        <p><strong>Preferințe culoare:</strong> {{ $customRequest->color_preferences }}</p>
    @endif
    @if($customRequest->special_message)
        <p><strong>Mesaj:</strong><br>{{ $customRequest->special_message }}</p>
    @endif
    <p>Cererea este disponibilă și în panoul de administrare.</p>
</body>
</html>
