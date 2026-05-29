<?php
session_start();
if (!isset($_SESSION['staff_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/../connect.php';
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$sid = $_SESSION['staff_id'];
$sname = $_SESSION['staff_name'];
$semail = $_SESSION['staff_email'];
$srole = $_SESSION['staff_role'];
$scomissao = $_SESSION['staff_comissao'];

$success = null;
$error = null;

// Fetch all distinct channels from DB
$channels = ['Geral'];
if (!empty($scomissao)) {
    $channels[] = $scomissao;
}
try {
    $stmt = $pdo->query("SELECT DISTINCT comissao FROM intranet_chat WHERE comissao IS NOT NULL AND comissao != '' ORDER BY comissao ASC");
    $db_channels = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($db_channels as $c) {
        if (!in_array($c, $channels)) {
            $channels[] = $c;
        }
    }
} catch (Exception $e) {}

// Active channel logic
$active_channel = $_GET['channel'] ?? 'Geral';
if (!in_array($active_channel, $channels)) {
    $active_channel = 'Geral';
}

// Handle Sub-group / Channel Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_channel'])) {
    $new_chan = trim($_POST['new_channel_name'] ?? '');
    $new_chan = preg_replace("/[^a-zA-Z0-9\s_-]/", "", $new_chan); // Sanitize
    if (!empty($new_chan)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO intranet_chat (sender_id, sender_name, sender_role, message, comissao) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$sid, 'Sistema', 'Direção', "Subgrupo '$new_chan' criado por $sname.", $new_chan]);
            header("Location: chat.php?channel=" . urlencode($new_chan));
            exit;
        } catch (Exception $e) {
            $error = "Erro ao criar canal: " . $e->getMessage();
        }
    }
}

// Handle New Message with Optional Media Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_msg'])) {
    $msg = trim($_POST['message'] ?? '');
    $file_path = null;
    $file_type = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $orig_name = basename($_FILES['attachment']['name']);
        $clean_name = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", $orig_name);
        $target_dir = __DIR__ . '/../uploads/intranet/';
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_path = $target_dir . $clean_name;
        
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_path)) {
            $file_path = 'uploads/intranet/' . $clean_name;
            
            $ext = strtolower(pathinfo($clean_name, PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                $file_type = 'image';
            } elseif (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
                $file_type = 'audio';
            } else {
                $file_type = 'document';
            }
        }
    }

    if (!empty($msg) || !empty($file_path)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO intranet_chat (sender_id, sender_name, sender_role, message, file_path, file_type, comissao) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sid, $sname, $srole, $msg, $file_path, $file_type, $active_channel]);
            
            header("Location: chat.php?channel=" . urlencode($active_channel));
            exit;
        } catch (Exception $e) {
            $error = "Erro ao enviar mensagem: " . $e->getMessage();
        }
    }
}

// Fetch Chat History for the active channel
$messages = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM intranet_chat WHERE comissao = ? ORDER BY created_at ASC");
    $stmt->execute([$active_channel]);
    $messages = $stmt->fetchAll();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat e Partilha do Staff | Intranet OAGB</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #B1A276;
            --sidebar-dark: #111923;
            --bg-light: #f5f6f8;
            --chat-bubble-self: #4D1C21;
            --chat-bubble-other: #e9ecef;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .navbar-intranet {
            background-color: var(--sidebar-dark);
            border-bottom: 5px solid var(--primary-gold);
        }
        .chat-container {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        .chat-sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fafafa;
        }
        .chat-messages {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        .chat-input-area {
            background: white;
            padding: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .message-bubble {
            max-width: 65%;
            padding: 14px 20px;
            border-radius: 20px;
            margin-bottom: 20px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }
        .message-bubble.self {
            background-color: var(--chat-bubble-self);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        .message-bubble.other {
            background-color: var(--chat-bubble-other);
            color: #333;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }
        .message-sender {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .message-time {
            font-size: 0.65rem;
            opacity: 0.6;
            margin-top: 6px;
            text-align: right;
        }
        .channel-link {
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-radius: 12px;
            margin: 5px 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .channel-link.active, .channel-link:hover {
            background: rgba(77, 28, 33, 0.08);
            color: var(--chat-bubble-self);
        }
    </style>
</head>
<body>

    <!-- INTRA NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-intranet py-3">
        <div class="container-fluid px-4">
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

    <!-- CHAT CONTAINER -->
    <div class="chat-container">
               <!-- SIDEBAR CHANNELS -->
        <aside class="chat-sidebar py-3" style="display: flex; flex-direction: column; height: 100%;">
            <h6 class="px-4 text-uppercase text-muted fw-bold x-small mb-3" style="font-size: 0.72rem; letter-spacing: 1px;">Canais & Subgrupos</h6>
            
            <div style="flex: 1; overflow-y: auto; max-height: calc(100vh - 250px);">
                <?php foreach ($channels as $c): ?>
                    <?php if ($c === 'Geral'): ?>
                        <a href="chat.php?channel=Geral" class="channel-link <?php echo $active_channel === 'Geral' ? 'active' : ''; ?>">
                            <i class="fas fa-hashtag me-2 text-warning"></i> Canal Geral
                        </a>
                    <?php elseif ($c === $scomissao): ?>
                        <a href="chat.php?channel=<?php echo urlencode($scomissao); ?>" class="channel-link <?php echo $active_channel === $scomissao ? 'active' : ''; ?>">
                            <i class="fas fa-landmark me-2 text-primary"></i> <?php echo htmlspecialchars($scomissao); ?>
                        </a>
                    <?php else: ?>
                        <a href="chat.php?channel=<?php echo urlencode($c); ?>" class="channel-link <?php echo $active_channel === $c ? 'active' : ''; ?>">
                            <i class="fas fa-users me-2 text-success"></i> <?php echo htmlspecialchars($c); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <hr class="mx-3 my-2 opacity-50">
            
            <!-- Create Subgroup Form -->
            <div class="px-3 mb-2">
                <form method="POST" class="d-flex flex-column gap-1">
                    <input type="text" name="new_channel_name" class="form-control form-control-sm border rounded-3 p-2" placeholder="Nome do subgrupo..." required style="font-size: 0.78rem;">
                    <button type="submit" name="create_channel" class="btn btn-warning btn-sm w-100 fw-bold rounded-pill" style="font-size: 0.75rem;"><i class="fas fa-plus me-1"></i> Criar Subgrupo</button>
                </form>
            </div>

            <hr class="mx-3 my-2 opacity-50">
            <div class="px-4 mt-auto text-muted small">
                <div class="fw-bold text-dark" style="font-size: 0.8rem;"><?php echo $sname; ?></div>
                <div class="x-small text-uppercase text-warning fw-bold" style="font-size: 0.65rem;"><?php echo $srole; ?></div>
            </div>
        </aside>ide>

        <!-- MAIN CONVERSATION FRAME -->
        <main class="chat-main">
            
            <div class="bg-white border-bottom p-3 px-4 d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <?php echo $active_channel === 'Geral' ? 'Canal Geral (Todo o Staff)' : 'Comissão: ' . htmlspecialchars($active_channel); ?>
                    </h5>
                    <span class="x-small text-muted" style="font-size: 0.72rem;">Espaço reservado para partilha de propostas, documentos e pareceres.</span>
                </div>
                <a href="chat.php?channel=<?php echo urlencode($active_channel); ?>" class="btn btn-outline-secondary btn-sm rounded-circle p-2" title="Atualizar Chat"><i class="fas fa-sync-alt"></i></a>
            </div>

            <!-- CHAT MESSAGES PANEL -->
            <div class="chat-messages" id="chatMessages">
                
                <?php if (empty($messages)): ?>
                    <div class="text-center py-5 opacity-50">Nenhuma mensagem registada neste canal. Seja o primeiro a partilhar!</div>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                        <?php $is_self = ($m['sender_id'] == $sid); ?>
                        <div class="message-bubble <?php echo $is_self ? 'self' : 'other'; ?>">
                            <div class="message-sender <?php echo $is_self ? 'text-warning' : 'text-primary'; ?>">
                                <?php echo htmlspecialchars($m['sender_name']); ?> <span class="opacity-50 small">(<?php echo htmlspecialchars($m['sender_role']); ?>)</span>
                            </div>
                            
                            <?php if (!empty($m['message'])): ?>
                                <div class="message-text"><?php echo htmlspecialchars($m['message']); ?></div>
                            <?php endif; ?>

                            <!-- ATTACHMENT RENDERING -->
                            <?php if (!empty($m['file_path'])): ?>
                                <div class="mt-2.5 pt-2 border-top border-white border-opacity-10">
                                    <?php if ($m['file_type'] === 'image'): ?>
                                        <div class="rounded-3 overflow-hidden border" style="max-width: 280px; max-height: 180px;">
                                            <a href="../<?php echo $m['file_path']; ?>" target="_blank">
                                                <img src="../<?php echo $m['file_path']; ?>" class="w-100 h-100" style="object-fit: cover;" alt="Shared Image">
                                            </a>
                                        </div>
                                    <?php elseif ($m['file_type'] === 'audio'): ?>
                                        <div class="py-1">
                                            <audio controls style="max-width: 100%;">
                                                <source src="../<?php echo $m['file_path']; ?>" type="audio/mpeg">
                                                O seu browser não suporta áudio HTML5.
                                            </audio>
                                        </div>
                                    <?php else: ?>
                                        <a href="../<?php echo $m['file_path']; ?>" target="_blank" class="d-flex align-items-center text-decoration-none <?php echo $is_self ? 'text-white' : 'text-danger'; ?> bg-white bg-opacity-10 p-2.5 rounded-3">
                                            <i class="far fa-file-alt fa-lg me-2 text-warning"></i>
                                            <div>
                                                <div class="small fw-bold text-truncate" style="max-width: 220px;"><?php echo basename($m['file_path']); ?></div>
                                                <div class="x-small opacity-50">Descarregar Ficheiro</div>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="message-time"><?php echo date('H:i', strtotime($m['created_at'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- INPUT FIELD -->
            <div class="chat-input-area">
                <form method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control bg-light border-0 py-3" placeholder="Escreva a sua nota ou parecer..." required autofocus>
                        <input type="file" name="attachment" id="fileAttach" class="d-none" accept="image/*,audio/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        
                        <button type="button" class="btn btn-outline-danger px-3 me-1" id="voiceRecBtn" title="Gravar Nota de Voz (WhatsApp)">
                            <i class="fas fa-microphone"></i>
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary px-3" onclick="document.getElementById('fileAttach').click();" title="Anexar Imagem, Áudio ou Documento">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        
                        <button type="submit" name="send_msg" class="btn btn-dark px-4 fw-bold" style="background: var(--chat-bubble-self); border: none;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div id="fileInfo" class="x-small text-success mt-1.5 fw-bold d-none"><i class="fas fa-check-circle me-1"></i> Ficheiro seleccionado pronto para envio!</div>
                </form>
            </div>

        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to the bottom of message window automatically
        const chatWindow = document.getElementById('chatMessages');
        chatWindow.scrollTop = chatWindow.scrollHeight;

        // Display selected filename
        document.getElementById('fileAttach').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('fileInfo').classList.remove('d-none');
            } else {
                document.getElementById('fileInfo').classList.add('d-none');
            }
        });

        // WhatsApp-style Audio voice messages
        let mediaRecorder;
        let audioChunks = [];
        let isRecording = false;
        const voiceRecBtn = document.getElementById('voiceRecBtn');

        voiceRecBtn.addEventListener('click', async () => {
            if (!isRecording) {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        mediaRecorder = new MediaRecorder(stream);
                        audioChunks = [];
                        
                        mediaRecorder.ondataavailable = event => {
                            audioChunks.push(event.data);
                        };

                        mediaRecorder.onstop = async () => {
                            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                            const formData = new FormData();
                            formData.append('audio_data', audioBlob);
                            formData.append('channel', '<?php echo $active_channel; ?>');

                            voiceRecBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-warning"></i> Enviando...';

                            try {
                                const response = await fetch('upload_voice.php', {
                                    method: 'POST',
                                    body: formData
                                });
                                const result = await response.json();
                                if (result.success) {
                                    window.location.reload();
                                } else {
                                    alert('Erro ao enviar áudio: ' + result.error);
                                    window.location.reload();
                                }
                            } catch (err) {
                                alert('Erro de rede ao enviar áudio.');
                                window.location.reload();
                            }
                        };

                        mediaRecorder.start();
                        isRecording = true;
                        voiceRecBtn.innerHTML = '<i class="fas fa-stop-circle text-danger fa-pulse"></i> Parar';
                        voiceRecBtn.classList.add('btn-danger', 'text-white');
                        voiceRecBtn.classList.remove('btn-outline-danger');
                    } catch (err) {
                        alert('Acesso ao microfone recusado ou indisponível.');
                    }
                } else {
                    alert('Gravação de áudio não é suportada neste navegador.');
                }
            } else {
                mediaRecorder.stop();
                isRecording = false;
                voiceRecBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                voiceRecBtn.classList.remove('btn-danger', 'text-white');
                voiceRecBtn.classList.add('btn-outline-danger');
            }
        });
    </script>
</body>
</html>
