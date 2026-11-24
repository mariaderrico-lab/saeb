<?php
require_once __DIR__ . '/../Config/Database.php';

class Curtida
{
    public static function alternar(int $idUsuario, int $idPublicacao): array
    {
        $pdo = Database::conectar();
        $st = $pdo->prepare('SELECT id_curtida FROM tb_curtida WHERE id_usuario = ? AND id_publicacao = ?');
        $st->execute([$idUsuario, $idPublicacao]);
        $existe = $st->fetch();

        if ($existe) {
            $pdo->prepare('DELETE FROM tb_curtida WHERE id_curtida = ?')->execute([$existe['id_curtida']]);
        } else {
            $pdo->prepare('INSERT INTO tb_curtida (id_usuario, id_publicacao) VALUES (?, ?)')->execute([$idUsuario, $idPublicacao]);
        }

        $st = $pdo->prepare('SELECT COUNT(*) FROM tb_curtida WHERE id_publicacao = ?');
        $st->execute([$idPublicacao]);
        return [!$existe, (int)$st->fetchColumn()];
    }
}
