<?php
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $morada = $_POST['morada'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $horario = $_POST['horario'];
    $ordem = (int)$_POST['ordem'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("INSERT INTO departamentos_contactos (titulo, morada, telefone, email, horario, ordem, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $morada, $telefone, $email, $horario, $ordem, $status]);
        
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) { $error = "Erro ao registar: " . $e->getMessage(); }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Novo Departamento</h2>
    </div>
</div>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-5">
        <form method="POST">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Nome do Departamento / Unidade</label>
                        <input type="text" name="titulo" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Sede Nacional, Gabinete de Estágio..." required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-uppercase fw-bold text-muted small">Morada Completa</label>
                        <textarea name="morada" class="form-control border-0 bg-light" rows="3" placeholder="Endereço físico detalhado..." required></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-bold text-muted small">Telefone</label>
                            <input type="text" name="telefone" class="form-control border-0 bg-light" placeholder="+245 ...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-uppercase fw-bold text-muted small">E-mail</label>
                            <input type="email" name="email" class="form-control border-0 bg-light" placeholder="departamento@oagb.gw">
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0 p-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Horário de Atendimento</label>
                            <input type="text" name="horario" class="form-control border-0 py-2 small" placeholder="Ex: Seg-Sex: 08:30-15:30">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Ordem de Exibição</label>
                            <input type="number" name="ordem" class="form-control border-0 py-2 small" value="1">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold text-muted small">Estado</label>
                            <select name="status" class="form-select border-0 py-2 small">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>

                        <hr class="my-4">
                        <button type="submit" class="btn btn-login w-100 py-3 shadow-sm fw-bold">GUARDAR DEPARTAMENTO</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
