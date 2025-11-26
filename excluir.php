<?php
include("conexao.php");

if (isset($_POST["id"])) {
    $id = $_POST["id"];
    $sql = "DELETE FROM produtos WHERE id_prod = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
            alert('Produto excluído com sucesso!');
            window.location.href = 'consul_todos.php'; 
        </script>";
    } else {
        echo "<script>
            alert('Erro ao excluir produto: " . addslashes($stmt->error) . "');
            window.location.href = 'consul_todos.php';
        </script>";
    }
    $stmt->close();
} else {
    echo "<script>
        alert('ID do produto não informado.');
        window.location.href = 'consul_todos.php';
    </script>";
}
$conexao->close();
?>