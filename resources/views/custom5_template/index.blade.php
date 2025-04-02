<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Dinamico</title>
</head>
<body>
    <h1>Dettagli della Richiesta</h1>
    <ul>
        <li><strong>Template:</strong> custom5</li>
        <li><strong>Directory:</strong> {{ $directory }}</li>
        <li><strong>ID Richiesta:</strong> {{ $id_richiesta }}</li>
        <li><strong>ID Sito:</strong> {{ $idsito }}</li>
        <li><strong>Tipo:</strong> {{ $tipo }}</li>
    </ul>
</body>
</html>