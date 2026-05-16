<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

// Fetch Users
try {
    $stmt = $pdo->query("SELECT * FROM admin_users ORDER BY role ASC, full_name ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) { $users = []; }
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title">Gestão de Utilizadores (Staff)</h2>
        <div class="text-muted small">Controle de acessos e níveis de privilégio administrativos.</div>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="add.php" class="btn btn-login w-auto px-4"><i class="fas fa-user-shield me-2"></i> Adicionar Operador</a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden p-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4 border-0 small text-uppercase py-3">Nome / Utilizador</th>
                    <th class="border-0 small text-uppercase py-3">E-mail</th>
                    <th class="border-0 small text-uppercase py-3">Nível de Acesso</th>
                    <th class="border-0 small text-uppercase py-3">Último Acesso</th>
                    <th class="border-0 small text-uppercase py-3 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light p-2 me-3"><i class="fas fa-id-badge text-muted opacity-50"></i></div>
                                <div>
                                    <div class="fw-bold small"><?php echo $u['full_name']; ?></div>
                                    <div class="text-muted x-small">@<?php echo $u['username']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="small opacity-75"><?php echo $u['email']; ?></td>
                        <td>
                            <span class="badge py-2 px-3 small <?php 
                                echo $u['role'] == 'superadmin' ? 'bg-danger-subtle text-danger' : 
                                     ($u['role'] == 'admin' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info'); 
                            ?> text-uppercase fw-bold"><?php echo $u['role']; ?></span>
                        </td>
                        <td class="small opacity-75">
                            <?php echo $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : 'Nunca acedeu'; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="edit.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-secondary p-2 me-1"><i class="fas fa-edit"></i></a>
                                <?php if($u['id'] != $_SESSION['admin_id']): ?>
                                    <a href="delete.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Eliminar este acesso?')"><i class="fas fa-trash-alt"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
