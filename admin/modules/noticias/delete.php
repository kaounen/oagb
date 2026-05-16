<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        // Find filename to delete
        $stmt = $pdo->prepare("SELECT foto1 FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $filename = $row['foto1'];
            $upload_path = __DIR__ . '/../../../uploads/' . $filename;
            
            // Delete record
            $del_stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
            if ($del_stmt->execute([$id])) {
                // Delete physical file
                if (!empty($filename) && file_exists($upload_path)) {
                    unlink($upload_path);
                }
                header("Location: index.php?deleted=1");
                exit;
            }
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
}

header("Location: index.php");
exit;
?>
