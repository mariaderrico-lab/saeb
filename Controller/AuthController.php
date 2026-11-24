<?php
require_once __DIR__ . '/../Model/Usuario.php';

class AuthController extends Controller
{
    public function login(): void
    {
        $d = $this->entrada();
        $email = trim($d['email'] ?? '');
        $senha = $d['senha'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['erro' => 'E-mail inválido ou vazio.'], 422);
        }
        if ($senha === '') {
            $this->json(['erro' => 'A senha é obrigatória e não pode estar vazia.'], 422);
        }

        $u = Usuario::buscarPorEmail($email);
        // Senhas do protótipo estão em texto puro; aceita também hash (password_hash)
        $ok = $u && (password_verify($senha, $u['senha']) || hash_equals($u['senha'], $senha));
        if (!$ok) {
            $this->json(['erro' => 'Usuário não encontrado ou senha incorreta'], 401);
        }

        session_regenerate_id(true);
        $_SESSION['usuario'] = Usuario::buscarPorId((int)$u['id_usuario']);
        $this->json(['usuario' => $_SESSION['usuario']]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->json(['ok' => true]);
    }

    public function sessao(): void
    {
        $sessao = $this->usuarioLogado();
        if (!$sessao) {
            $this->json(['usuario' => null]);
        }
        $u = Usuario::buscarPorId((int)$sessao['id_usuario']);
        $u['total_publicacoes'] = Usuario::totalPublicacoes($u['id_usuario']);
        $u['total_likes'] = Usuario::totalLikes($u['id_usuario']);
        $this->json(['usuario' => $u]);
    }
}
