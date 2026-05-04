<?php
require_once '../connect.php';

// Verificar se é uma requisição AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    if (!isset($_POST['offset'])) {
        http_response_code(403);
        exit('Acesso negado');
    }
}

header('Content-Type: application/json');

try {
    $offset = (int) ($_POST['offset'] ?? 0);
    $categoria = isset($_POST['categoria']) ? sanitize($_POST['categoria']) : '';
    $busca = isset($_POST['busca']) ? sanitize($_POST['busca']) : '';
    
    // Construir query
    $sql = "SELECT * FROM noticias WHERE ativo = 1";
    $params = [];
    
    if ($categoria) {
        $sql .= " AND categoria = ?";
        $params[] = $categoria;
    }
    
    if ($busca) {
        $sql .= " AND (titulo LIKE ? OR resumo LIKE ? OR conteudo LIKE ? OR tags LIKE ?)";
        $search_term = "%$busca%";
        $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
    }
    
    $sql .= " ORDER BY data_publicacao DESC LIMIT 10 OFFSET ?";
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $noticias = $stmt->fetchAll();
    
    if (empty($noticias)) {
        echo json_encode(['status' => 'fim', 'html' => '']);
        exit;
    }
    
    // Gerar HTML
    $html = '';
    foreach ($noticias as $noticia) {
        $imagem = $noticia->imagem_destaque ? "img/noticias/{$noticia->imagem_destaque}" : "img/Asset 7-100.jpg";
        $data_formatada = format_date($noticia->data_publicacao, 'd \d\e F \d\e Y');
        $resumo_truncado = truncate_text($noticia->resumo, 120);
        
        $html .= '
        <div class="col-md-6 wow slideInUp" data-wow-delay="0.1s">
            <div class="blog-item bg-light rounded overflow-hidden h-100">
                <div class="blog-img position-relative overflow-hidden">
                    <img class="img-fluid w-100" src="' . htmlspecialchars($imagem) . '" alt="' . htmlspecialchars($noticia->titulo) . '" style="height: 250px; object-fit: cover;">
                </div>
                <div class="p-4 d-flex flex-column h-100">
                    <h4 class="mb-3" style="margin:0px;color:#4D1C21;font-family: \'Libre Baskerville\'; font-weight: normal; font-size:180%;">
                        <a href="artigo.php?slug=' . htmlspecialchars($noticia->slug) . '" class="text-decoration-none" style="color:#4D1C21;">
                            ' . htmlspecialchars($noticia->titulo) . '
                        </a>
                    </h4>
                    <div class="d-flex mb-3">
                        <small style="color:#615759;font-family: \'Open Sans\'; font-weight: 300; font-style: normal;font-size:90%;">
                            ' . $data_formatada . '
                        </small>
                    </div>
                    <p style="color:#111923;font-family: \'Open Sans\'; font-weight: 600; font-style: normal;font-size:100%;" class="flex-grow-1">
                        ' . htmlspecialchars($resumo_truncado) . '
                    </p>
                    <div class="mt-auto" style="border-bottom:1px solid #111923;float:left;">
                        <a class="text-uppercase text-decoration-none" href="artigo.php?slug=' . htmlspecialchars($noticia->slug) . '" style="color:#111923;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="bi bi-arrow-right" style="color:#111923;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    echo json_encode(['status' => 'success', 'html' => $html]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao carregar notícias']);
}