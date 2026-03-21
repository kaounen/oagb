<?php
/**
 * OAGB 2.0 Mail Helper
 * Centralized mail engine for the Order.
 */

class MailHelper {
    private static $from = 'no-reply@oagb.gw';
    private static $name = 'Ordem dos Advogados da Guiné-Bissau 2.0';

    /**
     * Sends a simple HTML email.
     * Note: Requires sendmail or SMTP correctly configured in php.ini
     */
    public static function send($to, $subject, $messageHTML) {
        $headers  = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . self::$name . ' <' . self::$from . '>' . "\r\n";
        $headers .= 'Reply-To: ' . self::$from . "\r\n";
        
        // Wrap message in a premium template
        $fullBody = "
        <div style='background-color: #f5f6f8; padding: 40px; font-family: sans-serif; color: #111923;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                <div style='background-color: #111923; padding: 30px; text-align: center;'>
                    <img src='https://oagb.gw/img/logo3.png' alt='OAGB' style='height: 50px; filter: brightness(0) invert(1);'>
                </div>
                <div style='padding: 40px;'>
                    $messageHTML
                </div>
                <div style='background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #eeeeee; font-size: 11px; color: #999999;'>
                    Este é um e-mail institucional automático da OAGB. Por favor, não responda a esta mensagem.<br>
                    © " . date('Y') . " Ordem dos Advogados da Guiné-Bissau. Todos os direitos reservados.
                </div>
            </div>
        </div>";

        return mail($to, $subject, $fullBody, $headers);
    }

    public static function sendAlertQuota($email, $nome, $mesReferencia, $valor) {
        $subject = "AVISO DE PAGAMENTO: Quota Mensal - OAGB";
        $message = "
            <h2 style='color: #B1A276; margin-bottom: 20px;'>Olá, Dr(a). $nome,</h2>
            <p style='font-size: 16px; line-height: 1.6;'>Informamos que o nosso sistema de tesouraria identificou a ausência de pagamento da sua <strong>Quota Mensal</strong> referente a <strong>$mesReferencia</strong>.</p>
            <p style='font-size: 16px; line-height: 1.6;'>A regularização das quotas é fundamental para a manutenção das atividades da Ordem e para o gozo pleno dos seus direitos profissionais.</p>
            <div style='background-color: #fcf8e3; border: 1px solid #faebcc; padding: 20px; border-radius: 8px; margin: 30px 0;'>
                <strong style='color: #8a6d3b;'>Valor em Aberto: $valor CFA</strong><br>
                <span style='font-size: 13px;'>Pode realizar o pagamento por transferência bancária ou directamente na secretaria da Ordem.</span>
            </div>
            <p style='font-size: 14px; color: #666;'>Caso já tenha realizado o pagamento e anexado o comprovativo no portal, por favor ignore este aviso.</p>
            <div style='margin-top: 40px; text-align: center;'>
                <a href='https://oagb.gw/portal' style='background-color: #B1A276; color: #ffffff; padding: 15px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;'>ACEDER AO MEU PORTAL</a>
            </div>
        ";
        return self::send($email, $subject, $message);
    }
}
?>
