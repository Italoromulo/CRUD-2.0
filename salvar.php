<?php
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nomeprod = $_POST["nomeprod"];
    $preco = $_POST["preco"];
    $categorias = $_POST["categorias"];
    $especificacoes = $_POST["especificacoes"]; 
    $nome_imagem = "sem_foto.png";

    if (isset($_FILES['arquivo'])) {
        $arquivo = $_FILES['arquivo'];

        if ($arquivo['error'] === UPLOAD_ERR_OK) {
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $novo_nome = md5(time()) . "." . $extensao;
            $diretorio = "img/"; 

            if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $novo_nome)) {
                $nome_imagem = $novo_nome;
            } else {
                echo "<script>alert('Erro ao mover o arquivo para a pasta img.');</script>";
            }
        }
    }
    $sql = "INSERT INTO produtos (nomeprod, preco, categorias, img, especificacoes) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sdsss", $nomeprod, $preco, $categorias, $nome_imagem, $especificacoes);

    try {
        $stmt->execute();
        echo "<script>
            alert('Produto cadastrado com sucesso!');
            window.location.href = 'inicial.php';
        </script>";

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "<script>alert('Erro: Já existe um produto com esse nome.'); window.location.href = 'cadastro.php';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar: " . addslashes($e->getMessage()) . "'); window.location.href = 'cadastro.php';</script>";
        }
    }
    
    $stmt->close();
    $conexao->close();
    
} else {
    header("Location: cadastro.php");
}
?>