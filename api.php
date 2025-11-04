<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$jsonFile = __DIR__ . '/bookmarks.json';

function readBookmarks() {
    global $jsonFile;
    if (!file_exists($jsonFile)) {
        return [];
    }
    $content = file_get_contents($jsonFile);
    return json_decode($content, true) ?: [];
}

function saveBookmarks($data) {
    global $jsonFile;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    file_put_contents($jsonFile, $json);
}

$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents('php://input');

if ($method === 'GET') {
    echo json_encode(readBookmarks());
    exit();
}

if ($method === 'POST') {
    $data = json_decode($input, true);
    $bookmarks = readBookmarks();
    $bookmarks[] = ['name' => $data['name'], 'url' => $data['url']];
    saveBookmarks($bookmarks);
    echo json_encode(['status' => 'success']);
    exit();
}

if ($method === 'DELETE') {
    $data = json_decode($input, true);
    $bookmarks = readBookmarks();
    array_splice($bookmarks, $data['index'], 1);
    saveBookmarks($bookmarks);
    echo json_encode(['status' => 'success']);
    exit();
}

if ($method === 'PUT') {
    $data = json_decode($input, true);
    saveBookmarks($data);
    echo json_encode(['status' => 'success']);
    exit();
}

echo json_encode(['error' => 'Method not allowed']);
?>
