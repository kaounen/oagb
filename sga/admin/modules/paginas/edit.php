<?php
require_once __DIR__ . '/../../includes/db.php';

$id = $_GET['id'] ?? 0;
$slug_get = $_GET['slug'] ?? '';

if ($slug_get) {
    $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE slug = ?");
    $stmt->execute([$slug_get]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$p) {
        // Auto-create missing page if accessed via slug
        $stmt = $pdo->prepare("INSERT INTO paginas_ordem (titulo, slug, conteudo) VALUES (?, ?, ?)");
        $stmt->execute([ucfirst($slug_get), $slug_get, 'Conteúdo a ser definido...']);
        $id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $id = $p['id'];
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM paginas_ordem WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(!$p) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $slug = $_POST['slug'];
    $conteudo = $_POST['conteudo'];
    $ordem = (int)$_POST['ordem_exibicao'];
    $exibir_menu = isset($_POST['exibir_menu']) ? 1 : 0;
    $menu_categoria = $_POST['menu_categoria'];
    $ordem_menu = (int)$_POST['ordem_menu'];
    $layout_tipo = $_POST['layout_tipo'];
    $imagem_posicao = $_POST['imagem_posicao'];
    $mostrar_sidebar = isset($_POST['mostrar_sidebar']) ? 1 : 0;
    $mostrar_botoes = isset($_POST['mostrar_botoes']) ? 1 : 0;
    $card_bg = isset($_POST['card_bg']) ? 1 : 0;
    $parallax = isset($_POST['parallax']) ? 1 : 0;
    $titulo_cor = $_POST['titulo_cor'];
    $titulo_tamanho = $_POST['titulo_tamanho'];
    $texto_cor = $_POST['texto_cor'];
    $texto_tamanho = $_POST['texto_tamanho'];
    $fonte_familia = $_POST['fonte_familia'];
    $sidebar_conteudo = $_POST['sidebar_conteudo'];
    $botao1_texto = $_POST['botao1_texto'];
    $botao1_link = $_POST['botao1_link'];
    $botao2_texto = $_POST['botao2_texto'];
    $botao2_link = $_POST['botao2_link'];
    $sidebar_widget = $_POST['sidebar_widget'];
    $sidebar_menu_categoria = $_POST['sidebar_menu_categoria'];
    $sidebar_titulo = $_POST['sidebar_titulo'];
    $sidebar_icon = $_POST['sidebar_icon'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    // Handle Header Image Upload
    $imagem = $p['imagem'];
    if (!empty($_FILES['imagem']['name'])) {
        $target_dir = __DIR__ . '/../../../uploads/paginas/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $new_name = 'header_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_dir . $new_name)) { $imagem = $new_name; }
    }

    // Handle Card Image Upload
    $imagem_card = $p['imagem_card'];
    if (!empty($_FILES['imagem_card']['name'])) {
        $target_dir = __DIR__ . '/../../../uploads/paginas/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['imagem_card']['name'], PATHINFO_EXTENSION);
        $new_name = 'card_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['imagem_card']['tmp_name'], $target_dir . $new_name)) { $imagem_card = $new_name; }
    }

    // Handle Button File Uploads
    $botao1_file = $p['botao1_file'];
    if (!empty($_FILES['botao1_file']['name'])) {
        $target_dir = __DIR__ . '/../../../uploads/paginas/docs/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $new_name = 'doc1_' . time() . '_' . $_FILES['botao1_file']['name'];
        if (move_uploaded_file($_FILES['botao1_file']['tmp_name'], $target_dir . $new_name)) { $botao1_file = $new_name; }
    }

    $botao2_file = $p['botao2_file'];
    if (!empty($_FILES['botao2_file']['name'])) {
        $target_dir = __DIR__ . '/../../../uploads/paginas/docs/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $new_name = 'doc2_' . time() . '_' . $_FILES['botao2_file']['name'];
        if (move_uploaded_file($_FILES['botao2_file']['tmp_name'], $target_dir . $new_name)) { $botao2_file = $new_name; }
    }

    try {
        $stmt = $pdo->prepare("UPDATE paginas_ordem SET titulo=?, slug=?, conteudo=?, ordem_exibicao=?, ativo=?, exibir_menu=?, menu_categoria=?, ordem_menu=?, layout_tipo=?, imagem_posicao=?, mostrar_sidebar=?, mostrar_botoes=?, card_bg=?, parallax=?, titulo_cor=?, titulo_tamanho=?, texto_cor=?, texto_tamanho=?, fonte_familia=?, imagem=?, imagem_card=?, sidebar_conteudo=?, botao1_texto=?, botao1_link=?, botao2_texto=?, botao2_link=?, botao1_file=?, botao2_file=?, sidebar_widget=?, sidebar_menu_categoria=?, sidebar_titulo=?, sidebar_icon=? WHERE id=?");
        $stmt->execute([$titulo, $slug, $conteudo, $ordem, $ativo, $exibir_menu, $menu_categoria, $ordem_menu, $layout_tipo, $imagem_posicao, $mostrar_sidebar, $mostrar_botoes, $card_bg, $parallax, $titulo_cor, $titulo_tamanho, $texto_cor, $texto_tamanho, $fonte_familia, $imagem, $imagem_card, $sidebar_conteudo, $botao1_texto, $botao1_link, $botao2_texto, $botao2_link, $botao1_file, $botao2_file, $sidebar_widget, $sidebar_menu_categoria, $sidebar_titulo, $sidebar_icon, $id]);

        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'PAGE_UPDATE', "Atualizou a página institucional: $titulo", 'paginas_ordem', $id);

        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Erro ao atualizar página: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Editar Página: <?php echo $p['titulo']; ?></h2>
        <div class="text-muted small">Personalize o layout e conteúdo avançado desta secção.</div>
    </div>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm p-5 bg-white mb-5">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4 mb-4 border-bottom pb-4">
            <div class="col-md-3 text-center border-end">
                <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">Foto Cabeçalho</label>
                <div class="p-3 rounded-4 bg-light mb-2 overflow-hidden" style="height: 80px;">
                    <?php if($p['imagem']): ?>
                        <img src="/oagb/uploads/paginas/<?php echo $p['imagem']; ?>" class="img-fluid rounded">
                    <?php else: ?>
                        <i class="fas fa-image fa-2x text-muted opacity-25"></i>
                    <?php endif; ?>
                </div>
                <input type="file" name="imagem" class="form-control form-control-sm border-0 bg-light">
            </div>
            <div class="col-md-3 text-center border-end">
                <label class="form-label small fw-bold text-muted text-uppercase d-block mb-3">Foto do Conteúdo</label>
                <div class="p-3 rounded-4 bg-light mb-2 overflow-hidden" style="height: 80px;">
                    <?php if($p['imagem_card']): ?>
                        <img src="/oagb/uploads/paginas/<?php echo $p['imagem_card']; ?>" class="img-fluid rounded">
                    <?php else: ?>
                        <i class="fas fa-camera fa-2x text-muted opacity-25"></i>
                    <?php endif; ?>
                </div>
                <input type="file" name="imagem_card" class="form-control form-control-sm border-0 bg-light">
            </div>
            <div class="col-md-6 ps-md-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Tìtulo da Página</label>
                    <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5 fw-bold" required value="<?php echo htmlspecialchars($p['titulo']); ?>">
                </div>
                <div>
                    <label class="form-label small fw-bold text-muted text-uppercase">Slug URL</label>
                    <input type="text" name="slug" class="form-control border-0 bg-light p-3 fs-5" required value="<?php echo htmlspecialchars($p['slug']); ?>">
                </div>
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-muted text-uppercase">Conteúdo Institucional</label>
            <textarea name="conteudo" id="editor" class="form-control border-0 bg-light p-4" rows="15"><?php echo $p['conteudo']; ?></textarea>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-muted text-uppercase">Configuração da Sidebar (Barra Lateral)</label>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card bg-white border shadow-sm p-3 rounded-4 h-100">
                        <label class="x-small fw-bold text-muted mb-2">1. ESTILO VISUAL</label>
                        <select name="sidebar_widget" class="form-select border-0 bg-light py-2 small mb-3">
                            <option value="default" <?php echo $p['sidebar_widget']=='default'?'selected':''; ?>>Padrão (Lista Nobre)</option>
                            <option value="whats_new" <?php echo $p['sidebar_widget']=='whats_new'?'selected':''; ?>>O que há de novo? (Dinâmico)</option>
                            <option value="urgent" <?php echo $p['sidebar_widget']=='urgent'?'selected':''; ?>>Urgente (Destaque Marron)</option>
                            <option value="card" <?php echo $p['sidebar_widget']=='card'?'selected':''; ?>>Estilo Card Dourado</option>
                            <option value="gold" <?php echo $p['sidebar_widget']=='gold'?'selected':''; ?>>Destaque Ouro (Fundo Ouro)</option>
                            <option value="dark" <?php echo $p['sidebar_widget']=='dark'?'selected':''; ?>>Elegante (Fundo Escuro)</option>
                        </select>

                        <label class="x-small fw-bold text-muted mb-2">2. IDENTIDADE DA SIDEBAR</label>
                        <input type="text" name="sidebar_titulo" class="form-control border-0 bg-light py-2 small mb-2" value="<?php echo htmlspecialchars($p['sidebar_titulo']); ?>">
                        <input type="text" name="sidebar_icon" class="form-control border-0 bg-light py-2 small mb-3" value="<?php echo htmlspecialchars($p['sidebar_icon']); ?>">

                        <label class="x-small fw-bold text-muted mb-2">3. MENU DINÂMICO (FONTE)</label>
                        <select name="sidebar_menu_categoria" class="form-select border-0 bg-light py-2 small">
                            <option value="" <?php echo empty($p['sidebar_menu_categoria'])?'selected':''; ?>>Nenhum Menu Automático</option>
                            <option value="ORDEM" <?php echo $p['sidebar_menu_categoria']=='ORDEM'?'selected':''; ?>>Páginas da Categoria ORDEM</option>
                            <option value="ADVOGADOS" <?php echo $p['sidebar_menu_categoria']=='ADVOGADOS'?'selected':''; ?>>Páginas da Categoria ADVOGADOS</option>
                            <option value="PÚBLICO" <?php echo $p['sidebar_menu_categoria']=='PÚBLICO'?'selected':''; ?>>Páginas da Categoria PÚBLICO</option>
                            <option value="COMUNICAÇÃO" <?php echo $p['sidebar_menu_categoria']=='COMUNICAÇÃO'?'selected':''; ?>>Páginas da Categoria COMUNICAÇÃO</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card bg-white border shadow-sm p-3 rounded-4 h-100">
                        <label class="x-small fw-bold text-muted mb-2">3. CONTEÚDO PERSONALIZADO (TEXTO/LINKS/CONTACTOS)</label>
                        <textarea name="sidebar_conteudo" id="editor_sidebar" class="form-control border-0 bg-light p-4" rows="8"><?php echo $p['sidebar_conteudo']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-light border-0 p-4 rounded-4 mb-5">
            <h6 class="fw-bold text-uppercase small mb-4 text-maroon"><i class="fas fa-link me-2"></i>Botões de Ação Personalizados</h6>
            <div class="row g-4">
                <div class="col-md-6 border-end">
                    <label class="form-label small fw-bold text-muted">BOTÃO 1 (Primário)</label>
                    <input type="text" name="botao1_texto" class="form-control border-0 py-2 mb-2" value="<?php echo htmlspecialchars($p['botao1_texto']); ?>">
                    <div class="row g-2">
                        <div class="col-7">
                            <input type="text" name="botao1_link" class="form-control border-0 py-2" value="<?php echo htmlspecialchars($p['botao1_link']); ?>">
                        </div>
                        <div class="col-5">
                            <input type="file" name="botao1_file" class="form-control form-control-sm border-0 py-2 bg-white">
                            <?php if($p['botao1_file']): ?><div class="x-small text-success mt-1 fw-bold">Arquivo atual: <?php echo $p['botao1_file']; ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">BOTÃO 2 (Secundário)</label>
                    <input type="text" name="botao2_texto" class="form-control border-0 py-2 mb-2" value="<?php echo htmlspecialchars($p['botao2_texto']); ?>">
                    <div class="row g-2">
                        <div class="col-7">
                            <input type="text" name="botao2_link" class="form-control border-0 py-2" value="<?php echo htmlspecialchars($p['botao2_link']); ?>">
                        </div>
                        <div class="col-5">
                            <input type="file" name="botao2_file" class="form-control form-control-sm border-0 py-2 bg-white">
                            <?php if($p['botao2_file']): ?><div class="x-small text-success mt-1 fw-bold">Arquivo atual: <?php echo $p['botao2_file']; ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-md-4">
                <div class="card bg-light border-0 p-4 rounded-4">
                    <h6 class="fw-bold text-uppercase small mb-4 text-maroon"><i class="fas fa-list-ul me-2"></i>Configuração de Menu</h6>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="exibir_menu" id="exibir_menu" <?php echo $p['exibir_menu']?'checked':''; ?>>
                        <label class="form-check-label fw-bold small" for="exibir_menu">Mostrar no Menu</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Categoria do Menu</label>
                        <select name="menu_categoria" class="form-select border-0 py-2 small">
                            <option value="NENHUM" <?php echo $p['menu_categoria']=='NENHUM'?'selected':''; ?>>Nenhuma (Link Direto)</option>
                            <option value="ORDEM" <?php echo $p['menu_categoria']=='ORDEM'?'selected':''; ?>>Menu ORDEM</option>
                            <option value="ADVOGADOS" <?php echo $p['menu_categoria']=='ADVOGADOS'?'selected':''; ?>>Menu ADVOGADOS</option>
                            <option value="PÚBLICO" <?php echo $p['menu_categoria']=='PÚBLICO'?'selected':''; ?>>Menu PÚBLICO</option>
                            <option value="COMUNICAÇÃO" <?php echo $p['menu_categoria']=='COMUNICAÇÃO'?'selected':''; ?>>Menu COMUNICAÇÃO</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Posição no Menu (Ordem)</label>
                        <input type="number" name="ordem_menu" class="form-control border-0 py-2 small" value="<?php echo $p['ordem_menu']; ?>">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Estado da Página</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo" <?php echo $p['ativo']?'checked':''; ?>>
                            <label class="form-check-label small" for="ativo">Página Ativa</label>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Ordem de Listagem Geral</label>
                        <input type="number" name="ordem_exibicao" class="form-control border-0 py-2 small" value="<?php echo $p['ordem_exibicao']; ?>">
                    </div>
                </div>

                <div class="card bg-login-subtle border-0 p-4 rounded-4 mt-4">
                    <h6 class="fw-bold text-uppercase small mb-4 text-primary"><i class="fas fa-paint-brush me-2"></i>Design & Layout</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Estrutura de Colunas</label>
                        <select name="layout_tipo" class="form-select border-0 py-2 small">
                            <option value="1col" <?php echo $p['layout_tipo']=='1col'?'selected':''; ?>>1 Coluna (Largura Total)</option>
                            <option value="2col_right" <?php echo $p['layout_tipo']=='2col_right'?'selected':''; ?>>2 Colunas (Sidebar Direita)</option>
                            <option value="2col_left" <?php echo $p['layout_tipo']=='2col_left'?'selected':''; ?>>2 Colunas (Sidebar Esquerda)</option>
                            <option value="3col" <?php echo $p['layout_tipo']=='3col'?'selected':''; ?>>3 Colunas (Main + 2 Sidebars)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Posição da Imagem</label>
                        <select name="imagem_posicao" class="form-select border-0 py-2 small">
                            <option value="topo" <?php echo $p['imagem_posicao']=='topo'?'selected':''; ?>>No Topo (Destaque)</option>
                            <option value="meio" <?php echo $p['imagem_posicao']=='meio'?'selected':''; ?>>No Meio do Texto</option>
                            <option value="nenhuma" <?php echo $p['imagem_posicao']=='nenhuma'?'selected':''; ?>>Ocultar Imagem</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="mostrar_sidebar" id="mostrar_sidebar" <?php echo $p['mostrar_sidebar']?'checked':''; ?>>
                        <label class="form-check-label small" for="mostrar_sidebar">Ativar Sidebar</label>
                    </div>

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="mostrar_botoes" id="mostrar_botoes" <?php echo $p['mostrar_botoes']?'checked':''; ?>>
                        <label class="form-check-label small" for="mostrar_botoes">Ativar Botões de Ação</label>
                    </div>
                </div>
            </div>
            <div class="col-md-8 text-md-end pt-4">
                <button type="submit" class="btn btn-login w-100 py-3 shadow-lg fs-6 fw-bold text-uppercase">Guardar Alterações na Página</button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    }).catch(error => console.error(error));

    ClassicEditor.create(document.querySelector('#editor_sidebar'), {
        toolbar: ['bold', 'italic', 'link', 'bulletedList']
    }).catch(error => console.error(error));
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
