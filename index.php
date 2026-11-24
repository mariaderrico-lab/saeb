<?php

session_start();

require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/Controller/HomeController.php';
require_once __DIR__ . '/Controller/AuthController.php';
require_once __DIR__ . '/Controller/PublicacaoController.php';

$rotas = [
    'home' => ['HomeController', ['index']],
    'auth' => ['AuthController', ['login', 'logout', 'sessao']],
    'publicacao' => ['PublicacaoController', ['listar', 'buscar', 'curtir', 'minhas', 'criar', 'excluir']],
];

$partes = explode('/', trim($_GET['url'] ?? 'home/index', '/'));
$nome = $partes[0] ?: 'home';
$metodo = $partes[1] ?? 'index';

if (!isset($rotas[$nome]) || !in_array($metodo, $rotas[$nome][1], true)) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['erro' => 'Rota não encontrada.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $classe = $rotas[$nome][0];
    (new $classe())->$metodo();
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['erro' => 'Falha na conexão com o banco de dados.'], JSON_UNESCAPED_UNICODE);
}
