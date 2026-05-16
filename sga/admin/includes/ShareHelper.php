<?php
// admin/includes/ShareHelper.php

class ShareHelper {
    /**
     * Gera link de partilha para o Facebook
     */
    public static function facebook($url) {
        return "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($url);
    }

    /**
     * Gera link para partilha no WhatsApp
     */
    public static function whatsapp($text, $url) {
        return "https://api.whatsapp.com/send?text=" . urlencode($text . " " . $url);
    }

    /**
     * Gera link para o LinkedIn
     */
    public static function linkedin($url) {
        return "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($url);
    }

    /**
     * Construtor de pré-visualização de partilha (Link do Site Público)
     */
    public static function getPublicUrl($module, $slug_or_id) {
        $base = "https://oagb.gw/"; // URL Base real do site
        switch($module) {
            case 'noticias': return $base . "noticia.php?id=" . $slug_or_id;
            case 'agenda': return $base . "evento.php?id=" . $slug_or_id;
            default: return $base;
        }
    }
}
?>
