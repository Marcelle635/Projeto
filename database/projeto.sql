
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/11/2025 às 16:04
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
(13, NULL, 'comedi', '2fa_ok', '2FA correta (campo: nome_materno)', '::1', '2025-11-09 12:51:37'),
(14, 1, 'admin1', 'login_fail', 'senha incorreta', '::1', '2025-11-13 11:45:54'),
(15, 1, 'admin1', 'login_fail', 'senha incorreta', '::1', '2025-11-13 11:48:12'),
(16, 1, 'admin1', 'login_fail', 'senha incorreta', '::1', '2025-11-13 11:48:16'),
(17, 1, 'admin1', 'login_fail', 'senha incorreta', '::1', '2025-11-13 11:48:22'),
(18, 1, 'admin1', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-13 11:50:17'),
(19, 8, 'joao', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-13 12:04:16'),
(20, 1, 'admin1', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-14 19:48:58'),
(21, 8, 'joao', 'login_fail', 'senha incorreta', '::1', '2025-11-14 19:55:50'),
(22, 8, 'joao', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-14 20:04:27'),
(23, 1, 'admin1', 'login_ok', 'senha validada, aguardando 2FA', '::1', '2025-11-14 20:04:59');

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
(1, 'Admin Master', '1980-01-01', '12345678901', 'Master Admin', '(21)99999-9999', 'admin1', 'Masculino', '25f43b1486ad95a1398e3eeb3d83bc4010015fcc9bedb35b432e00298d5021f7', 'admin@example.com', 'Rua Admin, Centro, RJ', 'master'),
(8, 'João Silva', '1995-04-10', '22222222222', 'Ana', '(21)98888-1111', 'joao', 'Masculino', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'joao@raizes.com', 'Rua Café, 200', 'comum'),
(9, 'Maria Souza', '1998-08-22', '33333333333', 'Clara', '(21)97777-2222', 'maria', 'Feminino', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'maria@raizes.com', 'Av. Brasil, 350', 'comum'),
(10, 'Carlos Pereira', '1992-11-03', '44444444444', 'Helena', '(21)96666-3333', 'carlos', 'Masculino', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'carlos@raizes.com', 'Rua dos Ipês, 45', 'comum'),
(11, 'Fernanda Oliveira', '1999-03-27', '55555555555', 'Lucia', '(21)95555-4444', 'fernan', 'Feminino', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'fernanda@raizes.com', 'Rua das Flores, 77', 'comum');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
