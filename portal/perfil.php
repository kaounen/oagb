<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';
require_once __DIR__ . '/../includes/functions.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$lid = $_SESSION['lawyer_id'];
$mtype = $_SESSION['member_type'] ?? 'advogado';
$table = ($mtype == 'estagiario') ? 'advogados_estagiarios' : 'advogados';

// Self-healing fallback: query table and check existence!
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$lid]);
$user = $stmt->fetch();

if (!$user) {
    $fallback_type = ($mtype == 'estagiario') ? 'advogado' : 'estagiario';
    $table = ($fallback_type == 'estagiario') ? 'advogados_estagiarios' : 'advogados';
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->execute([$lid]);
    $user = $stmt->fetch();
    if ($user) {
        $mtype = $fallback_type;
        $_SESSION['member_type'] = $fallback_type;
    }
}

$success = null;
$error = null;

// List of standard specialties
$especialidades_gb = [
    'Direito Civil' => 'Direito Civil',
    'Direito Comercial' => 'Direito Comercial',
    'Direito Laboral' => 'Direito Laboral',
    'Direito Penal' => 'Direito Penal / Criminal',
    'Direito Administrativo' => 'Direito Administrativo',
    'Direito Fiscal' => 'Direito Fiscal / Tributário',
    'Família e Menores' => 'Família e Menores',
    'Propriedade e Terras' => 'Propriedade e Terras',
    'Investimento Estrangeiro' => 'Investimento Estrangeiro',
    'Contratos Internacionais' => 'Contratos Internacionais',
    'Arbitragem e Mediação' => 'Arbitragem e Mediação',
    'Migração e Nacionalidade' => 'Migração e Nacionalidade',
    'Concursos Públicos' => 'Empresas e Concursos'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'password') {
        $nova = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if ($nova !== $confirm) { $error = "As senhas não coincidem."; }
        else {
            $hashed = password_hash($nova, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE $table SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $lid]);
            $success = "Senha de acesso atualizada com sucesso!";
        }
    } 
    elseif (isset($_POST['action']) && $_POST['action'] === 'profile') {
        $telefone = clean_input($_POST['telefone'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $localidade = clean_input($_POST['localidade'] ?? '');
        
        if ($mtype === 'advogado') {
            $especialidade = clean_input($_POST['especialidade'] ?? '');
            $linguas = clean_input($_POST['linguas'] ?? '');
            $biografia = clean_input($_POST['biografia'] ?? '');
            $online = isset($_POST['atendimento_online']) ? 1 : 0;
            $diaspora = isset($_POST['atende_diaspora']) ? 1 : 0;
            
            $stmt = $pdo->prepare("UPDATE advogados SET telefone = ?, email = ?, localidade = ?, especialidade = ?, linguas = ?, biografia = ?, atendimento_online = ?, atende_diaspora = ? WHERE id = ?");
            $stmt->execute([$telefone, $email, $localidade, $especialidade, $linguas, $biografia, $online, $diaspora, $lid]);
        } else {
            $stmt = $pdo->prepare("UPDATE advogados_estagiarios SET telefone = ?, email = ?, localidade = ? WHERE id = ?");
            $stmt->execute([$telefone, $email, $localidade, $lid]);
        }
        $success = "Perfil público e contactos atualizados com sucesso!";
        // Re-fetch user details after update
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$lid]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f5f6f8; }
        .hero-profile { background: var(--sidebar-dark); padding: 50px 0; color: white; border-bottom: 5px solid var(--primary-gold); }
        .profile-card { background: white; border-radius: 20px; padding: 40px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -40px; }
        
        .form-label { font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--sidebar-dark); }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #ddd; padding: 12px 15px; font-size: 0.9rem; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-gold); box-shadow: 0 0 0 3px rgba(177, 162, 118, 0.15); }
        
        .custom-switch { display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; }
        .custom-switch input { display: none; }
        .switch-slider { width: 34px; height: 20px; background-color: #ddd; border-radius: 20px; position: relative; transition: .3s; }
        .switch-slider::before { content: ""; position: absolute; width: 14px; height: 14px; border-radius: 50%; background: #fff; top: 3px; left: 3px; transition: .3s; }
        .custom-switch input:checked + .switch-slider { background-color: var(--primary-gold); }
        .custom-switch input:checked + .switch-slider::before { left: 17px; }
        .switch-label { font-size: 0.82rem; font-weight: 600; color: #444; }
    </style>
</head>
<body>

    <header class="hero-profile">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Configurações do Meu Escritório Digital</h2>
            <a href="index.php" class="text-white text-decoration-none opacity-50 small fw-bold"><i class="fas fa-arrow-left me-1"></i> VOLTAR AO PORTAL</a>
        </div>
    </header>

    <main class="container mb-5">
        <div class="profile-card">
            
            <?php if($success): ?>
                <div class="alert alert-success border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row g-5">
                <!-- Registration Left Block -->
                <div class="col-lg-4 border-end">
                    <h5 class="fw-bold mb-4" style="color: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">Dados do Registro</h5>
                    <div class="mb-4 text-center">
                        <div class="bg-light p-4 rounded-4 border d-inline-block">
                            <i class="fas fa-id-card fa-4x text-muted mb-2"></i>
                            <div class="fw-bold text-uppercase small text-muted">Cédula Profissional</div>
                            <div class="fs-4 fw-bold" style="color: var(--primary-gold);"><?php echo $user['numero_registo']; ?></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase d-block mb-1">Nome Completo</label>
                        <div class="fw-bold fs-6 text-dark"><?php echo $user['nome_completo']; ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase d-block mb-1">Tipo de Inscrição</label>
                        <span class="badge bg-dark text-white text-uppercase px-3 py-2"><?php echo $mtype; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase d-block mb-1">Data de Inscrição</label>
                        <div class="fw-bold text-dark"><?php echo $user['data_inscricao'] ? date('d/m/Y', strtotime($user['data_inscricao'])) : 'Registada'; ?></div>
                    </div>
                </div>
                
                <!-- Form Inputs Block -->
                <div class="col-lg-8">
                    <!-- General details profile form -->
                    <form method="POST" class="mb-5">
                        <input type="hidden" name="action" value="profile">
                        <h5 class="fw-bold mb-4" style="color: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">Informações Públicas e Contactos</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label">Telefone de Contacto</label>
                                <input type="text" name="telefone" class="form-control" placeholder="+245 XXXXXXX" value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <label class="form-label">E-mail Profissional</label>
                                <input type="email" name="email" class="form-control" placeholder="advogado@exemplo.com" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Localidade / Escritório</label>
                                <input type="text" name="localidade" class="form-control" placeholder="Cidade, Bairro, Rua..." value="<?php echo htmlspecialchars($user['localidade'] ?? ''); ?>">
                            </div>

                            <?php if ($mtype === 'advogado'): ?>
                                <div class="col-md-6 col-12">
                                    <label class="form-label">Especialidade Principal</label>
                                    <select name="especialidade" class="form-select">
                                        <option value="">Nenhuma / Advocacia Geral</option>
                                        <?php foreach ($especialidades_gb as $val => $label): ?>
                                            <option value="<?php echo $val; ?>" <?php echo (($user['especialidade'] ?? '') === $val) ? 'selected' : ''; ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="form-label">Idiomas Falados</label>
                                    <input type="text" name="linguas" class="form-control" placeholder="Ex: Português, Francês, Crioulo..." value="<?php echo htmlspecialchars($user['linguas'] ?? 'Português'); ?>">
                                </div>

                                <div class="col-md-6 col-12 mt-4">
                                    <label class="custom-switch">
                                        <input type="checkbox" name="atendimento_online" value="1" <?php echo !empty($user['atendimento_online']) ? 'checked' : ''; ?>>
                                        <span class="switch-slider"></span>
                                        <span class="switch-label">Consulta Online / Videoconferência</span>
                                    </label>
                                </div>

                                <div class="col-md-6 col-12 mt-4">
                                    <label class="custom-switch">
                                        <input type="checkbox" name="atende_diaspora" value="1" <?php echo !empty($user['atende_diaspora']) ? 'checked' : ''; ?>>
                                        <span class="switch-slider"></span>
                                        <span class="switch-label">Disponível para a Diáspora / Clientes Internacionais</span>
                                    </label>
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label">Biografia / Nota Curricular Curta</label>
                                    <textarea name="biografia" class="form-control" rows="5" placeholder="Descreva sucintamente as suas habilitações, percurso académico e áreas de maior foco profissional..." style="resize: none;"><?php echo htmlspecialchars($user['biografia'] ?? ''); ?></textarea>
                                </div>
                            <?php endif; ?>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-dark px-4 py-3 fw-bold rounded-pill text-uppercase" style="background: var(--sidebar-dark); border: none;">Salvar Informações</button>
                            </div>
                        </div>
                    </form>

                    <!-- Reset password block -->
                    <form method="POST">
                        <input type="hidden" name="action" value="password">
                        <h5 class="fw-bold mb-4" style="color: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding-bottom: 8px;">Segurança: Alterar Senha</h5>
                        <div class="bg-light p-4 rounded-4 border">
                            <div class="row g-3">
                                <div class="col-md-6 col-12">
                                    <label class="form-label small fw-bold text-muted">Nova Senha</label>
                                    <input type="password" name="new_password" class="form-control border-0 p-3" required minlength="6" placeholder="Mínimo 6 caracteres">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label small fw-bold text-muted">Confirmar Nova Senha</label>
                                    <input type="password" name="confirm_password" class="form-control border-0 p-3" required placeholder="Repita a nova senha">
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-outline-dark px-4 py-3 fw-bold rounded-pill text-uppercase">Mudar Senha</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</body>
</html>
