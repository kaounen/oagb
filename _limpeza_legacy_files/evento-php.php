</body>
</html><?php
// Iniciar sessão e incluir ficheiros necessários
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';
require_once 'connect.php';

// Obter ID do evento
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$evento = null;
$outros_eventos = [];

try {
    // Buscar evento
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ? AND ativo = 1");
        $stmt->execute([$id]);
        $evento = $stmt->fetch();
    }
    
    if (!$evento) {
        header("Location: agenda.php");
        exit;
    }
    
    // Buscar outros eventos próximos
    $stmt = $pdo->prepare("
        SELECT id, titulo, data_evento, local_evento, imagem_destaque 
        FROM agenda 
        WHERE id != ? AND ativo = 1 
        AND DATE(data_evento) >= CURDATE()
        ORDER BY data_evento ASC 
        LIMIT 3
    ");
    $stmt->execute([$evento->id]);
    $outros_eventos = $stmt->fetchAll();
    
    // Se não houver eventos futuros, buscar eventos passados recentes
    if (empty($outros_eventos)) {
        $stmt = $pdo->prepare("
            SELECT id, titulo, data_evento, local_evento, imagem_destaque 
            FROM agenda 
            WHERE id != ? AND ativo = 1 
            ORDER BY data_evento DESC 
            LIMIT 3
        ");
        $stmt->execute([$evento->id]);
        $outros_eventos = $stmt->fetchAll();
    }
    
} catch (Exception $e) {
    error_log("Erro ao buscar evento: " . $e->getMessage());
    header("Location: agenda.php");
    exit;
}

$page_title = htmlspecialchars($evento->titulo);
$meta_description = htmlspecialchars($evento->descricao);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include 'includes/meta_tags_include.php'; ?>
    
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        .event-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .event-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .event-info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .event-info-icon {
            width: 50px;
            height: 50px;
            background: #c18046;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            flex-shrink: 0;
        }
        
        .event-content {
            font-family: 'Open Sans', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }
        
        .event-content h2, 
        .event-content h3, 
        .event-content h4 {
            font-family: 'Libre Baskerville', serif;
            color: #4D1C21;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        .event-content p {
            margin-bottom: 1.5rem;
        }
        
        .calendar-add {
            background: white;
            border: 2px solid #c18046;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-top: 2rem;
        }
        
        .calendar-add .btn {
            margin: 0.5rem;
        }
        
        .event-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .status-upcoming {
            background: #d4edda;
            color: #155724;
        }
        
        .status-ongoing {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-past {
            background: #f8d7da;
            color: #721c24;
        }
        
        .other-event-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .other-event-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        
        .other-event-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .share-event {
            padding: 1.5rem 0;
            border-top: 1px solid #e0e0e0;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .event-info-icon {
                width: 40px;
                height: 40px;
                margin-right: 1rem;
            }
            
            .calendar-add .btn {
                display: block;
                width: 100%;
                margin: 0.5rem 0;
            }
        }