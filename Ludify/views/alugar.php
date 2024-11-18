<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "PUC@1234";
$dbname = "Ludify";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_jogo'])) {
    $id_jogo = intval($_POST['id_jogo']);
    $email = $_SESSION['user'];

    // Recupera o ID do usuário com base no email
    $queryUsuario = "SELECT ID_Usuario FROM Usuario WHERE Email = ?";
    $stmt = $conn->prepare($queryUsuario);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id_usuario);
    $stmt->fetch();
    $stmt->close();

    if ($id_usuario) {
        // Verifica se o usuário já alugou este jogo
        $queryVerificaAluguel = "SELECT COUNT(*) FROM Aluguel WHERE ID_Jogo = ? AND ID_Usuario = ?";
        $stmt = $conn->prepare($queryVerificaAluguel);
        $stmt->bind_param("ii", $id_jogo, $id_usuario);
        $stmt->execute();
        $stmt->bind_result($alugado);
        $stmt->fetch();
        $stmt->close();

        if ($alugado > 0) {
            echo "Você já alugou este jogo.";
        } else {
            // Realiza o aluguel
            $dataInicio = date("Y-m-d");
            $dataFim = date("Y-m-d", strtotime("+7 days")); // Aluguel de 7 dias, por exemplo.

            $queryAluguel = "INSERT INTO Aluguel (Data_Inicio, Data_Fim, ID_Jogo, ID_Usuario) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($queryAluguel);
            $stmt->bind_param("ssii", $dataInicio, $dataFim, $id_jogo, $id_usuario);

            if ($stmt->execute()) {
                echo "Jogo alugado com sucesso!";
            } else {
                echo "Erro ao alugar o jogo: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        echo "Usuário não encontrado.";
    }
} else {
    echo "Dados inválidos.";
}

$conn->close();
?>
