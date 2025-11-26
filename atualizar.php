<?php
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST["id_prod"];
    $nomeprod = $_POST["nomeprod"];
    $preco = $_POST["preco"];
    $categorias = $_POST["categorias"];
    $especificacoes = $_POST["especificacoes"];
   
    $img_banco = $_POST["img_atual_completa"]; 
    $nome_digitado = $_POST['nome_arquivo']; 

    $novo_nome_base = !empty($nome_digitado) ? $nome_digitado : $nomeprod;

    $novo_nome_base = iconv('UTF-8', 'ASCII//TRANSLIT', $novo_nome_base);
    $novo_nome_base = str_replace(' ', '-', $novo_nome_base);
    $novo_nome_base = preg_replace('/[^A-Za-z0-9\-]/', '', $novo_nome_base);
    $novo_nome_base = strtolower($novo_nome_base); 

    $diretorio = "img/";
    $nome_final_salvar = $img_banco; 
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        
        $arquivo = $_FILES['arquivo'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        
        $nome_arquivo = $novo_nome_base . "." . $extensao;

        $i = 1;
        while (file_exists($diretorio . $nome_arquivo)) {
            $nome_arquivo = $novo_nome_base . "-" . $i . "." . $extensao;
            $i++;
        }
        if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo)) {
            $nome_final_salvar = $nome_arquivo;
            if (!empty($img_banco) && $img_banco != $nome_final_salvar && file_exists($diretorio . $img_banco)) {
                unlink($diretorio . $img_banco); 
            }
        }
    } 
    else if (!empty($img_banco) && file_exists($diretorio . $img_banco)) {
        
        $ext_atual = pathinfo($img_banco, PATHINFO_EXTENSION);
        $nome_atual_sem_ext = pathinfo($img_banco, PATHINFO_FILENAME);

        if ($novo_nome_base != $nome_atual_sem_ext) {
            
            $novo_nome_arquivo = $novo_nome_base . "." . $ext_atual;

            $i = 1;
            while (file_exists($diretorio . $novo_nome_arquivo)) {
                $novo_nome_arquivo = $novo_nome_base . "-" . $i . "." . $ext_atual;
                $i++;
            }

            if (rename($diretorio . $img_banco, $diretorio . $novo_nome_arquivo)) {
                $nome_final_salvar = $novo_nome_arquivo;
            }
        }
    }
    $sql = "UPDATE produtos SET 
                nomeprod = ?,
                preco = ?,
                categorias = ?,
                img = ?,
                especificacoes = ?
            WHERE id_prod = ?"; 

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sdsssi", $nomeprod, $preco, $categorias, $nome_final_salvar, $especificacoes, $id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Produto atualizado com sucesso!');
            window.location.href = 'consul_todos.php'; 
        </script>";
    } else {
        echo "<script>
            alert('Erro ao atualizar: " . addslashes($stmt->error) . "');
            window.location.href = 'consul_todos.php';
        </script>";
    }
    $stmt->close();

} else {
    echo "<script>window.location.href = 'consul_todos.php';</script>";
}
$conexao->close();
?>