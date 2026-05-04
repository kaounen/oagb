<?php
session_start();
require_once __DIR__ . '/../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificacao = trim($_POST['identificacao'] ?? '');

    if (empty($identificacao)) {
        $_SESSION['reset_error'] = "Por favor, insira o seu e-mail ou número de cédula.";
        header("Location: recuperar_senha.php");
        exit;
    }

    try {
        // Find Lawyer
        $stmt = $pdo->prepare("SELECT id, numero_registo, 'advogado' as mtype FROM advogados WHERE (numero_registo = ? OR email = ?) AND status = 'ativo'");
        $stmt->execute([$identificacao, $identificacao]);
        $user = $stmt->fetch();

        // If not, find Intern
        if (!$user) {
            $stmt = $pdo->prepare("SELECT id, numero_registo, 'estagiario' as mtype FROM advogados_estagiarios WHERE (numero_registo = ? OR email = ?) AND status = 'ativo'");
            $stmt->execute([$identificacao, $identificacao]);
            $user = $stmt->fetch();
        }

        if ($user) {
            // Found a valid active user. Reset password back to NULL
            // So that login.php forces logic: if ($user['password'] === null && $pass === $user['numero_registo'])
            
            if ($user['mtype'] === 'advogado') {
                $update = $pdo->prepare("UPDATE advogados SET password = NULL WHERE id = ?");
            } else {
                $update = $pdo->prepare("UPDATE advogados_estagiarios SET password = NULL WHERE id = ?");
            }
            $update->execute([$user['id']]);

            $_SESSION['reset_success'] = "A sua senha foi reposta com sucesso para o seu Número de Cédula!<br><br>Utilize o seu número: <b>" . htmlspecialchars($user['numero_registo']) . "</b> para entrar.";
            header("Location: recuperar_senha.php");
            exit;
        } else {
            $_SESSION['reset_error'] = "Não foi encontrado nenhum membro ativo com esses dados.";
            header("Location: recuperar_senha.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['reset_error'] = "Ocorreu um erro no servidor. Tente novamente mais tarde.";
        header("Location: recuperar_senha.php");
        exit;
    }
} else {
    header("Location: recuperar_senha.php");
    exit;
}
