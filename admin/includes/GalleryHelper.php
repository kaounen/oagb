<?php
// admin/includes/GalleryHelper.php

class GalleryHelper {
    private static $upload_dir = __DIR__ . '/../../uploads/';

    private static function getTableInfo($type) {
        if ($type === 'noticia') return ['table' => 'noticias_imagens', 'fk' => 'noticia_id'];
        if ($type === 'cidadaos') return ['table' => 'cidadaos_imagens', 'fk' => 'cidadaos_id'];
        if ($type === 'estagio' || $type === 'conteudos') return ['table' => 'conteudos_imagens', 'fk' => 'conteudo_id'];
        return ['table' => 'agenda_imagens', 'fk' => 'agenda_id'];
    }

    public static function save($pdo, $type, $entity_id, $files, $titles = [], $descriptions = []) {
        if (!file_exists(self::$upload_dir)) mkdir(self::$upload_dir, 0777, true);

        $info = self::getTableInfo($type);
        $table = $info['table'];
        $fk_field = $info['fk'];

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
        $info = self::getTableInfo($type);
        $table = $info['table'];
        $fk_field = $info['fk'];
        
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE $fk_field = ? ORDER BY ordem_exibicao ASC, created_at DESC");
        $stmt->execute([$entity_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function update($pdo, $type, $image_id, $legenda, $descricao, $ordem = 0) {
        $info = self::getTableInfo($type);
        $table = $info['table'];
        $stmt = $pdo->prepare("UPDATE $table SET legenda = ?, descricao = ?, ordem_exibicao = ? WHERE id = ?");
        return $stmt->execute([$legenda, $descricao, $ordem, $image_id]);
    }

    public static function delete($pdo, $type, $image_id) {
        $info = self::getTableInfo($type);
        $table = $info['table'];
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
