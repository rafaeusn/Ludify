<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "PUC@1234";
$dbname = "Ludify";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificando se existe um parâmetro 'id_genero' na URL para filtrar os jogos
$idGenero = isset($_GET['id_genero']) ? intval($_GET['id_genero']) : 0;

// Se o idGenero for 0, significa que queremos todos os jogos
if ($idGenero > 0) {
    // Filtrando por gênero
    $sql = "SELECT Jogo.Titulo, Jogo.Imagem FROM Jogo WHERE Jogo.ID_Genero = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idGenero);
} else {
    // Mostrando todos os jogos
    $sql = "SELECT Jogo.Titulo, Jogo.Imagem FROM Jogo"; 
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogos - Ludify</title>
    <link rel="stylesheet" href="../styles/jogo_genero/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <img src="../assets/imgs/dashboard/logo.png">
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Menu</a></li>
                    <li><a href="biblioteca.php">Todos os jogos</a></li>
                    <li class="nav-item dropdown">
                        <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Gêneros
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            // Carregar todos os gêneros para o menu dropdown
                            $sqlGeneros = "SELECT ID_Genero, Nome FROM Genero"; 
                            $resultGeneros = $conn->query($sqlGeneros);
                            if ($resultGeneros->num_rows > 0) {
                                while ($row = $resultGeneros->fetch_assoc()) {
                                    echo '<a class="dropdown-item" href="biblioteca.php?id_genero=' . $row["ID_Genero"] . '">' . htmlspecialchars($row["Nome"]) . '</a>';
                                }
                            } else {
                                echo "<a class='dropdown-item' href='#'>Nenhum gênero encontrado</a>";
                            }
                            ?>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="titulo-genero">
        <h1>Jogos <?php
            // Exibindo o nome do gênero se um gênero específico for filtrado
            if ($idGenero > 0) {
                $sqlGenero = "SELECT Nome FROM Genero WHERE ID_Genero = ?";
                $stmtGenero = $conn->prepare($sqlGenero);
                $stmtGenero->bind_param("i", $idGenero);
                $stmtGenero->execute();
                $resultGenero = $stmtGenero->get_result();
                if ($resultGenero->num_rows > 0) {
                    $rowGenero = $resultGenero->fetch_assoc();
                    echo "no gênero: " . htmlspecialchars($rowGenero["Nome"]);
                }
            } else {
                echo "Disponíveis";
            }
            ?></h1>
    </section>

    <section class="lista-filmes">
    <div class="descricao-filme">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="filme-item">';
                echo '<img src="../images/' . htmlspecialchars($row["Imagem"]) . '" alt="' . htmlspecialchars($row["Titulo"]) . '">';
                echo '<p>' . htmlspecialchars($row["Titulo"]) . '</p>';
                // Adicionando o botão "Baixar"
                echo '<button class="btn btn-primary btn-bloqueado">Baixar</button>';
                echo '</div>';
            }
        } else {
            echo "<p>Nenhum jogo encontrado.</p>";
        }
        ?>
    </div>
</section>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
