<?php
$user = require_auth($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$forceDownload = isset($_GET['download']) && $_GET['download'] === '1';

if ($id <= 0) {
    http_response_code(400);
    echo 'Document invalide.';
    exit;
}

$stmt = $pdo->prepare('SELECT filename, mime_type, size_bytes, content FROM documents WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$document = $stmt->fetch();

if (!$document) {
    http_response_code(404);
    echo 'Document introuvable.';
    exit;
}

$filename = $document['filename'] ?: 'document';
$mime = $document['mime_type'] ?: 'application/octet-stream';
$size = (int)($document['size_bytes'] ?? 0);
$content = $document['content'];

header('Content-Type: ' . $mime);
header('Content-Length: ' . $size);
header('X-Content-Type-Options: nosniff');
$disposition = $forceDownload ? 'attachment' : 'inline';
header('Content-Disposition: ' . $disposition . '; filename="' . rawurldecode(rawurlencode($filename)) . '"');

echo $content;
exit;
