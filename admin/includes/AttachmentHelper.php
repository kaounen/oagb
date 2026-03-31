<?php
// admin/includes/AttachmentHelper.php

class AttachmentHelper {
    private static $upload_dir = __DIR__ . '/../../../uploads/attachments/';

    public static function save($pdo, $entity_type, $entity_id, $files) {
        if (!file_exists(self::$upload_dir)) mkdir(self::$upload_dir, 0777, true);

        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $files['tmp_name'][$i];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_name = $entity_type . '_' . $entity_id . '_' . uniqid() . '.' . $ext;
                $mime = $files['type'][$i];
                $size = $files['size'][$i];

                if (move_uploaded_file($tmp_name, self::$upload_dir . $new_name)) {
                    $stmt = $pdo->prepare("INSERT INTO ficheiros_anexos (tipo_entidade, entidade_id, nome_ficheiro, nome_original, tipo_mime, tamanho) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$entity_type, $entity_id, $new_name, $name, $mime, $size]);
                }
            }
        }
    }

    public static function get($pdo, $entity_type, $entity_id) {
        $stmt = $pdo->prepare("SELECT * FROM ficheiros_anexos WHERE tipo_entidade = ? AND entidade_id = ? ORDER BY created_at DESC");
        $stmt->execute([$entity_type, $entity_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM ficheiros_anexos WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $path = self::$upload_dir . $file['nome_ficheiro'];
            if (file_exists($path)) unlink($path);
            
            $stmt = $pdo->prepare("DELETE FROM ficheiros_anexos WHERE id = ?");
            return $stmt->execute([$id]);
        }
        return false;
    }
}
