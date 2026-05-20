<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/AttachmentHelper.php';
require_once __DIR__ . '/../../includes/GalleryHelper.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        // Fetch article to get file paths
        $stmt = $pdo->prepare("SELECT imagem_destaque, ficheiro_anexo FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $upload_dir = __DIR__ . '/../../../uploads/';
            
            // 1. Delete highlight image file
            if (!empty($row['imagem_destaque'])) {
                $img_path = $upload_dir . $row['imagem_destaque'];
                if (file_exists($img_path)) {
                    @unlink($img_path);
                }
            }
            
            // 2. Delete main PDF attachment file
            if (!empty($row['ficheiro_anexo'])) {
                $pdf_path = $upload_dir . $row['ficheiro_anexo'];
                if (file_exists($pdf_path)) {
                    @unlink($pdf_path);
                }
            }
            
            // 3. Delete linked multiple attachments files & DB rows
            $attachments = AttachmentHelper::get($pdo, 'noticia', $id);
            foreach ($attachments as $att) {
                AttachmentHelper::delete($pdo, $att['id']);
            }
            
            // 4. Delete linked gallery images files & DB rows
            $gallery = GalleryHelper::get($pdo, 'noticia', $id);
            foreach ($gallery as $gal) {
                GalleryHelper::delete($pdo, 'noticia', $gal['id']);
            }
            
            // 5. Finally delete the news record itself
            $del_stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
            $del_stmt->execute([$id]);
            
            header("Location: index.php?deleted=1");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Error deleting article: " . $e->getMessage());
    }
}

header("Location: index.php");
exit;
?>
