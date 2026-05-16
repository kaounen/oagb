<?php
require_once 'connect.php';

try {
    // 1. Fix Timeline encoding
    $stmt = $pdo->query("SELECT id, titulo, descricao FROM timeline_marcos");
    $marcos = $stmt->fetchAll();
    
    foreach ($marcos as $m) {
        $fixed_titulo = str_replace(['FundaþÒo', 'CriaþÒo', 'GuinÚ-Bissau'], ['Fundação', 'Criação', 'Guiné-Bissau'], $m->titulo);
        $fixed_desc = str_replace(['FundaþÒo', 'CriaþÒo', 'GuinÚ-Bissau'], ['Fundação', 'Criação', 'Guiné-Bissau'], $m->descricao);
        
        if ($fixed_titulo !== $m->titulo || $fixed_desc !== $m->descricao) {
            $upd = $pdo->prepare("UPDATE timeline_marcos SET titulo = ?, descricao = ? WHERE id = ?");
            $upd->execute([$fixed_titulo, $fixed_desc, $m->id]);
            echo "Fixed Marco ID: {$m->id}<br>";
        }
    }
    
    // 2. Fix Institutional Info
    $stmt = $pdo->query("SELECT * FROM instituicao_info LIMIT 1");
    $info = $stmt->fetch();
    if ($info) {
        $fields = ['historia', 'missao', 'visao', 'valores'];
        $vals = [];
        foreach ($fields as $f) {
            $vals[$f] = str_replace(['FundaþÒo', 'CriaþÒo', 'GuinÚ-Bissau'], ['Fundação', 'Criação', 'Guiné-Bissau'], $info->$f);
        }
        $upd = $pdo->prepare("UPDATE instituicao_info SET historia = ?, missao = ?, visao = ?, valores = ? WHERE id = ?");
        $upd->execute([$vals['historia'], $vals['missao'], $vals['visao'], $vals['valores'], $info->id]);
        echo "Fixed Institutional Info<br>";
    }

    echo "Repair completed successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
