<?php
// admin/includes/LogHelper.php

class LogHelper {
    public static function log($pdo, $action, $description = '', $affected_table = '', $record_id = NULL) {
        if (!isset($_SESSION['admin_id'])) return false;

        $user_id = $_SESSION['admin_id'];
        $user_name = $_SESSION['admin_name'] ?? 'Admin';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        try {
            $stmt = $pdo->prepare("INSERT INTO logs_atividade (usuario_id, usuario_nome, acao, descricao, tabela_afetada, registro_id, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            return $stmt->execute([
                $user_id,
                $user_name,
                $action,
                $description,
                $affected_table,
                $record_id,
                $ip,
                $ua
            ]);
        } catch (PDOException $e) {
            error_log("Logging Error: " . $e->getMessage());
            return false;
        }
    }

    // Quick methods
    public static function create($pdo, $table, $id, $details = '') {
        return self::log($pdo, 'CREATE', "Criou novo registo em $table: $details", $table, $id);
    }

    public static function update($pdo, $table, $id, $details = '') {
        return self::log($pdo, 'UPDATE', "Atualizou registo #$id em $table: $details", $table, $id);
    }

    public static function delete($pdo, $table, $id, $details = '') {
        return self::log($pdo, 'DELETE', "Eliminou registo #$id em $table: $details", $table, $id);
    }

    public static function login($pdo) {
        return self::log($pdo, 'LOGIN', "Utilizador entrou no sistema.");
    }
}
?>
