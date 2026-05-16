<?php
// admin/includes/ajax_media_delete.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/GalleryHelper.php';
require_once __DIR__ . '/AttachmentHelper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? 0;
$type = $_POST['type'] ?? ''; // 'gallery_noticia', 'gallery_evento', 'attachment'

if (!$id || !$type) {
    echo json_encode(['success' => false, 'message' => 'Missing ID or Type']);
    exit;
}

try {
    $success = false;
    if ($type === 'gallery_noticia') {
        $success = GalleryHelper::delete($pdo, 'noticia', $id);
    } elseif ($type === 'gallery_evento') {
        $success = GalleryHelper::delete($pdo, 'evento', $id);
    } elseif ($type === 'attachment') {
        $success = AttachmentHelper::delete($pdo, $id);
    } elseif ($type === 'quick_pdf_noticia') {
        $success = $pdo->prepare("UPDATE noticias SET ficheiro_anexo = NULL, legenda_anexo = NULL WHERE id = ?")->execute([$id]);
    } elseif ($type === 'quick_pdf_evento') {
        $success = $pdo->prepare("UPDATE agenda SET ficheiro_anexo = NULL, legenda_anexo = NULL WHERE id = ?")->execute([$id]);
    } elseif ($type === 'highlight_noticia') {
        $success = $pdo->prepare("UPDATE noticias SET imagem_destaque = NULL WHERE id = ?")->execute([$id]);
    } elseif ($type === 'highlight_evento') {
        $success = $pdo->prepare("UPDATE agenda SET imagem_destaque = NULL WHERE id = ?")->execute([$id]);
    }

    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
