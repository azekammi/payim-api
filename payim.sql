-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 25 2018 г., 19:53
-- Версия сервера: 5.6.37
-- Версия PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `payim`
--

-- --------------------------------------------------------

--
-- Структура таблицы `all_users`
--

CREATE TABLE `all_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `account_id` int(7) NOT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `token` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `all_users`
--

INSERT INTO `all_users` (`id`, `username`, `password`, `type`, `account_id`, `balance`, `token`) VALUES
(1, 'mamed', '$2y$10$duN0Mld6vLXJbbh4SUUJLOleaoJSYXb9xtei5uvO3rP7gQnvxoQB.', 0, 1200001, 10340, 'L6BZ3A2VR3B499W'),
(2, 'lady5xl', '$2y$10$duN0Mld6vLXJbbh4SUUJLOleaoJSYXb9xtei5uvO3rP7gQnvxoQB.', 1, 1200002, 160, '5WSSW0OGNZVZTEJ'),
(3, 'mahir', '$2y$10$duN0Mld6vLXJbbh4SUUJLOleaoJSYXb9xtei5uvO3rP7gQnvxoQB.', 1, 1200003, 0, '8M4XXR3I073R9Y8'),
(4, 'patio', '$2y$10$duN0Mld6vLXJbbh4SUUJLOleaoJSYXb9xtei5uvO3rP7gQnvxoQB.', 1, 1200004, 0, NULL),
(5, 'panda_kids', '$2y$10$duN0Mld6vLXJbbh4SUUJLOleaoJSYXb9xtei5uvO3rP7gQnvxoQB.', 1, 1200005, 0, NULL),
(6, 'flowers_of_baku', '$2y$10$duN0Mld6vLXJbbh4SUUJLOleaoJSYXb9xtei5uvO3rP7gQnvxoQB.', 1, 1200006, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `business_categories`
--

CREATE TABLE `business_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `business_categories`
--

INSERT INTO `business_categories` (`id`, `name`) VALUES
(1, 'Geyim mağazaları'),
(2, 'Məişət texnikası'),
(3, 'Restoran və kafe'),
(4, 'Market'),
(5, 'Turizm'),
(6, 'İdman və əyləncə'),
(7, 'Online alış-veriş'),
(8, 'Səhiyyə'),
(9, 'Ehtiyat hissələri və avtoservi'),
(10, 'Gül salonu'),
(11, 'Kulinariya'),
(12, 'Gözəllik və sağlamlıq'),
(13, 'Optika'),
(14, 'Parfum mağazaları'),
(15, 'Uşaq aləmi'),
(16, 'Digər mağazalar/xidmətlər'),
(17, 'Hotel və istirahət mərkəzləri'),
(18, 'Aksesuar/Suvenir/Zinyət əşyaları'),
(19, 'Hotel və istirahət mərkəzləri'),
(20, 'Ehtiyat hissələri və avtoservis mərkəzləri');

-- --------------------------------------------------------

--
-- Структура таблицы `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `generated_code` int(11) NOT NULL,
  `business_user_id` int(11) NOT NULL,
  `discount` tinyint(3) NOT NULL,
  `amount` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `payments`
--

INSERT INTO `payments` (`id`, `generated_code`, `business_user_id`, `discount`, `amount`, `status`) VALUES
(1, 6110, 2, 20, 200, 1),
(2, 3321, 2, 20, 50, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `transactions`
--

INSERT INTO `transactions` (`id`, `from_user_id`, `to_user_id`, `payment_id`, `date`) VALUES
(4, 1, 2, 1, '2018-11-25 09:36:37');

-- --------------------------------------------------------

--
-- Структура таблицы `users_businesses`
--

CREATE TABLE `users_businesses` (
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `category_id` int(11) NOT NULL,
  `discount` tinyint(3) NOT NULL,
  `image` varchar(50) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users_businesses`
--

INSERT INTO `users_businesses` (`user_id`, `name`, `description`, `category_id`, `discount`, `image`, `logo`, `is_popular`) VALUES
(2, 'Lady 5XL', 'Lady 5XL', 1, 20, 'lady5xl.jpg', 'lady5xl.jpg', 0),
(3, 'Euromoda', 'Euromoda', 1, 10, 'euromoda.jpg', 'euromoda.jpg', 0),
(4, 'Il Patio', 'İl Patio & Planet Sushi restoranı', 3, 5, 'il_patio.jpg', 'il_patio.jpg', 1),
(5, 'Panda Kids', 'Panda Kids', 15, 10, 'panda_kids.jpg', 'panda_kids.jpg', 0),
(6, 'Flowers of Baku', 'Flowers of Baku', 10, 5, 'flowers_1.png', 'flowers_0.png', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users_customers`
--

CREATE TABLE `users_customers` (
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users_customers`
--

INSERT INTO `users_customers` (`user_id`, `name`, `surname`) VALUES
(1, 'Memmed', 'Memmedov');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `all_users`
--
ALTER TABLE `all_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `business_categories`
--
ALTER TABLE `business_categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users_businesses`
--
ALTER TABLE `users_businesses`
  ADD PRIMARY KEY (`user_id`);

--
-- Индексы таблицы `users_customers`
--
ALTER TABLE `users_customers`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `all_users`
--
ALTER TABLE `all_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `business_categories`
--
ALTER TABLE `business_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT для таблицы `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT для таблицы `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
