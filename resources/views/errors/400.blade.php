<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Errore 404 | QUOTO!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { text-align: center; padding: 50px; font-family: Arial, sans-serif; }
        h1 { font-size: 50px; }
        body { background-color: #f4f4f4; }
        p { font-size: 20px; color: #666; }
    </style>
</head>
<body>
    <div class="container mt-5 pt-5">
      <div style="text-align:center">
        <img src="https://www.quotocrm.it/img/logotipo_quoto_2021.png" alt="QUOTO!"> 
    </div>
      <div class="alert alert-danger text-center" role="alert">
        <h2 class="display-3">400</h2>
        <p class="display-3">Oops! Errore.</p>
        <p  class="display-5">{{ $exception->getMessage()}}</p>
      </div>
      <div style="text-align:center">
        <img src="https://www.quotocrm.it/img/logo_network_2021.png"> 
    </div>
    </div>
  </body>
</html>