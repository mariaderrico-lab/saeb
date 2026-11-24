<?php

class Database
{
    private const HOST = 'localhost';
    private const NOME = 'saepvision';
    private const USUARIO = 'root';
    private const SENHA = '';

    private static ?PDO $pdo = null;

    public static function conectar(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = new PDO(
                'mysql:host=' . self::HOST . ';dbname=' . self::NOME . ';charset=utf8mb4',
                self::USUARIO,
                self::SENHA,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        }
        return self::$pdo;
    }
}
