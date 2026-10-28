<?php

if (strpos($_SERVER['REQUEST_URI'], 'log_click.php') === false) {

    $log_file = 'stats.json';

    $visit_data = [
        'type'       => 'visit',
        'page'       => 'Strona Główna',
        'timestamp'  => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    ];

    $logs = file_exists($log_file) ? json_decode(file_get_contents($log_file), true) : [];
    if (!is_array($logs)) { $logs = []; }

    array_unshift($logs, $visit_data);

    file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT), LOCK_EX);
}

?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centrum Narzędzi iSpot</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico"> 
    <link rel="stylesheet" href="/../css/home_index.css">
   </head>

<body>

    <div class="main-container">
        <header>
            <h1>Centrum Narzędzi</h1>
            <p>Wybierz aplikację, której chcesz użyć.</p>
        </header>

        <div class="tools-grid">
            <a href="https://ispot.helptech.pl/generator/" class="tool-card">
                <div class="icon">🏷️</div>
                <h2>Generator Etykiet</h2>
                <p>Narzędzie do tworzenia i drukowania etykiet produktowych z kodami QR.</p>
                <span class="button">Uruchom</span>
            </a>
            <a href="https://ispot.helptech.pl/inwentaryzacja/" class="tool-card">
                <div class="icon">💻</div>
                <h2>Panel Inwentaryzacji</h2>
                <p>Główny panel do wgrywania plików Excel i podglądu wyników inwentaryzacji w czasie rzeczywistym.</p>
                <span class="button">Uruchom</span>
            </a>
            <a href="https://ispot.helptech.pl/inwentaryzacja/scanner.html" class="tool-card">
                <div class="icon">📱</div>
                <h2>Skaner (Telefon)</h2>
                <p>Użyj kamery w telefonie, aby skanować kody i przesyłać je do panelu inwentaryzacji.</p>
                <span class="button">Uruchom</span>
            </a>
            <a href="https://ispot.helptech.pl/inwentaryzacja/signer.html" class="tool-card">
                <div class="icon">✍️</div>
                <h2>Panel Podpisu (Tablet)</h2>
                <p>Otwórz na tablecie, aby połączyć się z sesją i złożyć podpisy pod raportem inwentaryzacyjnym.</p>
                <span class="button">Uruchom</span>
            </a>
            <a href="https://ispot.helptech.pl/serwis" class="tool-card">
                <div class="icon">🛠️</div>
                <h2>Generator Etykiet Serwisowych</h2>
                <p>Narzędzie do tworzenia i drukowania etykiet serwisowych z kodami QR.</p>
                <span class="button">Uruchom</span>
            </a>
         
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toolCards = document.querySelectorAll('.tool-card');

            toolCards.forEach(card => {
                card.addEventListener('click', function(event) {
                    event.preventDefault(); 
                    const targetUrl = this.href;
                    const toolName = this.querySelector('h2').innerText;

                    const data = {
                        target_url: targetUrl,
                        target_name: toolName
                    };

                    navigator.sendBeacon('log_click.php', JSON.stringify(data));

                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 100);
                });
            });
        });
    </script>
</body>
</html>