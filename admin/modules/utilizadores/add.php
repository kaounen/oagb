<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['full_name'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO admin_users (full_name, username, password, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $user, $pass, $email, $role]);
        
        require_once __DIR__ . '/../../includes/LogHelper.php';
        LogHelper::log($pdo, 'USER_CREATE', "Criou novo utilizador $user com nível $role", 'admin_users', $pdo->lastInsertId());

        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao criar: " . $e->getMessage(); }
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Operador (Staff)</h2>
        <div class="text-muted small">Registe um novo utilizador para acesso ao painel OAGB 2.0.</div>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 max-width-800 mx-auto">
    <?php if(isset($error)): ?>
        <div class="alert alert-danger border-0 small px-4 py-3 mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-4">
            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted">Nome Completo do Funcionário</label>
                <input type="text" name="full_name" class="form-control border-0 bg-light p-3" required placeholder="Ex: João Manuel dos Santos">
            </div>
            
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Nome de Utilizador (Username)</label>
                <div class="input-group">
                    <span class="input-group-text border-0 bg-light opacity-50"><i class="fas fa-at"></i></span>
                    <input type="text" name="username" class="form-control border-0 bg-light p-3" required placeholder="Ex: jsantos">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">E-mail Profissional</label>
                <input type="email" name="email" class="form-control border-0 bg-light p-3" required placeholder="Ex: info@oagb.gw">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Senha de Acesso</label>
                <input type="password" name="password" class="form-control border-0 bg-light p-3" required placeholder="Mínimo 8 caracteres">
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Nível de Permissões</label>
                <select name="role" class="form-select border-0 bg-light p-3" required>
                    <option value="editor">Editor (Conteúdos apenas)</option>
                    <option value="admin" selected>Administrador (Geral)</option>
                    <option value="superadmin">Superadmin (Controlo total)</option>
                </select>
            </div>

            <div class="col-12 mt-5">
                <button type="submit" class="btn btn-login w-100 py-3 shadow-sm text-uppercase fw-bold">Criar Acesso ao Sistema</button>
                <a href="index.php" class="btn btn-light w-100 py-3 mt-2 border">Cancelar e Voltar</a>
            </div>
        </div>
    </form>
</div>

<style>
    .max-width-800 { max-width: 800px; }
</style>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
