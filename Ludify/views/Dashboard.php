<?php
session_start();

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "PUC@1234";
$dbname = "Ludify";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user']; // Recupera o e-mail do usuário logado

// Recupera o ID do usuário com base no e-mail
$queryUsuario = "SELECT ID_Usuario FROM Usuario WHERE Email = ?";
$stmt = $conn->prepare($queryUsuario);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id_usuario);
$stmt->fetch();
$stmt->close();

// Inicializa arrays
$jogos = [];
$ultimosJogos = [];

if ($id_usuario) {
    // Query para obter imagens de todos os jogos
    $sqlJogos = "SELECT Imagem FROM Jogo";
    $resultJogos = $conn->query($sqlJogos);

    if ($resultJogos->num_rows > 0) {
        while ($row = $resultJogos->fetch_assoc()) {
            $jogos[] = $row['Imagem'];
        }
    }

    $queryUltimosJogos = "
    SELECT Jogo.Imagem, Jogo.Titulo, Genero.Nome AS Genero
    FROM Aluguel
    INNER JOIN Jogo ON Aluguel.ID_Jogo = Jogo.ID_Jogo
    INNER JOIN Genero ON Jogo.ID_Genero = Genero.ID_Genero
    WHERE Aluguel.ID_Usuario = ?
    ORDER BY Aluguel.Data_Inicio DESC
    LIMIT 2
";
    $stmtUltimosJogos = $conn->prepare($queryUltimosJogos);
    $stmtUltimosJogos->bind_param("i", $id_usuario);
    $stmtUltimosJogos->execute();
    $resultUltimosJogos = $stmtUltimosJogos->get_result();

    while ($row = $resultUltimosJogos->fetch_assoc()) {
        $ultimosJogos[] = $row;
    }
    $stmtUltimosJogos->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@200&display=swap">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


    <link rel="stylesheet" href="../styles/dashboard/style.css">
</head>
<body>
    <div class="container-fluid" id="fundo">
        <div class="row">
             <div class="col-1 mt-3" id="lbar"> <!--navbar esquerda -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                      <a class="navbar brand p-2 mt-3" href="#"><img src="../assets/imgs/dashboard/logo.png" alt=""></a>
                    </li>
                    <li class="nav-item p-2">
                      <a class="nav-link p-3 text-white" style="border-radius: 12px; background-color: #2f2f2f;" href="#"><img src="../assets/imgs/dashboard/icone1.png" alt=""></a>
                    </li>
                    
                </ul>
                  <ul class="nav flex-colum" style="margin-top: 35vh;">
                    <li class="nav-item p-2">
                      <a class="nav-link p-3" href="biblioteca.php" style="border-radius: 12px; background-color: #2f2f2f;"><img src="../assets/imgs/dashboard/icone2.png" alt=""></a>
                    </li>
                    <li class="nav-item p-2 mt-5">
                      <a class="nav-link p-3" href="#" style="border-radius: 12px; background-color: #2f2f2f;"><img src="../assets/imgs/dashboard/icone3.png" alt=""></a>
                    </li>
                    <li class="nav-item p-2 mt-5">
                      <a class="nav-link p-3" href="#" style="border-radius: 12px; background-color: #2f2f2f;"><img src="../assets/imgs/dashboard/icone4.png" alt=""></a>
                    </li>
                    <li class="nav-item p-2 mt-5">
                      <a class="nav-link p-3" href="#" style="border-radius: 12px; background-color: #2f2f2f;"><img src="../assets/imgs/dashboard/icone5.png" alt=""></a>
                    </li>
                  </ul>
                  
            </div>
            <div class="col-8 mt-4"> <!--Conteudo do meio -->
              <div class="row"> <!--Barra de pesquisa -->
                <div class="col-1"></div>
                <div class="col-3"><input style="border-radius: 25px; padding: 6%; border: none; background-color: #171717;" type="text" placeholder="Search..."></div>
              </div>
              <div class="row mt-3" > <!-- Row para o jogo destaque -->
                <div class="col-1"></div> <!--Espaçamento-->
                <div class="col-10" style="border-radius: 45px;"><!--Card do jogo destaque-->
                  <div class="row"> <!--Row para dividir a area da imagem e area de texto-->
                    <div class="col-5 p-3 rounded-right" style="background-color: #0d0d0d; border-radius: 45px;"> <!--Col do texto-->
                      <div class="container-fluid p-0 m-0">
                       <!--Titulo do card-->
                        <div class="h3 text-white text-break p-0 m-0 w-100">God of War Ragnarok</div>
                      <div class="lead text-white mt-3 h5"> Kratos e Atreus embarcam numa viagem mítica em busca de respostas antes da chegada do Ragnarök</div>
                    
                    </div>
                     <div class="row mt-5 p-0"><!--Preço/comprar-->
                      <div class="col-2"></div>
                      <div class="col-5 bg-secondary pt-2 text-center text-white">R$249,90</div>
                      <div class="col-5 bg-secondary p-0"><button type="button" class="btn btn-primary">+ Carrinho</button></div>
                     </div>
                     <div class="row mt-2"> <!--Tags-->
                      <div class="col-2"></div>
                      <div class="col-3 text-white bg-secondary">Novo</div>
                      <div class="col-3"><img src="../assets/imgs/dashboard/windows.png" alt=""></div>
                      <div class="col-3"><img src="../assets/imgs/dashboard/playstation.png" alt=""></div>
                     </div> 
                    </div>
                    
                    <div class="col-7 p-0">
                      <img src="../assets/imgs/dashboard/image 11.png" class="imagem-destaque" alt="Imagem do Jogo">
                      <img src="../assets/imgs/dashboard/kratos_Atreus.png" class="imagem-kratos" alt="Imagem do Jogo">
                    </div>
                  
                  </div>
                </div>
              </div> <!--Fim do card destaque-->


              <!--carousel-->  
        <div class="h5 text-white" id="text">Lançamentos Recentes</div>
              <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <?php
        $chunks = array_chunk($jogos, 3); // Divide os jogos em grupos de 3
        foreach ($chunks as $index => $chunk) {
          $activeClass = $index === 0 ? 'active' : '';
          echo '<div class="carousel-item ' . $activeClass . '">';
          echo '<div class="container">';
          echo '<div class="row">';
          foreach ($chunk as $imagem) {
              echo '<div class="col-4">';
              echo '<img class="d-block w-100" src="' . $imagem . '" alt="Imagem do Jogo">';
              echo '</div>';
          }
          echo '</div>';
          echo '</div>';
          echo '</div>';
      }
      ?>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<div class="row ml-4">
    <div class="h5 ml-5 text-white mt-5" id="text2">Últimos Downloads</div>

    <?php foreach ($ultimosJogos as $jogo): ?>
        <div class="col-md-5 p-3">
            <div class="jogo-card">
                <div class="row no-gutters">
                    <div class="col-6">
                        <img src="<?php echo htmlspecialchars($jogo['Imagem']); ?>" alt="Imagem do Jogo" class="jogo-imagem">
                    </div>
                    <div class="col-6 d-flex flex-column justify-content-center">
                        <div class="jogo-titulo text-truncate"><?php echo htmlspecialchars($jogo['Titulo']); ?></div>
                        <div class="jogo-genero"><?php echo htmlspecialchars($jogo['Genero']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>



            </div>
            <div class="col-3 mt-3" id="rbar">
            <button class="logar" onclick="window.location.href='logout.php'">
              DESLOGAR
            <div class="icon"> 
              <svg
                height="30"
                width="24"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path d="M0 0h24v24H0z" fill="none"></path>
                <path
                  d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z"
                  fill="currentColor"
                ></path>
              </svg>
            </div>
            </button>
            <br>
            <p id="bemvindo">Bem-vindo ao seu painel, <?php echo htmlspecialchars($email); ?>!</p>
            </div>
        </div>
</div>
</body>
</html>