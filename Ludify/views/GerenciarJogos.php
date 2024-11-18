<?php
// Configuração do banco de dados
$servername = "localhost:3306";
$username = "root";
$password = "PUC@1234";
$dbname = "Ludify";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Configura para trabalhar com caracteres acentuados do português
mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_query($conn, 'SET character_set_connection=utf8');
mysqli_query($conn, 'SET character_set_client=utf8');
mysqli_query($conn, 'SET character_set_results=utf8');

// Consulta para buscar jogos, classificações, gêneros e desenvolvedores (somente ativos)
$query = "SELECT j.ID_Jogo, j.Titulo, c.Descricao AS Classificacao, g.Nome AS Genero, d.Nome AS Desenvolvedora, j.Imagem 
          FROM Jogo j
          LEFT JOIN Classificacao c ON j.ID_Classificacao = c.ID_Classificacao
          LEFT JOIN Genero g ON j.ID_Genero = g.ID_Genero
          LEFT JOIN Desenvolvedora d ON j.ID_Desenvolvedora = d.ID_Desenvolvedora";
$stmt = $conn->query($query);

// Consulta para buscar todos os gêneros e armazenar em um array
$queryGeneros = "SELECT ID_Genero, Nome FROM Genero";
$resultGeneros = $conn->query($queryGeneros);
$generosArray = [];
while ($genre = $resultGeneros->fetch_assoc()) {
    $generosArray[] = $genre;
}

// Consulta para buscar todos os desenvolvedores e armazenar em um array
$queryDesenvolvedores = "SELECT ID_Desenvolvedora, Nome FROM Desenvolvedora";
$resultDesenvolvedores = $conn->query($queryDesenvolvedores);
$desenvolvedoresArray = [];
while ($developer = $resultDesenvolvedores->fetch_assoc()) {
    $desenvolvedoresArray[] = $developer;
}

// Consulta para buscar todas as classificações indicativas e armazenar em um array
$queryClassificacao = "SELECT ID_Classificacao, Descricao FROM Classificacao";
$resultClassificacao = $conn->query($queryClassificacao);
$classificacaoArray = [];
while ($class = $resultClassificacao->fetch_assoc()) {
    $classificacaoArray[] = $class;
}

// Controlar a exibição dos popups
$showAddPopup = false;
$showEditPopup = false;

// Processamento do formulário para adicionar jogo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addGame'])) {
    $gameName = $_POST['gameName'];
    $genre = $_POST['genre'];
    $developer = $_POST['developer'];
    $ageClassification = $_POST['ageClassification'];
    $imagePath = '';

    // Processamento da imagem
    if (isset($_FILES['gameImage']) && $_FILES['gameImage']['error'] == 0) {
        $imageTmpName = $_FILES['gameImage']['tmp_name'];
        $imageName = $_FILES['gameImage']['name'];
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);

        $uploadDir = '../uploads/';
        $imagePath = $uploadDir . uniqid() . '.' . $imageExt;

        // Validar a extensão da imagem
        if (in_array($imageExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            move_uploaded_file($imageTmpName, $imagePath);
        } else {
            echo "<p>Formato de imagem inválido! Apenas JPG, JPEG, PNG e GIF são permitidos.</p>";
            exit;
        }
    }

    // Verificar se todos os campos estão preenchidos
    if (!empty($gameName) && !empty($genre) && !empty($ageClassification) && !empty($developer)) {
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("INSERT INTO Jogo (Titulo, ID_Classificacao, ID_Genero, ID_Desenvolvedora, Imagem) 
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('siiss', $gameName, $ageClassification, $genre, $developer, $imagePath);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p>Erro ao adicionar o jogo: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Preencha todos os campos obrigatórios!</p>";
        $showAddPopup = true;
    }
}

// Processamento do formulário para excluir jogo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteGame'])) {
    $gameIdToDelete = $_POST['gameId'];

    if (!empty($gameIdToDelete)) {
        $conn->begin_transaction();

        try {
            // Excluir o jogo da tabela Jogo
            $stmt = $conn->prepare("DELETE FROM Jogo WHERE ID_Jogo = ?");
            $stmt->bind_param('i', $gameIdToDelete);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p>Erro ao excluir o jogo: " . $e->getMessage() . "</p>";
        }
    }
}

// Processamento do formulário para atualizar jogo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateGame'])) {
    $gameIdUpdate = $_POST['gameId'];
    $gameName = $_POST['gameName'];
    $genre = $_POST['genre'];
    $developer = $_POST['developer'];
    $ageClassification = $_POST['ageClassification'];
    $imagePath = '';

    // Processar a imagem
    if (isset($_FILES['gameImage']) && $_FILES['gameImage']['error'] == 0) {
        $imageTmpName = $_FILES['gameImage']['tmp_name'];
        $imageName = $_FILES['gameImage']['name'];
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);

        $uploadDir = '../uploads/';
        $imagePath = $uploadDir . uniqid() . '.' . $imageExt;

        if (in_array($imageExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            move_uploaded_file($imageTmpName, $imagePath);
        } else {
            echo "<p>Formato de imagem inválido! Apenas JPG, JPEG, PNG e GIF são permitidos.</p>";
            exit;
        }
    }

    // Verificar se todos os campos estão preenchidos
    if (!empty($gameIdUpdate) && !empty($gameName) && !empty($genre) && !empty($ageClassification) && !empty($developer)) {
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("UPDATE Jogo SET Titulo = ?, ID_Classificacao = ?, ID_Genero = ?, ID_Desenvolvedora = ?, Imagem = ? 
                                    WHERE ID_Jogo = ?");
            $stmt->bind_param('siissi', $gameName, $ageClassification, $genre, $developer, $imagePath, $gameIdUpdate);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "<p>Erro ao atualizar o jogo: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Preencha todos os campos obrigatórios!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles/gerenciarjogos/style.css">
    <script>
        // Garantir que os popups estejam sempre escondidos ao carregar a página
        window.onload = function () {
            document.getElementById('addGamePopup').style.display = 'none';
            document.getElementById('editGamePopup').style.display = 'none';
        };

        function openPopup() {
            document.getElementById('addGamePopup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('addGamePopup').style.display = 'none';
        }

        function openEditPopup(id, title, genreId, classificationId, developerId, imagePath) {
            document.getElementById('editGamePopup').style.display = 'block';
            document.getElementById('gameIdUpdate').value = id;
            document.getElementById('editGameName').value = title;
            document.getElementById('editGenre').value = genreId;
            document.getElementById('editAgeClassification').value = classificationId;
            document.getElementById('editDeveloper').value = developerId;
            document.getElementById('editGameImage').value = imagePath;
        }

        function closeEditPopup() {
            document.getElementById('editGamePopup').style.display = 'none';
        }
    </script>
</head>
<body>

    <div class="admin-container">
        <aside class="sidebar">
            <h2>Ludify</h2>
            <nav>
                <ul>
                    <li><a href="#">Dashboard</a></li>
                </ul>
            </nav>
        </aside>
        
        <section class="main-content">
            <header>
                <h1>Jogos</h1>
                <button class="add-game-btn" onclick="openPopup()">Adicionar Jogo</button>
            </header>

            <!-- Exibir os jogos com loop -->
            <?php while ($row = $stmt->fetch_assoc()): ?>
                <div class="game-item">
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($row['Titulo']); ?></p>
                    <p><strong>Classificação Indicativa:</strong> <?php echo htmlspecialchars($row['Classificacao']); ?></p>
                    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($row['Genero']); ?></p>
                    <p><strong>Desenvolvedor(a):</strong> <?php echo htmlspecialchars($row['Desenvolvedora']); ?></p>
                    <p><strong>Imagem:</strong></p>
                    <?php if (!empty($row['Imagem']) && file_exists($row['Imagem'])): ?>
                        <img src="<?php echo htmlspecialchars($row['Imagem']); ?>" alt="Imagem do Jogo" width="100">
                    <?php else: ?>
                        <p>Imagem não disponível</p>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="gameId" value="<?php echo $row['ID_Jogo']; ?>">
                        <button type="button" onclick="openEditPopup(
                            <?php echo htmlspecialchars(json_encode($row['ID_Jogo'])); ?>,
                            <?php echo htmlspecialchars(json_encode($row['Titulo'])); ?>,
                            <?php echo htmlspecialchars(json_encode($row['Genero'])); ?>,
                            <?php echo htmlspecialchars(json_encode($row['Classificacao'])); ?>,
                            <?php echo htmlspecialchars(json_encode($row['Desenvolvedora'])); ?>,
                            '<?php echo htmlspecialchars($row['Imagem']); ?>')" name="alterGame">Alterar</button>
                        <button type="submit" name="deleteGame">Excluir</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </section>
    </div>

    <!-- Add Game Popup -->
    <div id="addGamePopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Adicionar Jogo</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="gameName">Nome do Jogo</label>
                <input type="text" name="gameName" id="gameName" required>

                <label for="genre">Gênero</label>
                <select name="genre" id="genre" required>
                    <?php foreach ($generosArray as $genre): ?>
                        <option value="<?php echo $genre['ID_Genero']; ?>"><?php echo $genre['Nome']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="developer">Desenvolvedor(a)</label>
                <select name="developer" id="developer" required>
                    <?php foreach ($desenvolvedoresArray as $developer): ?>
                        <option value="<?php echo $developer['ID_Desenvolvedora']; ?>"><?php echo $developer['Nome']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="ageClassification">Classificação Indicativa</label>
                <select name="ageClassification" id="ageClassification" required>
                    <?php foreach ($classificacaoArray as $class): ?>
                        <option value="<?php echo $class['ID_Classificacao']; ?>"><?php echo $class['Descricao']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="gameImage">Imagem</label>
                <input type="file" name="gameImage" id="gameImage" accept="image/*">

                <button type="submit" name="addGame">Adicionar</button>
            </form>
        </div>
    </div>

    <!-- Edit Game Popup -->
    <div id="editGamePopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeEditPopup()">&times;</span>
            <h2>Editar Jogo</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="gameId" id="gameIdUpdate">

                <label for="editGameName">Nome do Jogo</label>
                <input type="text" name="gameName" id="editGameName" required>

                <label for="editGenre">Gênero</label>
                <select name="genre" id="editGenre" required>
                    <?php foreach ($generosArray as $genre): ?>
                        <option value="<?php echo $genre['ID_Genero']; ?>"><?php echo $genre['Nome']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="editDeveloper">Desenvolvedor(a)</label>
                <select name="developer" id="editDeveloper" required>
                    <?php foreach ($desenvolvedoresArray as $developer): ?>
                        <option value="<?php echo $developer['ID_Desenvolvedora']; ?>"><?php echo $developer['Nome']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="editAgeClassification">Classificação Indicativa</label>
                <select name="ageClassification" id="editAgeClassification" required>
                    <?php foreach ($classificacaoArray as $class): ?>
                        <option value="<?php echo $class['ID_Classificacao']; ?>"><?php echo $class['Descricao']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="editGameImage">Imagem</label>
                <input type="file" name="gameImage" id="editGameImage" accept="image/*">

                <button type="submit" name="updateGame">Atualizar</button>
            </form>
        </div>
    </div>
    <?php if ($showAddPopup): ?>
    <script>openPopup();</script>
    <?php endif; ?>

    <?php if ($showEditPopup): ?>
    <script>openEditPopup();</script>
    <?php endif; ?>
</body>
</html>
