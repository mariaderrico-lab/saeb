<?php
require_once __DIR__ . '/../Config/Database.php';

class Usuario
{
    public static function buscarPorEmail(string $email): ?array
    {
        $st = Database::conectar()->prepare('SELECT id_usuario, senha FROM tb_usuario WHERE email = ?');
        $st->execute([$email]);
        return $st->fetch() ?: null;
    }

    public static function buscarPorId(int $id): ?array
    {
        $st = Database::conectar()->prepare('SELECT id_usuario, nome, nome_usuario, imagem_usuario, tipo FROM tb_usuario WHERE id_usuario = ?');
        $st->execute([$id]);
        $u = $st->fetch();
        if (!$u) {
            return null;
        }
        $u['imagem'] = 'assets/usuarios/' . $u['imagem_usuario'];
        return $u;
    }

    public static function buscarFotografo(string $termo): ?array
    {
        $st = Database::conectar()->prepare("SELECT id_usuario FROM tb_usuario WHERE tipo = 'fotografo' AND (nome = ? OR nome_usuario = ?)");
        $st->execute([$termo, $termo]);
        return $st->fetch() ?: null;
    }

    public static function totalPublicacoes(int $id): int
    {
        $st = Database::conectar()->prepare('SELECT COUNT(*) FROM tb_publicacao WHERE id_usuario = ?');
        $st->execute([$id]);
        return (int)$st->fetchColumn();
    }

    public static function totalLikes(int $id): int
    {
        $st = Database::conectar()->prepare(
            'SELECT COUNT(*) FROM tb_curtida c JOIN tb_publicacao p ON p.id_publicacao = c.id_publicacao WHERE p.id_usuario = ?'
        );
        $st->execute([$id]);
        return (int)$st->fetchColumn();
    }
}
