<?php
require_once __DIR__ . '/../Model/Usuario.php';
require_once __DIR__ . '/../Model/Publicacao.php';
require_once __DIR__ . '/../Model/Curtida.php';

class PublicacaoController extends Controller
{
    private function idLogado(): int
    {
        return (int)($this->usuarioLogado()['id_usuario'] ?? 0);
    }

    public function listar(): void
    {
        $fot = isset($_GET['fotografo']) ? (int)$_GET['fotografo'] : null;
        $this->json(['publicacoes' => Publicacao::listar($this->idLogado(), $fot)]);
    }

    public function buscar(): void
    {
        $termo = ltrim(trim($_GET['q'] ?? ''), '@');
        if (!preg_match('/^[A-Za-z0-9_]+$/', $termo)) {
            $this->json(['erro' => 'Formato inválido (exemplo: fotografo1).'], 422);
        }
        $f = Usuario::buscarFotografo($termo);
        if (!$f) {
            $this->json(['erro' => 'Fotógrafo não encontrado'], 404);
        }
        $this->json(['publicacoes' => Publicacao::listar($this->idLogado(), (int)$f['id_usuario'])]);
    }

    public function curtir(): void
    {
        $u = $this->exigirLogin();
        $id = (int)($this->entrada()['id_publicacao'] ?? 0);
        if (!Publicacao::existe($id)) {
            $this->json(['erro' => 'Publicação não encontrada.'], 404);
        }
        [$curtiu, $total] = Curtida::alternar((int)$u['id_usuario'], $id);
        $this->json(['curtiu' => $curtiu, 'curtidas' => $total]);
    }

    
    public function minhas(): void
    {
        $u = $this->exigirLogin();
        $this->json(['publicacoes' => Publicacao::listar((int)$u['id_usuario'], (int)$u['id_usuario'])]);
    }

    public function criar(): void
    {
        $u = $this->exigirLogin();
        if ($u['tipo'] !== 'fotografo') {
            $this->json(['erro' => 'Apenas fotógrafos podem publicar.'], 403);
        }
        $titulo = trim($_POST['titulo'] ?? '');
        $local = trim($_POST['local'] ?? '');
        if ($titulo === '' || $local === '' || empty($_FILES['foto']['tmp_name'])) {
            $this->json(['erro' => 'Informe título, local e a fotografia.'], 422);
        }
        $info = @getimagesize($_FILES['foto']['tmp_name']);
        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$info['mime'] ?? ''] ?? null;
        if (!$ext) {
            $this->json(['erro' => 'Envie uma imagem JPG, PNG ou WEBP.'], 422);
        }
        $nome = 'foto_' . $u['id_usuario'] . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../uploads/' . $nome)) {
            $this->json(['erro' => 'Falha ao salvar a imagem.'], 500);
        }
        Publicacao::criar($titulo, $local, (int)$u['id_usuario'], 'uploads/' . $nome);
        $this->json(['ok' => true]);
    }

    public function excluir(): void
    {
        $u = $this->exigirLogin();
        $id = (int)($this->entrada()['id_publicacao'] ?? 0);
        $url = Publicacao::urlDoDono($id, (int)$u['id_usuario']);
        if ($url === null) {
            $this->json(['erro' => 'Publicação não encontrada.'], 404);
        }
        Publicacao::excluir($id);
        if (strpos($url, 'uploads/') === 0) {
            @unlink(__DIR__ . '/../' . $url);
        }
        $this->json(['ok' => true]);
    }
}
