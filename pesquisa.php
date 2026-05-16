<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'connect.php';
require_once 'includes/functions.php';

/**
 * Função para criar um padrão de pesquisa "Fuzzy" (insensível a acentos)
 * Substitui vogais por '_' para permitir correspondência de caracteres acentuados ou corrompidos.
 */
function fuzzy_search_term($term) {
    if (empty($term)) return '%';
    $vowels = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ã', 'õ', 'â', 'ê', 'î', 'ô', 'û', 'ç', 'Ç'];
    $clean = str_replace($vowels, '_', $term);
    return '%' . $clean . '%';
}

// Obter parâmetros de pesquisa
$query = clean_input($_GET['q'] ?? '');
$tipo = clean_input($_GET['tipo'] ?? 'todos');
$data_inicio = clean_input($_GET['data_inicio'] ?? '');
$data_fim = clean_input($_GET['data_fim'] ?? '');
$pagina = max(1, intval($_GET['page'] ?? 1));
$ajax = isset($_GET['ajax']) && $_GET['ajax'] == '1';
$por_pagina = 12;
$offset = ($pagina - 1) * $por_pagina;

$resultados = [];
$total_resultados = 0;

if (!empty($query)) {
    try {
        $search_term = '%' . $query . '%';
        $fuzzy_term = fuzzy_search_term($query);
        $all_results = [];
        
        // 1. Pesquisa em NOTÍCIAS
        if ($tipo == 'todos' || $tipo == 'noticias') {
            $sql = "SELECT 'noticia' as tipo, id, titulo, resumo as descricao, slug, data_publicacao as data, imagem_destaque as imagem, NULL as info_extra FROM noticias WHERE ativo = 1 AND (titulo LIKE :s1 OR resumo LIKE :s2 OR conteudo LIKE :s3 OR titulo LIKE :f1 OR resumo LIKE :f2)";
            $params = [':s1' => $search_term, ':s2' => $search_term, ':s3' => $search_term, ':f1' => $fuzzy_term, ':f2' => $fuzzy_term];
            if (!empty($data_inicio) && !empty($data_fim)) { $sql .= " AND DATE(data_publicacao) BETWEEN :di AND :df"; $params[':di'] = $data_inicio; $params[':df'] = $data_fim; }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        
        // 2. Pesquisa em BASTONÁRIOS
        if ($tipo == 'todos' || $tipo == 'institucional') {
            $sql = "SELECT 'bastonario' as tipo, id, nome_completo as titulo, biografia as descricao, NULL as slug, data_inicio_mandato as data, foto_url as imagem, IF(is_atual=1, 'Bastonário Atual', 'Antigo Bastonário') as info_extra FROM bastonarios WHERE (nome_completo LIKE :s1 OR biografia LIKE :s2 OR nome_completo LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        
        // 3. Pesquisa em AGENDA / EVENTOS
        if ($tipo == 'todos' || $tipo == 'agenda') {
            $sql = "SELECT 'evento' as tipo, id, titulo, descricao, slug, data_evento as data, imagem_destaque as imagem, local_evento as info_extra FROM agenda WHERE ativo = 1 AND (titulo LIKE :s1 OR descricao LIKE :s2 OR local_evento LIKE :s3 OR titulo LIKE :f1)";
            $params = [':s1' => $search_term, ':s2' => $search_term, ':s3' => $search_term, ':f1' => $fuzzy_term];
            if (!empty($data_inicio) && !empty($data_fim)) { $sql .= " AND DATE(data_evento) BETWEEN :di AND :df"; $params[':di'] = $data_inicio; $params[':df'] = $data_fim; }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        
        // 4. Pesquisa em ADVOGADOS (Profissionais e Estagiários)
        if ($tipo == 'todos' || $tipo == 'advogados') {
            // Profissionais
            $sql = "SELECT 'advogado' as tipo, id, nome_completo as titulo, CONCAT('Nº Registo: ', numero_registo, ' | ', regiao) as descricao, numero_registo as slug, data_inscricao as data, foto as imagem, localidade as info_extra FROM advogados WHERE status = 'ativo' AND (nome_completo LIKE :s1 OR numero_registo LIKE :s2 OR regiao LIKE :s3 OR nome_completo LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':s3' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));

            // Estagiários
            $sql = "SELECT 'estagiario' as tipo, id, nome_completo as titulo, CONCAT('Estagiário - Nº Registo: ', numero_registo, ' | ', regiao) as descricao, numero_registo as slug, data_inicio_estagio as data, foto as imagem, localidade as info_extra FROM advogados_estagiarios WHERE status = 'ativo' AND (nome_completo LIKE :s1 OR numero_registo LIKE :s2 OR nome_completo LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        // 5. Pesquisa em DOCUMENTOS E PARECERES
        if ($tipo == 'todos' || $tipo == 'documentos') {
            // Documentos Públicos
            $sql = "SELECT 'documento' as tipo, id, titulo, descricao, arquivo as slug, data_documento as data, NULL as imagem, tipo as info_extra FROM documentos_publicos WHERE ativo = 1 AND (titulo LIKE :s1 OR descricao LIKE :s2 OR numero_documento LIKE :s3 OR titulo LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':s3' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));

            // Pareceres e Deliberações
            $sql = "SELECT 'parecer' as tipo, id, assunto as titulo, resumo as descricao, arquivo_pdf as slug, data_emissao as data, NULL as imagem, tipo as info_extra FROM pareceres_deliberacoes WHERE ativo = 1 AND (assunto LIKE :s1 OR resumo LIKE :s2 OR numero LIKE :s3 OR assunto LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':s3' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        // 6. Pesquisa em INSTITUCIONAL (Órgãos Sociais e Comissões)
        if ($tipo == 'todos' || $tipo == 'institucional') {
            // Órgãos Sociais
            $sql = "SELECT 'membro' as tipo, id, nome as titulo, cargo as descricao, NULL as slug, created_at as data, foto as imagem, 'Órgão Social' as info_extra FROM orgaos_sociais WHERE ativo = 1 AND (nome LIKE :s1 OR cargo LIKE :s2 OR biografia LIKE :s3 OR nome LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':s3' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));

            // Comissões
            $sql = "SELECT 'comissao' as tipo, id, nome as titulo, descricao, NULL as slug, created_at as data, NULL as imagem, presidente as info_extra FROM comissoes WHERE ativo = 1 AND (nome LIKE :s1 OR descricao LIKE :s2 OR presidente LIKE :s3 OR nome LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':s3' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));

            // Páginas Estáticas
            $sql = "SELECT 'pagina' as tipo, id, titulo, SUBSTRING(conteudo, 1, 300) as descricao, slug, created_at as data, imagem as imagem, NULL as info_extra FROM paginas_ordem WHERE ativo = 1 AND (titulo LIKE :s1 OR conteudo LIKE :s2 OR titulo LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        // 7. Pesquisa em ANÚNCIOS
        if ($tipo == 'todos' || $tipo == 'anuncios') {
            $sql = "SELECT 'anuncio' as tipo, id, titulo, descricao, link_url as slug, created_at as data, imagem as imagem, NULL as info_extra FROM anuncios WHERE ativo = 1 AND (titulo LIKE :s1 OR descricao LIKE :s2 OR titulo LIKE :f1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':s1' => $search_term, ':s2' => $search_term, ':f1' => $fuzzy_term]);
            $all_results = array_merge($all_results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        
        // Remover duplicados por ID e Tipo
        $temp = [];
        $unique_results = [];
        foreach ($all_results as $res) {
            $key = $res['tipo'] . '_' . $res['id'];
            if (!isset($temp[$key])) {
                $temp[$key] = true;
                $unique_results[] = $res;
            }
        }
        $all_results = $unique_results;

        // Ordenação Global
        usort($all_results, function($a, $b) { 
            return strtotime($b['data'] ?? '0000-00-00') - strtotime($a['data'] ?? '0000-00-00'); 
        });
        
        $total_resultados = count($all_results);
        $resultados = array_slice($all_results, $offset, $por_pagina);
        $resultados = array_map(function($item) { return (object) $item; }, $resultados);
        
    } catch (Exception $e) { error_log("Erro na pesquisa: " . $e->getMessage()); }
}

if ($ajax) {
    foreach ($resultados as $resultado) {
        $link = '#'; $badge_class = 'badge-primary'; $label = ucfirst($resultado->tipo);
        switch($resultado->tipo) {
            case 'noticia': $link = "artigo.php?id={$resultado->id}&slug=" . urlencode($resultado->slug ?? ''); $badge_class = 'badge-noticia'; break;
            case 'evento': $link = "evento.php?id={$resultado->id}"; $badge_class = 'badge-evento'; break;
            case 'advogado': $link = "advogado-detalhe.php?id={$resultado->id}"; $badge_class = 'badge-advogado'; break;
            case 'estagiario': $link = "estagiario-detalhe.php?id={$resultado->id}"; $badge_class = 'badge-advogado'; $label = "Adv. Estagiário"; break;
            case 'bastonario': $link = "bastonario-ordem.php"; $badge_class = 'bg-primary'; $label = "Bastonário"; break;
            case 'membro': $link = "orgaos-sociais.php"; $badge_class = 'bg-primary'; $label = "Direção"; break;
            case 'comissao': $link = "comissoes-especializadas.php"; $badge_class = 'bg-success'; $label = "Comissão"; break;
            case 'anuncio': $link = !empty($resultado->slug) ? $resultado->slug : "anuncios.php?id={$resultado->id}"; $badge_class = 'bg-info'; break;
            case 'documento': case 'parecer': $link = !empty($resultado->slug) ? "uploads/" . $resultado->slug : "documentos.php?id={$resultado->id}"; $badge_class = 'bg-dark'; $label = !empty($resultado->info_extra) ? ucfirst($resultado->info_extra) : "Documento"; break;
            case 'pagina': $link = "apresentacao-historia.php?slug=" . ($resultado->slug ?? ''); $badge_class = 'bg-secondary'; $label = "Institucional"; break;
            case 'faq': $link = "contacto.php#faq-{$resultado->id}"; $badge_class = 'bg-warning text-dark'; $label = "FAQ"; break;
        }
        ?>
        <div class="result-card mb-4 shadow-sm">
            <div class="row g-0">
                <?php if (!empty($resultado->imagem) && !in_array($resultado->tipo, ['faq', 'documento', 'parecer'])): ?>
                <div class="col-md-3">
                    <div class="result-img-wrapper">
                        <?php 
                        $img_src = trim($resultado->imagem);
                        // Compatibilidade com PHP < 8 (str_contains/str_starts_with)
                        $is_absolute = (strpos($img_src, 'http') !== false);
                        $has_path = (strpos($img_src, 'uploads/') === 0 || strpos($img_src, 'img/') === 0);
                        
                        if (!$is_absolute && !$has_path) { 
                            $img_src = 'uploads/' . ltrim($img_src, '/'); 
                        }
                        ?>
                        <img src="<?php echo $img_src; ?>" class="result-img" alt="<?php echo htmlspecialchars($resultado->titulo); ?>">
                    </div>
                </div>
                <div class="col-md-9">
                <?php else: ?>
                <div class="col-md-12">
                <?php endif; ?>
                    <div class="result-content">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge-custom <?php echo $badge_class; ?> mb-3"><?php echo $label; ?></span>
                                <h4 class="result-title">
                                    <a href="<?php echo $link; ?>">
                                        <?php 
                                        // Fix encoding if needed (for Mojibake cases)
                                        $display_title = $resultado->titulo;
                                        if (strpos($display_title, 'ß') !== false || strpos($display_title, 'Ý') !== false) {
                                            $search  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾'];
                                            $replace = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó'];
                                            $display_title = str_replace($search, $replace, $display_title);
                                        }
                                        echo htmlspecialchars($display_title); 
                                        ?>
                                    </a>
                                </h4>
                            </div>
                            <a href="<?php echo $link; ?>" class="btn-detail-circle"><i class="bi bi-arrow-right"></i></a>
                        </div>
                        <p class="result-desc mb-3">
                            <?php 
                            $display_desc = strip_tags($resultado->descricao);
                            if (strpos($display_desc, 'ß') !== false || strpos($display_desc, 'Ý') !== false) {
                                $search  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾'];
                                $replace = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó'];
                                $display_desc = str_replace($search, $replace, $display_desc);
                            }
                            echo htmlspecialchars(truncate_text($display_desc, 300)); 
                            ?>
                        </p>
                        <div class="result-meta d-flex flex-wrap gap-4 mt-auto">
                            <?php if (!empty($resultado->data)): ?>
                            <span><i class="far fa-calendar-alt me-2 text-gold"></i><?php echo format_date_pt($resultado->data); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($resultado->info_extra)): ?>
                            <span><i class="fa fa-info-circle me-2 text-gold"></i><?php echo htmlspecialchars($resultado->info_extra); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    exit;
}

$total_paginas = ceil($total_resultados / $por_pagina);
$page_title = "Pesquisa Geral";
$header_image = 'uploads/close-up-scales-justice-original-azul.jpg';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">

    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; }
        html, body { overflow-x: hidden !important; width: 100%; margin: 0; padding: 0; }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem !important; opacity: 1 !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }
        .quick-links a { width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3); display: inline-flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }

        @media (max-width: 991px) {
            .mobile-breadcrumb-bar { background: transparent; padding: 10px 0; position: absolute; bottom: 0; left: 0; right: 0; z-index: 1045 !important; }
            .mobile-breadcrumb-bar a, .mobile-breadcrumb-bar span { font-size: 0.72rem; color: #fff; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); }
            .mobile-breadcrumb-bar .quick-links a { border-color: rgba(255,255,255,0.4); color: #fff; width: 28px; height: 28px; font-size: 0.65rem; }
        }

        .search-hero { background: #fff; border-radius: 20px; padding: 40px; border: 1px solid #f0ece4; box-shadow: 0 15px 45px rgba(0,0,0,0.03); margin-top: -30px; position: relative; z-index: 20; }
        .search-title { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 1.3rem; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
        .search-title i { color: var(--primary-gold); }

        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-maroon); }
        .form-control, .form-select { border-radius: 12px; border: 1px solid #eee; padding: 12px 18px; font-size: 0.9rem; transition: .3s; background: #fbfbfb; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); background: #fff; box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.1); }
        
        .btn-filter { background: var(--primary-maroon); color: #fff; border-radius: 12px; height: 50px; font-weight: 700; border: none; transition: .3s; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-filter:hover { background: var(--primary-gold); transform: translateY(-2px); }
        .btn-clear { background: #eee; color: #666; border-radius: 12px; height: 50px; font-weight: 700; border: none; transition: .3s; width: 100%; }
        .btn-clear:hover { background: #ddd; }

        .result-count-bar { padding: 20px 0; margin-bottom: 30px; border-bottom: 2px solid #f0ece4; display: flex; justify-content: space-between; align-items: center; }
        .result-count-text { font-family: 'Libre Baskerville', serif; color: #5B463F; font-size: 1.2rem; }
        .result-count-num { color: var(--primary-maroon); font-weight: 700; }

        .result-card { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #f0ece4; transition: .3s; box-shadow: 0 5px 15px rgba(0,0,0,0.015); height: 100%; }
        .result-card:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(0,0,0,0.06); }
        .result-img-wrapper { height: 100%; min-height: 220px; position: relative; overflow: hidden; background: #f9f9f9; }
        .result-img { width: 100%; height: 100%; object-fit: cover; transition: .6s; }
        .result-card:hover .result-img { transform: scale(1.08); }
        .result-content { padding: 30px; height: 100%; display: flex; flex-direction: column; }
        .result-title { font-family: 'Libre Baskerville', serif; font-size: 1.2rem; color: var(--primary-maroon); font-weight: 700; margin-bottom: 12px; line-height: 1.4; }
        .result-title a { color: inherit; text-decoration: none; transition: .3s; }
        .result-title a:hover { color: var(--primary-gold); }
        .result-desc { font-size: 0.9rem; color: #555; line-height: 1.6; }
        .result-meta { font-size: 0.78rem; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-top: auto; border-top: 1px solid #f9f6f0; padding-top: 15px; }
        .text-gold { color: var(--primary-gold) !important; }

        .badge-custom { padding: 6px 14px; border-radius: 50px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff; display: inline-block; }
        .badge-noticia { background: #c18046; }
        .badge-evento { background: #f37263; }
        .badge-advogado { background: var(--primary-maroon); }

        .btn-detail-circle { width: 44px; height: 44px; border-radius: 50%; background: #fdfbf7; border: 1px solid #f0ece4; display: flex; align-items: center; justify-content: center; color: var(--primary-maroon); transition: .3s; text-decoration: none; flex-shrink: 0; }
        .btn-detail-circle:hover { background: var(--primary-maroon); color: #fff; border-color: var(--primary-maroon); transform: rotate(-45deg); }

        .empty-state { text-align: center; padding: 100px 20px; background: #fff; border-radius: 20px; border: 1px dashed #ddd; }
        .empty-icon { font-size: 5rem; color: #f0ece4; margin-bottom: 25px; }
        .empty-title { font-family: 'Libre Baskerville', serif; color: #999; font-size: 1.4rem; }

        .loading-bar { display: none; text-align: center; padding: 40px; }
        .loading-bar.active { display: block; }
    </style>
</head>

<body>
<div style="overflow-x: hidden; width: 100%; position: relative;">

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('<?php echo $header_image; ?>') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Pesquisa Pesquisa Inteligente</span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                        <a href="#" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}"><i class="fas fa-share-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <?php 
    $mobile_breadcrumbs = [
        ['label' => 'Início', 'url' => 'index.php'],
        ['label' => 'Pesquisa', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <section class="py-5" style="background: #fdfbf7;">
        <div class="container py-lg-4">
            
            <div class="search-hero mb-5 wow fadeInUp">
                <h3 class="search-title"><i class="fas fa-search-plus"></i> Sistema de Pesquisa Abrangente</h3>
                <form method="GET" action="pesquisa.php" id="filter-form">
                    <div class="row g-3">
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label">O que procura? (Insensível a acentos)</label>
                            <input type="text" name="q" class="form-control" placeholder="Ex: Januario ou Januário" value="<?php echo htmlspecialchars($query); ?>" required>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Filtrar por Categoria</label>
                            <select name="tipo" class="form-select">
                                <option value="todos" <?php echo $tipo == 'todos' ? 'selected' : ''; ?>>Tudo</option>
                                <option value="noticias" <?php echo $tipo == 'noticias' ? 'selected' : ''; ?>>Notícias</option>
                                <option value="agenda" <?php echo $tipo == 'agenda' ? 'selected' : ''; ?>>Eventos</option>
                                <option value="advogados" <?php echo $tipo == 'advogados' ? 'selected' : ''; ?>>Advogados</option>
                                <option value="documentos" <?php echo $tipo == 'documentos' ? 'selected' : ''; ?>>Documentos</option>
                                <option value="institucional" <?php echo $tipo == 'institucional' ? 'selected' : ''; ?>>Liderança / Órgãos</option>
                                <option value="faq" <?php echo $tipo == 'faq' ? 'selected' : ''; ?>>FAQ</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="pt-lg-4 mt-lg-1">
                                <button type="submit" class="btn-filter w-100">PROCURAR <i class="fas fa-search ms-1"></i></button>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="pt-lg-4 mt-lg-1">
                                <a href="pesquisa.php" class="btn btn-clear d-flex align-items-center justify-content-center"><i class="fas fa-sync-alt me-2"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (!empty($query)): ?>
                <div class="result-count-bar">
                    <h5 class="result-count-text">
                        Exibindo resultados para: <span class="text-maroon">"<?php echo htmlspecialchars($query); ?>"</span>
                    </h5>
                    <span class="badge bg-white text-dark p-2 rounded-3 border">
                        <span class="result-count-num"><?php echo $total_resultados; ?></span> itens
                    </span>
                </div>

                <div id="results-container">
                    <?php if (!empty($resultados)): ?>
                        <?php foreach ($resultados as $resultado): ?>
                            <?php
                            $link = '#'; $badge_class = 'badge-primary'; $label = ucfirst($resultado->tipo);
                            switch($resultado->tipo) {
                                case 'noticia': $link = "artigo.php?id={$resultado->id}&slug=" . urlencode($resultado->slug ?? ''); $badge_class = 'badge-noticia'; break;
                                case 'evento': $link = "evento.php?id={$resultado->id}"; $badge_class = 'badge-evento'; break;
                                case 'advogado': $link = "advogado-detalhe.php?id={$resultado->id}"; $badge_class = 'badge-advogado'; break;
                                case 'estagiario': $link = "estagiario-detalhe.php?id={$resultado->id}"; $badge_class = 'badge-advogado'; $label = "Adv. Estagiário"; break;
                                case 'bastonario': $link = "bastonario-ordem.php"; $badge_class = 'bg-primary'; $label = "Bastonário"; break;
                                case 'membro': $link = "orgaos-sociais.php"; $badge_class = 'bg-primary'; $label = "Direção"; break;
                                case 'comissao': $link = "comissoes-especializadas.php"; $badge_class = 'bg-success'; $label = "Comissão"; break;
                                case 'anuncio': $link = !empty($resultado->slug) ? $resultado->slug : "anuncios.php?id={$resultado->id}"; $badge_class = 'bg-info'; break;
                                case 'documento': case 'parecer': $link = !empty($resultado->slug) ? "uploads/" . $resultado->slug : "documentos.php?id={$resultado->id}"; $badge_class = 'bg-dark'; $label = !empty($resultado->info_extra) ? ucfirst($resultado->info_extra) : "Documento"; break;
                                case 'pagina': $link = "apresentacao-historia.php?slug=" . ($resultado->slug ?? ''); $badge_class = 'bg-secondary'; $label = "Institucional"; break;
                                case 'faq': $link = "contacto.php#faq-{$resultado->id}"; $badge_class = 'bg-warning text-dark'; $label = "FAQ"; break;
                            }
                            ?>
                            <div class="result-card mb-4 shadow-sm">
                                <div class="row g-0">
                                    <?php if (!empty($resultado->imagem) && !in_array($resultado->tipo, ['faq', 'documento', 'parecer'])): ?>
                                    <div class="col-md-3">
                                        <div class="result-img-wrapper">
                                            <?php 
                                            $img_src = trim($resultado->imagem);
                                            // Compatibilidade com PHP < 8
                                            $is_absolute = (strpos($img_src, 'http') !== false);
                                            $has_path = (strpos($img_src, 'uploads/') === 0 || strpos($img_src, 'img/') === 0);
                                            
                                            if (!$is_absolute && !$has_path) { 
                                                $img_src = 'uploads/' . ltrim($img_src, '/'); 
                                            }
                                            ?>
                                            <img src="<?php echo $img_src; ?>" class="result-img" alt="<?php echo htmlspecialchars($resultado->titulo); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                    <?php else: ?>
                                    <div class="col-md-12">
                                    <?php endif; ?>
                                        <div class="result-content">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge-custom <?php echo $badge_class; ?> mb-3"><?php echo $label; ?></span>
                                                    <h4 class="result-title">
                                                        <a href="<?php echo $link; ?>" <?php echo (in_array($resultado->tipo, ['documento', 'parecer'])) ? 'target="_blank"' : ''; ?>>
                                                            <?php 
                                                            $display_title = $resultado->titulo;
                                                            if (strpos($display_title, 'ß') !== false || strpos($display_title, 'Ý') !== false) {
                                                                $search_moj  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾'];
                                                                $replace_moj = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó'];
                                                                $display_title = str_replace($search_moj, $replace_moj, $display_title);
                                                            }
                                                            echo htmlspecialchars($display_title); 
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <a href="<?php echo $link; ?>" class="btn-detail-circle" <?php echo (in_array($resultado->tipo, ['documento', 'parecer'])) ? 'target="_blank"' : ''; ?>><i class="bi bi-arrow-right"></i></a>
                                            </div>
                                            <p class="result-desc mb-3">
                                                <?php 
                                                $display_desc = strip_tags($resultado->descricao);
                                                if (strpos($display_desc, 'ß') !== false || strpos($display_desc, 'Ý') !== false) {
                                                    $search_moj  = ['þÒ', 'þ', 'Ú', 'Ò', 'Ý', 'ß', '¾'];
                                                    $replace_moj = ['ção', 'ç', 'é', 'ã', 'í', 'á', 'ó'];
                                                    $display_desc = str_replace($search_moj, $replace_moj, $display_desc);
                                                }
                                                echo htmlspecialchars(truncate_text($display_desc, 300)); 
                                                ?>
                                            </p>
                                            <div class="result-meta d-flex flex-wrap gap-4 mt-auto">
                                                <?php if (!empty($resultado->data)): ?>
                                                <span><i class="far fa-calendar-alt me-2 text-gold"></i><?php echo format_date_pt($resultado->data); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($resultado->info_extra)): ?>
                                                <span><i class="fa fa-info-circle me-2 text-gold"></i><?php echo htmlspecialchars($resultado->info_extra); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-search-minus empty-icon"></i>
                            <h4 class="empty-title">Sem resultados para "<?php echo htmlspecialchars($query); ?>"</h4>
                            <p class="text-muted mt-3">Experimente remover acentos ou usar termos mais simples.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="loading-bar" id="loading-spinner">
                    <div class="spinner-border text-gold" role="status">
                        <span class="visually-hidden">Procurando...</span>
                    </div>
                </div>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search empty-icon" style="opacity: 0.1;"></i>
                    <h4 class="empty-title">Central de Pesquisa Inteligente</h4>
                    <p class="text-muted mt-3 mx-auto" style="max-width: 500px;">
                        Pesquise em toda a plataforma OAGB. O sistema agora é inteligente e ignora diferenças em acentuação para encontrar o que precisa.
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="js/main.js?v=<?php echo time(); ?>"></script>

    <script>
    <?php if (!empty($query) && $total_paginas > 1): ?>
    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;
    
    window.addEventListener('scroll', function() {
        if (isLoading || !hasMore) return;
        if (window.innerHeight + window.scrollY >= document.documentElement.offsetHeight - 400) {
            loadMoreResults();
        }
    });
    
    function loadMoreResults() {
        isLoading = true;
        currentPage++;
        document.getElementById('loading-spinner').classList.add('active');
        
        const params = new URLSearchParams(window.location.search);
        params.set('page', currentPage);
        params.set('ajax', '1');
        
        fetch('pesquisa.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                if (html.trim()) {
                    document.getElementById('results-container').insertAdjacentHTML('beforeend', html);
                    if (currentPage >= <?php echo $total_paginas; ?>) hasMore = false;
                } else {
                    hasMore = false;
                }
                document.getElementById('loading-spinner').classList.remove('active');
                isLoading = false;
            })
            .catch(error => {
                console.error('Erro na carga:', error);
                document.getElementById('loading-spinner').classList.remove('active');
                isLoading = false;
            });
    }
    <?php endif; ?>
    </script>
</div>
</body>
</html>
