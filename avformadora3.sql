CREATE DATABASE IF NOT EXISTS cadastro_produtos;

USE cadastro_produtos;

CREATE TABLE IF NOT EXISTS produtos (
  `id_prod` int PRIMARY KEY AUTO_INCREMENT,
  `preco` decimal(10,2) NOT NULL,
  `nomeprod` varchar(255) NOT NULL unique,
  `categorias` varchar(100) NOT NULL,
  `img` varchar(255) NOT NULL,
  `especificacoes` text
);

CREATE TABLE IF NOT EXISTS usuarios (
  `id_usuario` int PRIMARY KEY AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(110) NOT NULL UNIQUE,
  `senha` varchar(255) NOT NULL,
  `login` varchar(45) NOT NULL UNIQUE, 
  `cpf` varchar(15) NOT NULL UNIQUE,
  `telefone` varchar(15) NOT NULL,
  `adm` TINYINT(1) DEFAULT 0
);

UPDATE usuarios SET adm = 1 WHERE login = 'admin';

CREATE TABLE IF NOT EXISTS pedidos (
  `id_pedido` int PRIMARY KEY AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `data_pedido` datetime DEFAULT CURRENT_TIMESTAMP,
  `valor_total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pendente',
  `endereco_entrega` text NOT NULL,
  `forma_pagamento` varchar(50),
  `prazo_entrega` varchar(50),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE IF NOT EXISTS itens_pedido (
  `id_item` int PRIMARY KEY AUTO_INCREMENT,
  `id_pedido` int NOT NULL,
  `id_prod` int NOT NULL,
  `quantidade` int NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
  FOREIGN KEY (id_prod) REFERENCES produtos(id_prod)
);

INSERT INTO usuarios (nome, email, senha, login, cpf, telefone, adm) VALUES 
('Administrador', 'admin@gmail.com', '$2y$10$vP1HS0SqseS3Vqx8heT9Mu0n3CKmw5uSXVgHeM8AqzGm1rQXZXSlW', 'admin', '825.071.850-07', '(21) 99999-9999', 1);


INSERT INTO produtos (id_prod, nomeprod, categorias, preco, img, especificacoes) VALUES 
(1, 'Placa de Vídeo RTX 5080', 'Placas de Vídeo', 7999.90, 'rtx_5080.png', 'A nova RTX 5080 oferece um salto de performance geracional com a arquitetura de última geração da NVIDIA. Ideal para jogos em 4K com Ray Tracing no máximo e para criadores de conteúdo que exigem velocidade.'),
(2, 'Processador Core i9 15900K', 'Processadores', 3899.90, 'i9.png', 'O processador Core i9 15900K é a escolha definitiva para entusiastas e gamers que buscam o máximo de desempenho. Com seus múltiplos núcleos e altas frequências, ele encara qualquer tarefa pesada sem dificuldades.'),
(3, 'SSD NVMe 2TB SuperSpeed', 'Armazenamento', 1199.90, 'ssd_nvme.png', 'Elimine as telas de carregamento com o SSD NVMe de 2TB. Com velocidades de leitura e escrita ultrarrápidas, seus jogos e programas carregarão em um piscar de olhos.'),
(4, 'Memória RAM DDR5 32GB (2x16)', 'Memória RAM', 899.90, 'memoria_ram.png', 'Este kit de 32GB (2x16GB) de memória RAM DDR5 é perfeito para multitarefa e jogos de alta performance. A tecnologia DDR5 garante maior largura de banda e eficiência para o seu sistema.'),
(5, 'Monitor Gamer Husky Storm 27 Pol', 'Monitor', 959.90, 'monitor180hz.png', 'Mergulhe na ação com o monitor gamer curvo Husky Storm de 27 polegadas. Com 180Hz de taxa de atualização e 1ms de tempo de resposta, você terá a vantagem competitiva que precisa.'),
(6, 'Microfone Gamer Fifine Ampligame', 'Microfones', 279.99, 'microfone.png', 'Capture sua voz com clareza cristalina usando o microfone Fifine Ampligame. Seu padrão cardióide foca na sua voz, reduzindo ruídos de fundo, e a iluminação RGB adiciona estilo ao seu setup.'),
(7, 'Pen Drive 64GB Kingston Onyx', 'Pen Drive', 49.99, 'pendrive.png', 'Leve seus arquivos para qualquer lugar com o Pen Drive Kingston DataTraveler de 64GB. Confiável, rápido e com design elegante para o uso diário.'),
(8, 'Mouse Gamer Logitech G305', 'Mouse', 349.90, 'mouselogi.png', 'Liberte-se dos fios com o mouse gamer Logitech G305. A tecnologia sem fio LIGHTSPEED oferece uma resposta de 1ms, e o sensor HERO de 12.000 DPI garante precisão impecável.'),
(9, 'Teclado Mecânico TKL sem fio', 'Teclado', 1399.90, 'tecladotkl.png', 'Compacto e poderoso, este teclado mecânico TKL (Tenkeyless) sem fio oferece a resposta tátil que os gamers amam, em um formato que economiza espaço na sua mesa.'),
(10, 'Headset Gamer Redragon 7.1', 'Headset', 599.90, 'headset.png', 'Ouça cada passo do seu inimigo com o Headset Gamer Redragon. A tecnologia de som surround 7.1 proporciona uma imersão total e áudio posicional preciso para seus jogos.'),
(11, 'PC Gamer Pichau Jotunheim', 'Computadores', 5499.00, 'pcgamer1.png', 'Este PC Gamer é uma máquina de performance. Equipado com um AMD Ryzen 5 e uma RTX 4060 Ti, ele está pronto para rodar os últimos lançamentos com altas taxas de quadros e qualidade gráfica.'),
(12, 'PC Gamer Pichau i5 + RX 7600', 'Computadores', 4099.99, 'pcgamer2.png', 'Uma excelente porta de entrada para o mundo dos jogos. Com um processador Intel i5 e uma placa de vídeo Radeon RX 7600, este PC oferece um ótimo custo-benefício para jogar em Full HD.'),
(13, 'PC Gamer Pichau Afrodite', 'Computadores', 6299.90, 'pcgamer3.png', 'Performance e estilo se encontram neste PC. O poderoso Ryzen 7 5700X combinado com a RTX 4060 Ti e um SSD de 1TB garantem velocidade tanto para jogos quanto para produtividade.'),
(14, 'PC Gamer Pichau Fuzhu XIII', 'Computadores', 8699.99, 'pcgamer4.png', 'Eleve sua experiência de jogo a um novo patamar. Este computador conta com um Intel i7, a poderosa RTX 4070 Super e memória DDR5 para performance extrema em jogos e aplicações profissionais.'),
(15, 'PC Gamer Pichau Highflyer', 'Computadores', 17599.99, 'pcgamer5.png', 'Para quem não aceita nada menos que o máximo. Com um AMD Ryzen 9, a futura RTX 5080, 32GB de RAM e um SSD de 2TB, este é o PC definitivo para entusiastas que buscam poder de fogo absoluto.');
