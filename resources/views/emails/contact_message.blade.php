<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Mesaj nou de pe site</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Mesaj nou de pe mtdart.ro</h2>
    <p><strong>Nume:</strong> {{ $messageData['name'] }}</p>
    <p><strong>Email:</strong> {{ $messageData['email'] }}</p>
    <p><strong>Subiect:</strong> {{ ($messageData['subject'] ?? null) ?: 'Nespecificat' }}</p>
    <p><strong>Mesaj:</strong></p>
    <p style="white-space: pre-wrap;">{{ $messageData['message'] }}</p>
</body>
</html>
