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
     * Envia e-mail institucional.
     */
    public static function sendEmail($pdo, $to_email, $subject, $body) {
        try {
            $from_email = defined('FROM_EMAIL') ? FROM_EMAIL : 'comunicacao@oagb.gw';
            $from_name = defined('FROM_NAME') ? FROM_NAME : 'OAGB Institucional';
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: $from_name <$from_email>" . "\r\n";

            // Queue for audit
            $stmt = $pdo->prepare("INSERT INTO gestao_notificacoes (destinatario_id, tipo, mensagem, status) VALUES (0, 'email', ?, 'pendente')");
            $stmt->execute(["Para: $to_email | Assunto: $subject"]);
            
            // Send
            return @mail($to_email, $subject, $body, $headers);
        } catch (Exception $e) { return false; }
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
