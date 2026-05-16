<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $data = $_POST['data_reuniao'];
    $conteudo = $_POST['conteudo'];
    $status = $_POST['status'];
    $codigo = $_POST['codigo'];
    $partilha_interna = isset($_POST['partilha_interna']) ? 1 : 0;
    $partilha_ordem = isset($_POST['partilha_ordem']) ? 1 : 0;
    
    $ficheiro_url = null;
    if (isset($_FILES['ficheiro']) && $_FILES['ficheiro']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../uploads/actas/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = time() . '_' . basename($_FILES['ficheiro']['name']);
        if (move_uploaded_file($_FILES['ficheiro']['tmp_name'], $upload_dir . $filename)) {
            $ficheiro_url = 'uploads/actas/' . $filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO gestao_actas (codigo, titulo, data_reuniao, conteudo, ficheiro_url, status, partilha_interna, partilha_ordem, criada_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$codigo, $titulo, $data, $conteudo, $ficheiro_url, $status, $partilha_interna, $partilha_ordem, 1]); // Mock user 1
    $new_id = $pdo->lastInsertId();

    // Trigger emails to Ordem if finalized and checked
    if ($status === 'finalizada' && $partilha_ordem) {
        require_once __DIR__ . '/../../includes/MailHelper.php';
        
        $emails = [];
        // Fetch Bastonarios
        $stmt = $pdo->query("SELECT email_contacto FROM bastonarios WHERE email_contacto != ''");
        while($row = $stmt->fetchColumn()) $emails[] = $row;
        // Fetch Departments
        $stmt = $pdo->query("SELECT email FROM departamentos_contactos WHERE status = 'ativo' AND email != ''");
        while($row = $stmt->fetchColumn()) $emails[] = $row;
        
        $emails = array_unique($emails);
        if (!empty($emails)) {
            $acta_link = "https://oagb.gw/view-acta.php?id=" . $new_id . "&code=" . $codigo;
            $subject = "OAGB: Nova Acta Digital Registada - $codigo";
            $message = "
                <h2 style='color: #4D1C21;'>Notificação de Acta Oficial</h2>
                <p>Informamos que foi registada uma nova acta no sistema digital da OAGB.</p>
                <p><strong>Título:</strong> " . htmlspecialchars($titulo) . "<br>
                   <strong>Código:</strong> $codigo<br>
                   <strong>Data:</strong> " . date('d/m/Y', strtotime($data)) . "</p>
                <div style='margin-top: 30px;'>
                    <a href='$acta_link' style='background-color: #111923; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>VISUALIZAR ACTA</a>
                </div>
            ";
            foreach($emails as $to) { MailHelper::send($to, $subject, $message); }
        }
    }
    
    require_once __DIR__ . '/../../includes/LogHelper.php';
    LogHelper::log($pdo, 'MINUTES_ADD', "Lavrou a acta: $titulo", 'gestao_actas', $new_id);

    header("Location: index.php?success=1"); exit;
}
?>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="page-title"><a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i></a> Nova Acta</h2>
        <div class="text-muted small">Registo oficial de deliberações e reuniões deliberativas da Ordem.</div>
    </div>
</div>

<div class="card border-0 shadow-sm p-5 bg-white mb-5">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Código da Acta</label>
                <input type="text" name="codigo" class="form-control border-0 bg-light p-3 fs-6" placeholder="Ex: ACT-2026-001">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted text-uppercase">Tìtulo da Reunião / Assembleia</label>
                <input type="text" name="titulo" class="form-control border-0 bg-light p-3 fs-5" required placeholder="Ex: Assembleia Geral Extraordinária">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Data da Reunião</label>
                <input type="date" name="data_reuniao" class="form-control border-0 bg-light p-3 fs-6" required value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="mb-5">
            <label class="form-label small fw-bold text-muted text-uppercase">Conteúdo Integrando Deliberações</label>
            <textarea name="conteudo" id="editor" class="form-control border-0 bg-light p-4" rows="15" placeholder="Digite o conteudo oficial da acta..."></textarea>
            <div class="x-small text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Utilize este espaço para transcrever todas as decisões tomadas.</div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted text-uppercase">Anexar Ficheiro (PDF/DOC)</label>
                <input type="file" name="ficheiro" class="form-control border-0 bg-light p-3">
            </div>
            <div class="col-md-6 d-flex align-items-center gap-4">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="partilha_interna" id="partilha_interna" checked>
                    <label class="form-check-label fw-bold small text-muted text-uppercase" for="partilha_interna">Portal de Membros</label>
                </div>
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="partilha_ordem" id="partilha_ordem" checked>
                    <label class="form-check-label fw-bold small text-muted text-uppercase" for="partilha_ordem">Notificar Ordem (Bastonários/Deps)</label>
                </div>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-4">
                <select name="status" class="form-select border-0 bg-light p-3 fw-bold text-uppercase small text-muted">
                    <option value="rascunho">MANTER COMO RASCUNHO</option>
                    <option value="finalizada">FINALIZAR E PUBLICAR</option>
                </select>
            </div>
            <div class="col-md-8 text-md-end">
                <button type="submit" class="btn btn-login w-auto px-5 py-3 shadow-lg fs-6 fw-bold text-uppercase">Registrar em Livro Digital</button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo'],
            language: 'pt'
        })
        .catch(error => {
            console.error(error);
        });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
