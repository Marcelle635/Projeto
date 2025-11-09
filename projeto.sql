-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/11/2025 às 17:00
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projeto`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `login_via` varchar(50) DEFAULT NULL,
  `acao` enum('login_attempt','login_ok','login_fail','2fa_ok','2fa_fail') NOT NULL,
  `detalhe` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `data_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `login_via`, `acao`, `detalhe`, `ip`, `data_hora`) VALUES
(1, NULL, 'Admin Master', 'login_attempt', 'usuario nao encontrado', '::1', '2025-11-04 21:09:37'),
(2, NULL, 'Admin Master', 'login_attempt', 'usuario nao encontrado', '::1', '2025-11-04 21:09:45'),
(3, NULL, 'Admin Master', 'login_attempt', 'usuario nao encontrado', '::1', '2025-11-04 21:09:51'),
(4, NULL, 'Admin Master', 'login_attempt', 'usuario nao encontrado', '::1', '2025-11-04 21:11:23'),
(5, NULL, 'Admin Master', 'login_attempt', 'usuario nao encontrado', '::1', '2025-11-04 21:12:41'),
(6, 1, 'admin1', 'login_fail', 'senha incorreta', '::1', '2025-11-04 21:15:33'),
(7, 1, 'admin1', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-04 21:17:33'),
(8, 1, 'admin1', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-04 21:21:12'),
(9, 1, 'admin1', '2fa_ok', '2FA correta (campo: data_nasc)', '::1', '2025-11-04 21:21:45'),
(10, 1, 'admin1', 'login_fail', 'senha incorreta', '::1', '2025-11-09 12:16:41'),
(11, NULL, 'comedi', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-09 12:17:31'),
(12, NULL, 'comedi', '2fa_fail', 'Resposta incorreta (campo: nome_materno) tentativa 1', '::1', '2025-11-09 12:51:06'),
(13, NULL, 'comedi', '2fa_ok', '2FA correta (campo: nome_materno)', '::1', '2025-11-09 12:51:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `data_nasc` date NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `nome_materno` varchar(80) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `login` varchar(6) NOT NULL,
  `sexo` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `endereco` varchar(200) NOT NULL,
  `perfil` enum('master','comum') DEFAULT 'comum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `data_nasc`, `cpf`, `nome_materno`, `telefone`, `login`, `sexo`, `senha`, `email`, `endereco`, `perfil`) VALUES
(1, 'Admin Master', '1980-01-01', '12345678901', 'Master Admin', '(21)99999-9999', 'admin1', 'Masculino', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin@example.com', 'Rua Admin, Centro, RJ', 'master');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
