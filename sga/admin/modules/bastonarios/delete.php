<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;

try {
    // Get photo URL to delete file
    $stmt = $pdo->prepare("SELECT foto_url FROM bastonarios WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    if($row && $row['foto_url']) {
        $upload_dir = __DIR__ . '/../../../uploads/bastonarios/';
        if(file_exists($upload_dir . $row['foto_url'])) {
            unlink($upload_dir . $row['foto_url']);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM bastonarios WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: index.php?deleted=1");
    exit;
} catch (PDOException $e) { header("Location: index.php?error=1"); exit; }
?>
