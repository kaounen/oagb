<?php
session_start();
if (!isset($_SESSION['staff_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$sid = $_SESSION['staff_id'];
$sname = $_SESSION['staff_name'];
$srole = $_SESSION['staff_role'];
$scomissao = $_SESSION['staff_comissao'];

// Fetch meetings (Staff can see everything!)
$stmt = $pdo->prepare("SELECT * FROM intranet_reunioes WHERE status != 'concluida' ORDER BY data_hora ASC");
$stmt->execute();
$meetings = $stmt->fetchAll();

$success = null;
$error = null;

// Handle Meeting Scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_meeting'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $desc = trim($_POST['descricao'] ?? '');
    $data_hora = $_POST['data_hora'] ?? '';
    $acesso = $_POST['tipo_acesso'] ?? 'todos';
    
    if (!empty($titulo) && !empty($data_hora)) {
        try {
            $room = 'OAGB_Room_' . md5($titulo . time());
            $stmt = $pdo->prepare("INSERT INTO intranet_reunioes (titulo, descricao, room_name, data_hora, tipo_acesso, criador_id, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, 'agendada')");
            $stmt->execute([$titulo, $desc, $room, $data_hora, $acesso, $sid]);
            $success = "Reunião de videoconferência agendada com sucesso!";
            // Refresh meetings
            $stmt = $pdo->prepare("SELECT * FROM intranet_reunioes WHERE status != 'concluida' ORDER BY data_hora ASC");
            $stmt->execute();
            $meetings = $stmt->fetchAll();
        } catch (Exception $e) {
            $error = "Erro ao agendar reunião: " . $e->getMessage();
        }
    }
}

$active_room = $_GET['room'] ?? null;
$active_title = $_GET['title'] ?? 'Videoconferência';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoconferências e Reuniões | Intranet OAGB</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #B1A276;
            --sidebar-dark: #111923;
            --bg-light: #f5f6f8;
            --chat-bubble-self: #4D1C21;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
        }
        .navbar-intranet {
            background-color: var(--sidebar-dark);
            border-bottom: 5px solid var(--primary-gold);
        }
        .jitsi-frame-container {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            background: black;
            border: 3px solid var(--primary-gold);
        }
        .card-custom {
            border: none;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            background: white;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-intranet py-3">
        <div class="container px-4">
            <a class="navbar-brand d-flex align-items-center text-white fw-bold" href="index.php">
                <img src="../img/logo3.png" alt="OAGB" style="height: 38px; filter: brightness(0) invert(1);" class="me-2">
                <span style="font-family: 'Libre Baskerville', serif; font-size: 1.15rem;">INTRANET OAGB</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill fw-bold px-3"><i class="fas fa-home me-1"></i> Painel Principal</a>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill fw-bold px-3"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-1.5 mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">OAGB Meet</span>
                <h2 class="fw-bold mb-1" style="font-family: 'Libre Baskerville', serif;">Salas de Videoconferência do Staff</h2>
                <p class="text-muted small mb-0">Agende, inicie ou participe em sessões virtuais oficiais com suporte a partilha de ecrã e gravação.</p>
            </div>
        </div>

        <?php if ($active_room): ?>
            <!-- ACTIVE MEETING IFRAME -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-12">
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-video text-danger me-2"></i> Reunião Activa: <?php echo htmlspecialchars($active_title); ?></h5>
                            <a href="reunioes.php" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold"><i class="fas fa-door-open me-1"></i> Sair da Reunião</a>
                        </div>
                        
                        <div class="jitsi-frame-container">
                            <div id="meet" style="height: 600px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://meet.jit.si/external_api.js"></script>
            <script>
                const domain = 'meet.jit.si';
                const options = {
                    roomName: '<?php echo $active_room; ?>',
                    width: '100%',
                    height: 600,
                    parentNode: document.querySelector('#meet'),
                    userInfo: {
                        displayName: '<?php echo $sname; ?> (Staff - <?php echo $srole; ?>)'
                    },
                    configOverwrite: {
                        startWithAudioMuted: false,
                        startWithVideoMuted: false
                    }
                };
                const api = new JitsiMeetExternalAPI(domain, options);
            </script>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-check-circle me-2"></i> <?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger border-0 shadow-sm p-3 mb-4 rounded-3"><i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: Meetings List -->
            <div class="col-lg-7">
                <h5 class="fw-bold text-dark mb-4"><i class="fas fa-calendar-alt text-warning me-2"></i> Próximas Reuniões Agendadas</h5>
                
                <div class="row g-3">
                    <?php if (empty($meetings)): ?>
                        <div class="col-12 text-center py-5 opacity-50 bg-white rounded-4 border">
                            <i class="fas fa-video-slash fa-2x mb-3 text-muted"></i>
                            <div>Nenhuma reunião virtual agendada para o seu nível de acesso.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($meetings as $m): ?>
                            <div class="col-12">
                                <div class="card card-custom p-4 border">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1.5 rounded-pill small fw-bold">
                                            <i class="fas fa-dot-circle me-1"></i> <?php echo strtoupper($m['status']); ?>
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill small fw-bold text-uppercase">
                                            Acesso: <?php echo strtoupper($m['tipo_acesso']); ?>
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1" style="font-family: 'Libre Baskerville', serif;"><?php echo htmlspecialchars($m['titulo']); ?></h5>
                                    <p class="small text-muted mb-4"><?php echo htmlspecialchars($m['descricao']); ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                        <div class="small text-muted">
                                            <i class="far fa-clock me-1 text-warning"></i> <?php echo date('d/m/Y H:i', strtotime($m['data_hora'])); ?>
                                        </div>
                                        <a href="reunioes.php?room=<?php echo urlencode($m['room_name']); ?>&title=<?php echo urlencode($m['titulo']); ?>" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark"><i class="fas fa-sign-in-alt me-1"></i> Entrar na Sala</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column: Meeting Scheduler Form -->
            <div class="col-lg-5">
                <div class="card card-custom p-4 border" style="position: sticky; top: 20px;">
                    <h5 class="fw-bold text-dark mb-3" style="border-bottom: 2px solid var(--primary-gold); padding-bottom: 10px;">
                        <i class="fas fa-plus-circle text-warning me-2"></i> Agendar Reunião
                    </h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Título da Reunião *</label>
                            <input type="text" name="titulo" class="form-control rounded-3 p-3 border-0 bg-light" placeholder="Ex: Assembleia Geral Extraordinária" required style="font-size: 0.9rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descrição / Pauta</label>
                            <textarea name="descricao" class="form-control rounded-3 p-3 border-0 bg-light" rows="3" placeholder="Ex: Análise das contas anuais e propostas de orçamento." style="font-size: 0.9rem;"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Data e Hora *</label>
                            <input type="datetime-local" name="data_hora" class="form-control rounded-3 p-3 border-0 bg-light" required style="font-size: 0.9rem;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nível de Acesso</label>
                            <select name="tipo_acesso" class="form-select rounded-3 p-3 border-0 bg-light" style="font-size: 0.9rem;">
                                <option value="todos">Todos (Advogados e Estagiários)</option>
                                <option value="advogados">Apenas Advogados Inscritos</option>
                                <option value="estagiarios">Apenas Estagiários</option>
                                <option value="staff">Apenas Membros do Staff</option>
                            </select>
                        </div>
                        <button type="submit" name="schedule_meeting" class="btn btn-dark w-100 py-3 rounded-pill fw-bold text-uppercase" style="background: var(--chat-bubble-self); border: none;">
                            <i class="fas fa-video me-1"></i> Agendar & Criar Link Jitsi
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-dark text-white text-center py-4 border-top border-white border-opacity-10 mt-5">
        <div class="container">
            <div class="small opacity-50">&copy; 2026 Ordem dos Advogados da Guiné-Bissau. Intranet Segura.</div>
        </div>
    </footer>

</body>
</html>
