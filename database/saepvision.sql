estrutura física do banco de dados (MySQL)
DROP DATABASE IF EXISTS saepvision;
CREATE DATABASE saepvision CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saepvision;

CREATE TABLE tb_usuario (
  id_usuario     INT AUTO_INCREMENT PRIMARY KEY,
  nome           VARCHAR(100) NOT NULL,
  nome_usuario   VARCHAR(100) NOT NULL UNIQUE,
  email          VARCHAR(150) NOT NULL UNIQUE,
  senha          VARCHAR(255) NOT NULL,
  imagem_usuario VARCHAR(255) NOT NULL,
  tipo           ENUM('usuario','fotografo') NOT NULL DEFAULT 'usuario',
  created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tb_publicacao (
  id_publicacao  INT AUTO_INCREMENT PRIMARY KEY,
  titulo_imagem  VARCHAR(150) NOT NULL,
  local_foto     VARCHAR(150) NOT NULL,
  id_usuario     INT NOT NULL,
  url_fotografia VARCHAR(255) NOT NULL,
  created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pub_usuario FOREIGN KEY (id_usuario)
    REFERENCES tb_usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tb_curtida (
  id_curtida    INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario    INT NOT NULL,
  id_publicacao INT NOT NULL,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_curtida (id_usuario, id_publicacao),
  CONSTRAINT fk_cur_usuario FOREIGN KEY (id_usuario)
    REFERENCES tb_usuario(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_cur_pub FOREIGN KEY (id_publicacao)
    REFERENCES tb_publicacao(id_publicacao) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO tb_usuario (id_usuario,nome,nome_usuario,email,senha,imagem_usuario,tipo,created_at,updated_at) VALUES
(1,'fotografo1','fotografo_1','fotografo1@email.com','123456','fotografo1.jpg','fotografo','2025-03-25 12:40:08','2025-03-25 12:40:08'),
(2,'fotografo2','fotografo_2','fotografo2@gmail.com','123456','fotografo2.jpg','fotografo','2025-03-25 12:42:56','2025-03-25 12:42:56'),
(3,'fotografo3','fotografo_3','fotografo3@gmail.com','123456','fotografo3.jpg','fotografo','2025-03-25 12:45:44','2025-03-25 12:45:44'),
(4,'usuario1','usuario_1','usuario1@gmail.com','123456','usuario1.jpg','usuario','2025-03-25 12:48:32','2025-03-25 12:48:32'),
(5,'usuario2','usuario_2','usuario2@gmail.com','123456','usuario2.jpg','usuario','2025-03-25 12:51:20','2025-03-25 12:51:20'),
(6,'usuario3','usuario_3','usuario3@gmail.com','123456','usuario3.jpg','usuario','2025-03-25 12:54:08','2025-03-25 12:54:08');

INSERT INTO tb_publicacao (id_publicacao,titulo_imagem,local_foto,id_usuario,url_fotografia) VALUES
(1,'Foto 1','Rio de Janeiro',1,'assets/fotografias/foto1.jpg'),
(2,'Foto 2','São Paulo',1,'assets/fotografias/foto2.jpg'),
(3,'Foto 3','Salvador',1,'assets/fotografias/foto3.jpg'),
(4,'Foto 4','Recife',2,'assets/fotografias/foto4.jpg'),
(5,'Foto 5','Curitiba',2,'assets/fotografias/foto5.jpg'),
(6,'Foto 6','Belo Horizonte',2,'assets/fotografias/foto6.jpg'),
(7,'Foto 7','Fortaleza',3,'assets/fotografias/foto7.jpg'),
(8,'Foto 8','Porto Alegre',3,'assets/fotografias/foto8.jpg'),
(9,'Foto 9','Brasília',3,'assets/fotografias/foto9.jpg');

INSERT INTO tb_curtida (id_usuario,id_publicacao) VALUES
(4,1),(5,1),(6,1),(4,2),(5,3),(6,4),(4,5),(5,5),(1,7),(2,8),(3,9),(4,9);
