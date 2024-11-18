<?php
$servername = "localhost:3306";
$username = "root"; 
$password = "PUC@1234"; 
$dbname = "Ludify"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $email_check = $conn->prepare("SELECT * FROM Usuario WHERE Email = ?");
    $email_check->bind_param("s", $email);
    $email_check->execute();
    $email_result = $email_check->get_result();

    if ($email_result->num_rows > 0) {
        echo 'true'; // E-mail já cadastrado
    } else {
        echo 'false'; // E-mail disponível
    }

    $email_check->close();
}

$conn->close();
?>
