<?php

abstract class Controller
{
    protected function json($dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function view(string $nome, array $dados = []): void
    {
        extract($dados);
        require __DIR__ . '/../View/' . $nome . '.php';
    }

    protected function usuarioLogado(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    protected function exigirLogin(): array
    {
        $u = $this->usuarioLogado();
        if (!$u) {
            $this->json(['erro' => 'Não autenticado.'], 401);
        }
        return $u;
    }

    protected function entrada(): array
    {
        $json = json_decode(file_get_contents('php://input'), true);
        return is_array($json) ? $json : $_POST;
    }
}
