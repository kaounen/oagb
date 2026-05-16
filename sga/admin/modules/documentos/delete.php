<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT arquivo FROM documentos_publicos WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            $filename = $row['arquivo'];
            $upload_path = __DIR__ . '/../../../../uploads/' . $filename;
            
            $del_stmt = $pdo->prepare("DELETE FROM documentos_publicos WHERE id = ?");
            if ($del_stmt->execute([$id])) {
                if (!empty($filename) && file_exists($upload_path)) {
                    unlink($upload_path);
                }
                header("Location: index.php?deleted=1");
                exit;
            }
        }
    } catch (PDOException $e) { error_log($e->getMessage()); }
}

header("Location: index.php");
exit;
?>
