<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Hasil - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="/assets/css/main/app.css">
    <link rel="stylesheet" href="/assets/css/main/app-dark.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>
