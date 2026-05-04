<?php
// admin/modules/utilizadores/edit.php
require_once __DIR__ . '/../../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Proteção extra: só Super Admin ou o próprio utilizador pode editar o próprio perfil
if ($_SESSION['admin_role'] !== 'super_admin' && $_SESSION['admin_id'] != $id) {
    die('<div class="alert alert-danger m-4">Sem permissão para editar este perfil.</div>');
}

// Obter dados atuais
$stmt = $pdo->prepare("SELECT id, username, full_name, role FROM admin_users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('<div class="alert alert-danger m-4">Utilizador não encontrado.</div>');
}

// Processar submissão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $role = $_POST['role'] ?? $user['role'];
    $new_password = $_POST['new_password'];

    // Se for o próprio utilizador, não pode alterar o seu próprio Role para não se trancar acidentalmente fora das permissões, a não ser que tenha lógica extra.
    if ($_SESSION['admin_role'] !== 'super_admin') {
        $role = $user['role']; // Força o role existente
    }

    if (empty($full_name) || empty($username)) {
        $error = "O Nome e o E-mail de utilizador são obrigatórios.";
    } else {
        try {
            if (!empty($new_password)) {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE admin_users SET full_name = ?, username = ?, role = ?, password = ? WHERE id = ?");
                $update->execute([$full_name, $username, $role, $hash, $id]);
            } else {
                $update = $pdo->prepare("UPDATE admin_users SET full_name = ?, username = ?, role = ? WHERE id = ?");
                $update->execute([$full_name, $username, $role, $id]);
            }
            
            $success = "Perfil atualizado com sucesso.";
            
            // Atualizar variável de sessão temporária se editou o próprio
            if ($_SESSION['admin_id'] == $id) {
                $_SESSION['admin_name'] = $full_name;
                $_SESSION['admin_username'] = $username;
            }

            // Recarregar os dados
            $user['full_name'] = $full_name;
            $user['username'] = $username;
            $user['role'] = $role;

        } catch (PDOException $e) {
            $error = "Erro ao guardar. O e-mail poderá já estar em uso.";
        }
    }
}
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="mb-0 fw-bold" style="font-family: 'Libre Baskerville', serif;">Perfil de Utilizador</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0 small">
                <li class="breadcrumb-item"><a href="/oagb/admin/" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Equipa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Perfil</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                
                <?php if ($error): ?>
                    <div class="alert alert-danger bg-danger-subtle text-danger border-0 small"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success bg-success-subtle text-success border-0 small"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nome Completo</label>
                        <input type="text" name="full_name" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">E-mail de Acesso</label>
                        <input type="email" name="username" class="form-control form-control-lg bg-light border-0" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Papel (Permissões)</label>
                        <select name="role" class="form-select form-select-lg bg-light border-0">
                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Administrador Geral</option>
                            <option value="editor" <?php echo $user['role'] == 'editor' ? 'selected' : ''; ?>>Editor de Conteúdos</option>
                            <option value="super_admin" <?php echo $user['role'] == 'super_admin' ? 'selected' : ''; ?>>Super Administrador</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <hr class="my-5 border-dashed text-muted">

                    <h5 class="fw-bold mb-4">Segurança</h4>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nova Palavra-passe</label>
                        <input type="password" name="new_password" class="form-control form-control-lg bg-light border-0" placeholder="Deixe em branco para manter a atual">
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1 opacity-50"></i> Preencha este campo apenas se pretender alterar a sua palavra-passe de acesso.</div>
                    </div>

                    <div class="mt-5 d-flex gap-3">
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="background-color: var(--primary-gold); border-color: var(--primary-gold);"><i class="fas fa-save me-2"></i> Guardar Alterações</button>
                        <a href="index.php" class="btn btn-light px-4 fw-bold border"><i class="fas fa-times me-2"></i> Cancelar</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
