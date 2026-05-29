<?php
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/functions.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = clean_input($_POST['titulo'] ?? '');
    $instituicao = clean_input($_POST['instituicao'] ?? '');
    $tipo = clean_input($_POST['tipo'] ?? 'Consultoria');
    $valor = clean_input($_POST['valor_financeiro'] ?? 'A combinar');
    $data_limite = $_POST['data_limite'] ?? '';
    $descricao = clean_input($_POST['descricao'] ?? '');
    $qualificacoes = clean_input($_POST['qualificacoes'] ?? '');
    $link = clean_input($_POST['link_edital'] ?? '#');
    $contacto = clean_input($_POST['contacto_submissao'] ?? '');
    
    if (empty($titulo) || empty($instituicao) || empty($data_limite) || empty($contacto) || empty($descricao)) {
        $error = "Por favor, preencha todos os campos obrigatórios (*).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO gestao_oportunidades (titulo, instituicao, tipo, valor_financeiro, data_limite, descricao, qualificacoes, link_edital, contacto_submissao, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente')");
            $stmt->execute([$titulo, $instituicao, $tipo, $valor, $data_limite, $descricao, $qualificacoes, $link, $contacto]);
            
            $success = "Agradecemos a sua submissão! A vaga/consultoria foi registada e será analisada pela comissão da OAGB. Após validação interna, ficará visível para toda a nossa rede de advogados inscritos.";
        } catch (PDOException $e) {
            $error = "Erro ao registar oportunidade: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    <title>Canal de Recrutamento & Parceiros | OAGB</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/header-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/footer-styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="css/banner-inscricao.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-maroon: #4D1C21;
            --primary-gold: #B1A276;
            --dark-navy: #111923;
        }
        body { font-family: 'Open Sans', sans-serif; background-color: #fafafa; }
        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: #111923; letter-spacing: 0.5px; }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #dcdfe3; padding: 12px 18px; font-size: 0.92rem; background: #fff; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 4px rgba(177, 162, 118, 0.15); }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        
        .subpage-breadcrumb-bar { padding: 10px 0 0 0; padding-top: 20px; background: transparent; z-index: 10; width: 100%; margin-bottom: 20px; }
        .subpage-breadcrumb-bar a, .subpage-breadcrumb-bar span { color: rgba(255,255,255,0.85) !important; text-decoration: none !important; font-size: 0.8rem; letter-spacing: 0.5px; transition: .3s; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
        .subpage-breadcrumb-bar a:hover { color: #fff; }
        .subpage-breadcrumb-bar .bc-active { color: #fff; font-weight: 600; font-size: 0.8rem !important; opacity: 1 !important; }
        .bc-sep { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--primary-gold); margin: 0 10px; vertical-align: middle; opacity: 0.6; }

        .quick-links a {
            width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.9); transition: .3s; font-size: 0.8rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            line-height: 1; vertical-align: middle;
        }
        .quick-links a:hover { background: rgba(255,255,255,0.15); color: #fff; border-color: var(--primary-gold); }

        .section-label { font-size: 0.7rem; letter-spacing: 4px; text-transform: uppercase; font-weight: 700; color: var(--primary-gold); display: block; margin-bottom: 12px; }
        .section-heading { font-family: 'Libre Baskerville', serif; color: var(--primary-maroon); font-weight: 700; font-size: 2.2rem; line-height: 1.3; margin-bottom: 30px; border-left: 5px solid var(--primary-gold); padding-left: 20px; }
    </style>
</head>
<body>

    <?php include 'includes/topbar.php'; ?>

    <!-- Desktop Header -->
    <div class="container-fluid position-relative p-0 d-none d-lg-block">
        <?php include 'includes/navbar.php'; ?>
        <div class="container-fluid bg-primary bg-header d-flex align-items-end" style="min-height: 400px; padding-bottom: 0; background: linear-gradient(rgba(17, 25, 35, 0.1), rgba(17, 25, 35, 0.45)), url('img/symbol-legal-law.jpg') center center no-repeat; background-size: cover;">
            <div class="subpage-breadcrumb-bar w-100" style="margin-bottom: 20px;">
                <div class="container d-flex justify-content-between">
                    <div class="d-flex align-items-center" style="margin-top: 12px;">
                        <a href="index.php">Início</a>
                        <span class="bc-sep"></span>
                        <a href="publicacoes.php">Publicações</a>
                        <span class="bc-sep"></span>
                        <span class="bc-active">Recrutamento & Parceiros</span>
                    </div>
                    <div class="quick-links d-flex align-items-center gap-2">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <a href="javascript:window.print()"><i class="fas fa-print"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <?php 
    $mobile_breadcrumbs = [
        ['label' => 'Início', 'url' => 'index.php'],
        ['label' => 'Publicações', 'url' => 'publicacoes.php'],
        ['label' => 'Parceiros', 'active' => true]
    ];
    include 'includes/mobile-header-subpage.php'; 
    ?>

    <!-- ======= MAIN CONTENT ======= -->
    <section class="py-5" style="background: #f7f5f0;">
        <div class="container py-lg-3">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <span class="section-label">Canal de Parcerias</span>
                    <h2 class="section-heading">Recrutamento & Parceiros</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 shadow p-4 mb-4 rounded-4 text-white bg-success"><i class="fas fa-check-circle fa-lg me-2"></i> <?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 shadow p-4 mb-4 rounded-4"><i class="fas fa-exclamation-triangle fa-lg me-2"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="card card-custom bg-white p-5 border-top border-4" style="border-top-color: var(--primary-maroon) !important;">
                        <h3 class="font-libre fw-bold text-dark mb-3 text-center">Submissão de Oportunidades</h3>
                        <p class="text-muted small text-center mb-5" style="max-width: 600px; margin: 0 auto;">
                            Ao publicar uma vaga na plataforma da OAGB, a sua proposta será distribuída de forma direta a todos os advogados e profissionais jurídicos inscritos na República da Guiné-Bissau.
                        </p>

                        <form method="POST">
                            <div class="row g-4">
                                <h5 class="fw-bold text-dark mt-4 mb-1" style="border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;"><i class="fas fa-building me-2 text-warning"></i> 1. Identificação do Recrutador / Parceiro</h5>
                                
                                <div class="col-md-6 col-12">
                                    <label class="form-label">Instituição / Organização *</label>
                                    <input type="text" name="instituicao" class="form-control" placeholder="Ex: Delegação da UE, PNUD, Banco Mundial, Advocacia Lda" required>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="form-label">Email de Contacto de Candidaturas *</label>
                                    <input type="email" name="contacto_submissao" class="form-control" placeholder="recrutamento@organizacao.org" required>
                                </div>
                                
                                <h5 class="fw-bold text-dark mt-5 mb-1" style="border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;"><i class="fas fa-briefcase me-2 text-warning"></i> 2. Detalhes da Oportunidade / Concurso</h5>
                                
                                <div class="col-12">
                                    <label class="form-label">Título da Vaga / Cargo de Consultoria *</label>
                                    <input type="text" name="titulo" class="form-control" placeholder="Ex: Consultor Sénior para Revisão Jurídica do Código Comercial" required>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="form-label">Categoria de Oportunidade *</label>
                                    <select name="tipo" class="form-select" required>
                                        <option value="Consultoria">Consultoria</option>
                                        <option value="Concurso Público">Concurso Público</option>
                                        <option value="Emprego">Vaga Corporativa / Emprego</option>
                                        <option value="Estágio">Oportunidade para Estagiários</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="form-label">Orçamento Estimado / Salário (CFA)</label>
                                    <input type="text" name="valor_financeiro" class="form-control" placeholder="Ex: 1.500.000 CFA ou A Combinar">
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="form-label">Data Limite de Candidaturas *</label>
                                    <input type="date" name="data_limite" class="form-control" required>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="form-label">Link para Edital / Termos de Referência</label>
                                    <input type="url" name="link_edital" class="form-control" placeholder="https://exemplo.org/edital.pdf">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Descrição Resumida das Funções *</label>
                                    <textarea name="descricao" class="form-control" rows="5" placeholder="Descreva os objetivos principais do concurso..." required></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Qualificações e Requisitos Necessários</label>
                                    <textarea name="qualificacoes" class="form-control" rows="4" placeholder="Ex: Licenciatura em Direito, 5 anos de prática profissional, etc."></textarea>
                                </div>

                                <div class="col-12 mt-5 text-end">
                                    <button type="submit" class="btn btn-dark px-5 py-3 fw-bold rounded-pill text-uppercase" style="background: var(--primary-maroon); border: none;"><i class="fas fa-upload me-1"></i> Submeter Vaga para Validação</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/banner-inscricao.php'; ?>
    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
