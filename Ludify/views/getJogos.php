<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "PUC@1234";
$dbname = "LUDIFY";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Buscar jogos no banco de dados
$sql = "SELECT Imagem FROM Jogo";
$result = $conn->query($sql);

$jogos = [];
if ($result->num_rows > 0) {
    // Armazenar os jogos em um array
    while($row = $result->fetch_assoc()) {
        $jogos[] = $row;
    }
}

// Fechar a conexão
$conn->close();

// Retornar os dados em formato JSON
echo json_encode($jogos);
?>
