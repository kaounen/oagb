<?php
// admin/includes/GalleryHelper.php

class GalleryHelper {
    private static $upload_dir = __DIR__ . '/../../uploads/';

    public static function save($pdo, $type, $entity_id, $files, $titles = [], $descriptions = []) {
        if (!file_exists(self::$upload_dir)) mkdir(self::$upload_dir, 0777, true);

        $table = ($type === 'noticia') ? 'noticias_imagens' : 'agenda_imagens';
        $fk_field = ($type === 'noticia') ? 'noticia_id' : 'agenda_id';

        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $files['tmp_name'][$i];
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $new_name = 'gal_' . $type . '_' . $entity_id . '_' . uniqid() . '.' . $ext;
                
                $legenda = $titles[$i] ?? '';
                $descricao = $descriptions[$i] ?? '';

                if (move_uploaded_file($tmp_name, self::$upload_dir . $new_name)) {
                    $stmt = $pdo->prepare("INSERT INTO $table ($fk_field, imagem, legenda, descricao) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$entity_id, $new_name, $legenda, $descricao]);
                }
            }
        }
    }

    public static function get($pdo, $type, $entity_id) {
        $table = ($type === 'noticia') ? 'noticias_imagens' : 'agenda_imagens';
        $fk_field = ($type === 'noticia') ? 'noticia_id' : 'agenda_id';
        
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $fk_field = ? ORDER BY ordem_exibicao ASC, created_at DESC");
        $stmt->execute([$entity_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function update($pdo, $type, $image_id, $legenda, $descricao, $ordem = 0) {
        $table = ($type === 'noticia') ? 'noticias_imagens' : 'agenda_imagens';
        $stmt = $pdo->prepare("UPDATE $table SET legenda = ?, descricao = ?, ordem_exibicao = ? WHERE id = ?");
        return $stmt->execute([$legenda, $descricao, $ordem, $image_id]);
    }

    public static function delete($pdo, $type, $image_id) {
        $table = ($type === 'noticia') ? 'noticias_imagens' : 'agenda_imagens';
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$image_id]);
        $img = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($img) {
            $path = self::$upload_dir . $img['imagem'];
            if (file_exists($path)) unlink($path);
            
            $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
            return $stmt->execute([$image_id]);
        }
        return false;
    }
}
