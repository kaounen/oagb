<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';
$table = ($mtype == 'estagiario') ? 'advogados_estagiarios' : 'advogados';

$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$lid]);
$user = $stmt->fetch();

if (!$user) { exit("Utilizador inválido."); }

// Check Regularized Status (Using valid_until)
$tipo_quota_id = ($mtype == 'estagiario') ? 2 : 1; 
$stmt = $pdo->prepare("SELECT COUNT(*) FROM finan_pagamentos 
                       WHERE advogado_id = ? AND membro_tipo = ? AND tipo_pagamento_id = ? 
                       AND status = 'confirmado' AND valid_until >= CURDATE()");
$stmt->execute([$lid, $mtype, $tipo_quota_id]);
$is_regularized = ($stmt->fetchColumn() > 0);

$page_title = "Carteira Profissional Digital";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | OAGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@400;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --primary-maroon: #4D1C21; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-carteira { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        
        /* Glassmorphic Professional Card Container */
        .card-preview-zone { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 0; }
        
        .id-card-wrapper {
            perspective: 1000px;
            width: 380px;
            height: 240px;
            cursor: pointer;
            margin-bottom: 30px;
        }
        
        .id-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s;
            transform-style: preserve-3d;
            box-shadow: 0 15px 45px rgba(0,0,0,0.15);
            border-radius: 16px;
        }
        
        .id-card-wrapper:hover .id-card-inner, .id-card-inner.flipped {
            transform: rotateY(180deg);
        }
        
        .id-card-front, .id-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Front of the Professional ID Card */
        .id-card-front {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, #200b0d 100%);
            color: #fff;
            padding: 20px;
        }
        
        .card-header-logo { display: flex; align-items: center; gap: 10px; border-bottom: 1px solid rgba(177, 162, 118, 0.3); padding-bottom: 8px; margin-bottom: 12px; }
        .card-header-logo img { height: 35px; }
        .card-header-logo .title-org { font-family: 'Montserrat', sans-serif; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #fff; }
        .card-header-logo .subtitle-org { font-size: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--primary-gold); }
        
        .card-main-content { display: flex; gap: 15px; align-items: center; }
        .card-photo-box { width: 75px; height: 75px; border-radius: 8px; border: 2px solid var(--primary-gold); background: rgba(255,255,255,0.05); object-fit: cover; }
        .card-photo-placeholder { width: 75px; height: 75px; border-radius: 8px; border: 2px solid var(--primary-gold); background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--primary-gold); }
        
        .card-details-info { flex-grow: 1; min-width: 0; }
        .card-lawyer-name { font-family: 'Montserrat', sans-serif; font-size: 0.9rem; font-weight: 700; color: #fff; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-truncate: true; }
        .card-lawyer-cedula { font-size: 0.72rem; color: var(--primary-gold); font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
        
        .card-badge-role { background: var(--primary-gold); color: var(--primary-maroon); font-size: 0.55rem; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 50px; display: inline-block; letter-spacing: 0.5px; }
        
        .card-footer-strip { margin-top: auto; display: flex; justify-content: space-between; align-items: center; font-size: 0.55rem; opacity: 0.7; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 6px; }
        
        /* Back of the Professional ID Card */
        .id-card-back {
            background: #fff;
            color: #111923;
            transform: rotateY(180deg);
            padding: 20px;
            border: 2px solid var(--primary-maroon);
        }
        
        .card-back-main { display: flex; align-items: center; justify-content: space-between; height: 100%; }
        .card-back-qr { width: 100px; height: 100px; }
        .card-back-text { flex-grow: 1; padding-left: 15px; font-size: 0.6rem; color: #444; line-height: 1.4; }
        .card-back-title { font-family: 'Montserrat', sans-serif; font-size: 0.68rem; font-weight: 700; color: var(--primary-maroon); margin-bottom: 5px; text-transform: uppercase; }
        
        .card-status-pill { border-radius: 50px; font-size: 0.75rem; padding: 10px 25px; font-weight: 700; text-align: center; width: 100%; max-width: 320px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

    <header class="hero-carteira">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Carteira Profissional Digital</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 text-center card-preview-zone">
                <p class="text-muted small mb-4">Passe o cursor ou toque no cartão para rodar e visualizar o verso com o QR Code de verificação em tempo real.</p>
                
                <!-- ID Card flip element -->
                <div class="id-card-wrapper">
                    <div class="id-card-inner" id="innerCard">
                        <!-- Front Card -->
                        <div class="id-card-front shadow-lg">
                            <div class="card-header-logo">
                                <img src="<?php echo ROOT_URL; ?>/img/logo3.png" alt="Logo">
                                <div>
                                    <div class="title-org">Ordem dos Advogados</div>
                                    <div class="subtitle-org">da Guiné-Bissau</div>
                                </div>
                            </div>
                            
                            <div class="card-main-content">
                                <?php if (!empty($user['foto'])): ?>
                                    <img src="<?php echo ROOT_URL; ?>/uploads/advogados/<?php echo $user['foto']; ?>" class="card-photo-box" alt="Foto">
                                <?php else: ?>
                                    <div class="card-photo-placeholder">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-details-info text-start">
                                    <div class="card-lawyer-name" title="<?php echo htmlspecialchars($user['nome_completo']); ?>"><?php echo htmlspecialchars($user['nome_completo']); ?></div>
                                    <div class="card-lawyer-cedula">Cédula Profissional: <?php echo $user['numero_registo']; ?></div>
                                    <div class="card-badge-role"><?php echo ($mtype == 'estagiario') ? 'Advogado Estagiário' : 'Advogado Habilitado'; ?></div>
                                </div>
                            </div>
                            
                            <div class="card-footer-strip">
                                <span>OAGB DIGITAL SYSTEM</span>
                                <span>Emitida em: <?php echo date('m/Y'); ?></span>
                            </div>
                        </div>
                        
                        <!-- Back Card -->
                        <div class="id-card-back shadow-lg">
                            <div class="card-back-main">
                                <div class="card-back-text text-start">
                                    <div class="card-back-title">Verificação de Situação</div>
                                    <p class="m-0 mb-1">A leitura deste QR Code permite a qualquer autoridade judicial, bancária ou administrativa comprovar a validade da cédula e o status ativo do profissional.</p>
                                    <p class="m-0 text-muted" style="font-size: 0.5rem;">Cód: <?php echo strtoupper(substr(md5($lid . $mtype), 0, 10)); ?></p>
                                </div>
                                <div class="card-back-qr">
                                    <!-- Dynamic QR Code pointing directly to lawyer-perfil.php validation page -->
                                    <img src="https://chart.googleapis.com/chart?chs=100&cht=qr&chl=<?php echo urlencode(ROOT_URL . '/advogado-perfil.php?id=' . $lid); ?>&choe=UTF-8" style="width: 100px; height: 100px;" alt="QR Code">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <?php if($is_regularized): ?>
                        <div class="card-status-pill bg-success-subtle text-success border border-success-subtle mx-auto"><i class="fas fa-check-circle me-2"></i> SITUAÇÃO: ATIVO E REGULARIZADO</div>
                    <?php else: ?>
                        <div class="card-status-pill bg-danger-subtle text-danger border border-danger-subtle mx-auto"><i class="fas fa-exclamation-triangle me-2"></i> SITUAÇÃO: PENDENTE / QUOTAS EM ATRASO</div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-dark rounded-pill px-4 py-2" onclick="document.getElementById('innerCard').classList.toggle('flipped')"><i class="fas fa-sync-alt me-2"></i> Rudar Cartão</button>
                    <button class="btn btn-outline-dark rounded-pill px-4 py-2" onclick="window.print()"><i class="fas fa-print me-2"></i> Imprimir Cartão</button>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
