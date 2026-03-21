<?php
session_start();
if(!isset($_SESSION['lawyer_id'])) { header("Location: login.php"); exit; }
require_once __DIR__ . '/../connect.php';

$lid = $_SESSION['lawyer_id'];
$room = $_GET['room'] ?? 'OAGB-GERAL-'.date('Y');
$uname = $_SESSION['lawyer_name'] ?? 'Membro OAGB';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoconferência Institucional | OAGB 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gold: #B1A276; --sidebar-dark: #111923; }
        body, html { height: 100%; margin: 0; background: #000; overflow: hidden; }
        .jitsi-container { height: calc(100% - 70px); width: 100%; background: #111; }
        .conf-header { height: 70px; background: var(--sidebar-dark); border-bottom: 2px solid var(--primary-gold); padding: 0 40px; display: flex; align-items: center; justify-content: space-between; color: white; }
    </style>
</head>
<body>

    <header class="conf-header">
        <div class="d-flex align-items-center">
            <img src="/oagb/img/logo3.png" height="30" class="me-3">
            <h5 class="fw-bold mb-0 text-uppercase small" style="letter-spacing: 1px;">Sessão Segura: <span class="text-primary"><?php echo str_replace('-', ' ', $room); ?></span></h5>
        </div>
        <a href="index.php" class="btn btn-sm btn-dark fw-bold px-4 rounded-pill border-0 shadow-none"><i class="fas fa-sign-out-alt me-1"></i> FINALIZAR & VOLTAR</a>
    </header>

    <div id="jitsi-meet-conf" class="jitsi-container"></div>

    <script src="https://meet.jit.si/external_api.js"></script>
    <script>
        const domain = "meet.jit.si";
        const options = {
            roomName: "<?php echo $room; ?>",
            width: "100%",
            height: "100%",
            parentNode: document.querySelector('#jitsi-meet-conf'),
            userInfo: {
                displayName: "<?php echo $uname; ?>"
            },
            configOverwrite: { 
                startWithAudioMuted: true,
                disableDeepLinking: true,
                prejoinPageEnabled: false
            },
            interfaceConfigOverwrite: {
                TOOLBAR_BUTTONS: [
                    'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                    'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                    'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                    'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                    'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                    'e2ee'
                ],
                DEFAULT_REMOTE_DISPLAY_NAME: 'Membro OAGB'
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
