<?php

require_once "config.php";

function read()
{
    global $pdo;

    $sql = "SELECT * FROM Alunos";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($alunos);
}

function create()
{
    global $pdo;

    $dados = json_decode(file_get_contents("php://input"), true);

    $nome = $dados["nome"] ?? "";
    $email = $dados["email"] ?? "";
    $telefone = $dados["telefone"] ?? "";
    $data_nascimento = $dados["data_nascimento"] ?? "";

    $sql = "INSERT INTO Alunos
            (nome, email, telefone, data_nascimento)
            VALUES
            (:nome, :email, :telefone, :data_nascimento)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":nome" => $nome,
            ":email" => $email,
            ":telefone" => $telefone,
            ":data_nascimento" => $data_nascimento
        ]);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "mensagem" => "Aluno cadastrado com sucesso!"
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(409);
            echo json_encode([
                "erro" => "Este e-mail ja esta cadastrado."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "erro" => "Erro ao cadastrar aluno."
            ]);
        }
    }
}

$rota = $_GET["rota"] ?? "";
if ($_SERVER["REQUEST_METHOD"] == "GET" && $rota == "alunos") {
    read();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && $rota == "alunos/create") {
    create();
} else {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "erro" => "Rota nao encontrada."
    ]);
}

?>
