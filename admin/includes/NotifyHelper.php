<?php
/**
 * OAGB 2.0 NOTIFICATION ENGINE
 * Powering Communication via SMS, Push and Email.
 */

class NotifyHelper {
    
    /**
     * Envia uma notificação por SMS via Fila de Espera (Database).
     * Integração futura com Gateways (Twilio, Nexmo, BulkSMS).
     */
    public static function sendSMS($pdo, $adv_id, $mensagem) {
        try {
            // Get Phone
            $stmt = $pdo->prepare("SELECT telefone FROM advogados WHERE id = ?");
            $stmt->execute([$adv_id]);
            $phone = $stmt->fetchColumn();
            
            if (!$phone) return false;
            
            // Log in Queue
            $stmt = $pdo->prepare("INSERT INTO gestao_notificacoes (destinatario_id, tipo, mensagem, status) VALUES (?, 'sms', ?, 'pendente')");
            $stmt->execute([$adv_id, $mensagem]);
            
            // HOOK: Call External API here
            // self::callSMSGateway($phone, $mensagem);

            return true;
        } catch (Exception $e) { return false; }
    }

    /**
     * Envia Alerta de Quotas em Atraso por SMS.
     */
    public static function notifyPendingQuota($pdo, $adv_id) {
        $msg = "Aviso OAGB: Constatamos quotas em atraso. Regularize a sua situacao profissional no portal ou na secretaria. Obrigado.";
        return self::sendSMS($pdo, $adv_id, $msg);
    }
    
    /**
     * Envia Alerta de Eleições.
     */
    public static function notifyElection($pdo, $adv_id, $eleicao_nome) {
        $msg = "Aviso OAGB: O ato eleitoral '$eleicao_nome' esta a decorrer. Exerca o seu direito de voto no portal do advogado. O seu voto conta!";
        return self::sendSMS($pdo, $adv_id, $msg);
    }
}
?>
