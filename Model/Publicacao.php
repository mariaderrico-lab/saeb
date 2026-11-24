<?php
require_once __DIR__ . '/../Config/Database.php';

class Publicacao
{
    public static function listar(int $idLogado = 0, ?int $idFotografo = null): array
    {
        $sql = 'SELECT p.id_publicacao, p.titulo_imagem, p.local_foto, p.url_fotografia, p.id_usuario,
                       u.nome AS fotografo,
                       (SELECT COUNT(*) FROM tb_curtida c WHERE c.id_publicacao = p.id_publicacao) AS curtidas,
                       (SELECT COUNT(*) FROM tb_curtida c WHERE c.id_publicacao = p.id_publicacao AND c.id_usuario = :logado) AS curtiu
                FROM tb_publicacao p
                JOIN tb_usuario u ON u.id_usuario = p.id_usuario';
        $params = [':logado' => $idLogado];
        if ($idFotografo !== null) {
            $sql .= ' WHERE p.id_usuario = :fot';
            $params[':fot'] = $idFotografo;
        }
        $sql .= ' ORDER BY p.id_publicacao';
        $st = Database::conectar()->prepare($sql);
        $st->execute($params);
        $linhas = $st->fetchAll();
        foreach ($linhas as &$l) {
            $l['curtidas'] = (int)$l['curtidas'];
            $l['curtiu'] = (bool)$l['curtiu'];
        }
        return $linhas;
    }

    public static function existe(int $id): bool
    {
        $st = Database::conectar()->prepare('SELECT 1 FROM tb_publicacao WHERE id_publicacao = ?');
        $st->execute([$id]);
        return (bool)$st->fetch();
    }

    public static function criar(string $titulo, string $local, int $idUsuario, string $url): void
    {
        Database::conectar()
            ->prepare('INSERT INTO tb_publicacao (titulo_imagem, local_foto, id_usuario, url_fotografia) VALUES (?,?,?,?)')
            ->execute([$titulo, $local, $idUsuario, $url]);
    }

    public static function urlDoDono(int $id, int $idUsuario): ?string
    {
        $st = Database::conectar()->prepare('SELECT url_fotografia FROM tb_publicacao WHERE id_publicacao = ? AND id_usuario = ?');
        $st->execute([$id, $idUsuario]);
        $p = $st->fetch();
        return $p ? $p['url_fotografia'] : null;
    }

    public static function excluir(int $id): void
    {
        Database::conectar()->prepare('DELETE FROM tb_publicacao WHERE id_publicacao = ?')->execute([$id]);
    }
}
