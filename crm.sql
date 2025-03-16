-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 13 2025 г., 06:30
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `crm`
--

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `birthday` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `birthday`, `created_at`) VALUES
(1, 'Ivan Ivanov', 'ivan.ivanov@example.com', '+79991234567', '1985-05-15', '2025-01-13 09:21:57'),
(2, 'Maria Petrovna', 'maria.petrova@example.com', '+79991234568', '1990-07-20', '2025-01-13 09:21:57'),
(3, 'Sergei Sidorov', 'sergey.sidorov@example.com', '+79991234569', '1988-03-30', '2025-01-13 09:21:57'),
(6, 'Pedik Petr', 'dmitry.dmitriev@mail.ru', '13895714124', '2025-01-31', '2025-02-03 03:04:50');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `admin` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `order_date`, `total`, `status`, `admin`) VALUES
(3, 1, '2025-01-13 09:25:36', 200.00, '0', 1),
(5, 2, '2025-01-14 03:15:45', 300.00, '0', 1),
(1740721346, 1, '2025-02-28 05:42:26', 250.50, '1', 1),
(1740721350, 2, '2025-02-28 05:42:30', 100.00, '1', 1),
(1740721354, 3, '2025-02-28 05:42:34', 1500.00, '1', 1),
(1740721358, 6, '2025-02-28 05:42:38', 123.00, '1', 1),
(1740721362, 2, '2025-02-28 05:42:42', 1500.00, '1', 1),
(1740721368, 2, '2025-02-28 05:42:48', 100.00, '1', 1),
(1740721378, 3, '2025-02-28 05:42:58', 123.00, '1', 1),
(1740721382, 2, '2025-02-28 05:43:02', 150.50, '0', 1),
(1740721388, 1, '2025-02-28 05:43:08', 123.00, '0', 1),
(1740721393, 1, '2025-02-28 05:43:13', 100.00, '0', 1),
(1740721396, 3, '2025-02-28 05:43:16', 1500.00, '1', 1),
(1740721404, 1, '2025-02-28 05:43:24', 1750.50, '1', 1),
(1740721408, 3, '2025-02-28 05:43:28', 1650.50, '1', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(23, 3, 6, 12, 450.00),
(26, 5, 8, 13, 500.00),
(104, 1740721346, 1, 1, 100.00),
(105, 1740721346, 2, 1, 150.50),
(106, 1740721350, 1, 1, 100.00),
(107, 1740721354, 6, 1, 1500.00),
(108, 1740721358, 8, 1, 123.00),
(109, 1740721362, 6, 1, 1500.00),
(110, 1740721368, 1, 1, 100.00),
(111, 1740721378, 8, 1, 123.00),
(112, 1740721382, 2, 1, 150.50),
(113, 1740721388, 8, 1, 123.00),
(114, 1740721393, 1, 1, 100.00),
(115, 1740721396, 6, 1, 1500.00),
(116, 1740721404, 1, 1, 100.00),
(117, 1740721404, 2, 1, 150.50),
(118, 1740721404, 6, 1, 1500.00),
(119, 1740721408, 2, 1, 150.50),
(120, 1740721408, 6, 1, 1500.00);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`) VALUES
(1, 'Tovar 1', 'Opisanie tovara 1', 100.00, 50),
(2, 'Tovar 2', 'Opisanie tovara 2', 150.50, 30),
(6, 'какашки', 'очень вкусные', 1500.00, 75),
(8, 'товарик3', 'не будет', 123.00, 15),
(9, 'стул', 'кожанный', 500.00, 30);

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `type` enum('tech','crm') NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `clients` int(11) NOT NULL,
  `admin` int(11) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('waiting','work','complete') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tickets`
--

INSERT INTO `tickets` (`id`, `type`, `message`, `clients`, `admin`, `create_at`, `status`) VALUES
(1, 'tech', '1488 dfsghdf ghgdf hgfh fgh ghfh', 2, 1, '2025-03-10 08:46:00', 'work'),
(2, 'tech', 'Ебать мой хуй', 3, 1, '2025-03-10 09:04:50', 'waiting'),
(3, 'crm', 'Бла Бла бла бла бла бла ', 3, 1, '2025-03-13 04:12:48', 'work'),
(4, 'tech', 'ОООООО РРРРРР ДДДДД ВВВВВ ВВВВВ АААААА', 3, 1, '2025-03-13 04:13:22', 'complete'),
(5, 'tech', 'Не работает принтер в отделе продаж. Срочно нужна помощь!', 1, 1, '2025-03-11 04:15:39', 'work'),
(6, 'crm', 'Не могу создать нового клиента в системе. Выдает ошибку при сохранении.', 2, 1, '2025-03-08 04:15:39', 'work'),
(7, 'tech', 'Компьютер постоянно перезагружается. Не могу нормально работать.', 3, 1, '2025-03-12 04:15:39', 'waiting'),
(8, 'crm', 'Необходимо добавить новую категорию товаров в систему.', 1, 1, '2025-03-03 04:15:39', 'complete'),
(9, 'tech', 'Не работает интернет на втором этаже офиса.', 2, 1, '2025-03-10 04:15:39', 'work'),
(10, 'crm', 'Ошибка при формировании отчета по продажам за прошлый месяц.', 3, 1, '2025-03-06 04:15:39', 'complete'),
(11, 'tech', 'Нужно установить новое ПО на рабочие станции отдела маркетинга.', 1, 1, '2025-03-09 04:15:39', 'waiting'),
(12, 'crm', 'Проблема с расчетом скидок для постоянных клиентов.', 2, 1, '2025-03-07 04:15:39', 'work'),
(13, 'tech', 'Требуется настроить новый сервер для базы данных.', 3, 1, '2025-03-05 04:15:39', 'waiting'),
(14, 'crm', 'Не отображается история заказов клиента в профиле.', 1, 1, '2025-03-04 04:15:39', 'complete');

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_message`
--

CREATE TABLE `ticket_message` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `ticket_message`
--

INSERT INTO `ticket_message` (`id`, `ticket_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 1, 'Ебать мой хуй', '2025-03-10 08:46:25');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(256) NOT NULL,
  `type` enum('admin','tech') NOT NULL DEFAULT 'admin',
  `token` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `surname`, `type`, `token`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'kitchen', 'tech', 'bG9naW49YWRtaW4mcGFzc3dvcmQ9YWRtaW4xMjMmdW5pcXVlPTE3NDE4MzQyMTY='),
(2, 'manager', 'manager456', 'Manager', '', 'admin', ''),
(3, 'sales', 'sales789', 'Sales Representative', '', 'admin', '');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `orders_ibfk_1` (`admin`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ticket_message`
--
ALTER TABLE `ticket_message`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1740721234;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1740721409;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `ticket_message`
--
ALTER TABLE `ticket_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`admin`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
