<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$scanDir = __DIR__ . '/scans/';
if (!is_dir($scanDir)) {
    mkdir($scanDir, 0775, true);
}

$action = $_REQUEST['action'] ?? null;
$session = $_REQUEST['session'] ?? null;

if (!$session || !ctype_digit($session)) {
    echo json_encode(['error' => 'Brak lub niepoprawny format kodu sesji.']);
    exit;
}

$sessionConnectionFile = $scanDir . $session . '_conn.flag'; 
$sessionScanFile = $scanDir . $session . '.txt';             
$sessionSignatureFile1 = $scanDir . $session . '_sig1.txt';    
$sessionSignatureFile2 = $scanDir . $session . '_sig2.txt';    

switch ($action) {
    case 'create_session':
        touch($sessionConnectionFile); 
        echo json_encode(['status' => 'session_created', 'sessionCode' => $session]);
        break;

    case 'join_session':
        if (file_exists($sessionConnectionFile)) {
            echo json_encode(['status' => 'ok', 'message' => 'Połączono pomyślnie.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Sesja o podanym kodzie nie istnieje.']);
        }
        break;

    case 'save_scan':
        $code = $_POST['code'] ?? null;
        if ($code && file_exists($sessionConnectionFile)) {
            file_put_contents($sessionScanFile, trim($code) . PHP_EOL, FILE_APPEND | LOCK_EX);
            echo json_encode(['status' => 'ok']);
        }
        break;

    case 'get_scans':
        if (file_exists($sessionScanFile)) {
            $scans = file_get_contents($sessionScanFile);
            file_put_contents($sessionScanFile, '', LOCK_EX); 
            echo json_encode(array_filter(explode(PHP_EOL, $scans)));
        } else {
            echo json_encode([]);
        }
        break;
        
    case 'save_signatures':
        $sig1 = $_POST['signature1'] ?? null;
        $sig2 = $_POST['signature2'] ?? null;
        if ($sig1 && $sig2 && file_exists($sessionConnectionFile)) {
            file_put_contents($sessionSignatureFile1, $sig1);
            file_put_contents($sessionSignatureFile2, $sig2);
            echo json_encode(['status' => 'signatures_saved']);
        } else {
            echo json_encode(['error' => 'Brak danych podpisu lub sesja nieaktywna.']);
        }
        break;

    case 'get_signatures':
        if (file_exists($sessionSignatureFile1) && file_exists($sessionSignatureFile2)) {
            $sig1 = file_get_contents($sessionSignatureFile1);
            $sig2 = file_get_contents($sessionSignatureFile2);
            unlink($sessionSignatureFile1);
            unlink($sessionSignatureFile2);
            echo json_encode(['status' => 'ok', 'signature1' => $sig1, 'signature2' => $sig2]);
        } else {
            echo json_encode(['status' => 'waiting']);
        }
        break;

    default:
        echo json_encode(['error' => 'Nieznana akcja.']);
        break;
}

if (rand(1, 20) === 1) {
    foreach (glob($scanDir . "*") as $file) {
        if (filemtime($file) < time() - 7200) {
            unlink($file);
        }
    }
}
?>