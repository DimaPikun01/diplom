-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 14 2024 г., 11:54
-- Версия сервера: 5.7.33
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `repair_shop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `device_models`
--

CREATE TABLE `device_models` (
  `id` int(11) NOT NULL,
  `device_type_id` int(11) NOT NULL,
  `model_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `device_models`
--

INSERT INTO `device_models` (`id`, `device_type_id`, `model_name`) VALUES
(1, 1, 'Iphone 11 pro max'),
(2, 1, 'Iphone 11 pro ');

-- --------------------------------------------------------

--
-- Структура таблицы `device_types`
--

CREATE TABLE `device_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `device_types`
--

INSERT INTO `device_types` (`id`, `type_name`) VALUES
(1, 'Телефон');

-- --------------------------------------------------------

--
-- Структура таблицы `finish_orders`
--

CREATE TABLE `finish_orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `repair_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty_term` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `finish_orders`
--

INSERT INTO `finish_orders` (`id`, `order_id`, `repair_type`, `service_type`, `warranty_term`) VALUES
(1, 1, 'Электрический ремонт', 'Замена дисплея', 'Месяц'),
(2, 2, 'Механический ремонт', 'замена платы', 'Месяц'),
(3, 3, 'Механический ремонт', 'замена платы', 'Месяц');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `technician` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `imei_sn` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `appearance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_type` int(11) DEFAULT NULL,
  `device_model` int(11) DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'новый',
  `creation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `client_first_name`, `client_last_name`, `client_phone`, `manager`, `technician`, `cost`, `reason`, `imei_sn`, `appearance`, `device_type`, `device_model`, `status`, `creation_date`) VALUES
(1, 'Евгений', 'Цыгановв', '+375333566557', 'Иванн', 'Иванн', '120.00', 'не включаетсяя', '13131415', 'Царапина на стеклее', 1, 2, 'готов', '2024-06-13 23:21:44'),
(2, 'Анна', 'Штык', '+375333566552', 'Жека', 'Александр', '10.00', 'залит водой', '645645646', 'Царапина на стекле', 1, 2, 'готов', '2024-06-14 01:44:58'),
(3, 'Александр', 'Штык', '+375333566559', 'Анна', 'Анна', '500.00', 'залит водой', '88888', '-', 1, 1, 'готов', '2024-06-14 02:17:31');

-- --------------------------------------------------------

--
-- Структура таблицы `repair_types`
--

CREATE TABLE `repair_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `repair_types`
--

INSERT INTO `repair_types` (`id`, `type_name`, `description`) VALUES
(1, 'Электрический ремонт', 'использование лабораторных бп, паяльное оборудование'),
(2, 'Механический ремонт', 'восстановление корпуса');

-- --------------------------------------------------------

--
-- Структура таблицы `service_types`
--

CREATE TABLE `service_types` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `service_types`
--

INSERT INTO `service_types` (`id`, `service_name`, `description`) VALUES
(1, 'Замена дисплея', 'замена поврежденного дисплейного модуля на новый'),
(2, 'замена платы', 'замена материнской платы устройства');

-- --------------------------------------------------------

--
-- Структура таблицы `warranty_terms`
--

CREATE TABLE `warranty_terms` (
  `id` int(11) NOT NULL,
  `term_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `months` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `warranty_terms`
--

INSERT INTO `warranty_terms` (`id`, `term_name`, `months`) VALUES
(1, 'Месяц', 6);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `device_models`
--
ALTER TABLE `device_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_type_id` (`device_type_id`);

--
-- Индексы таблицы `device_types`
--
ALTER TABLE `device_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `finish_orders`
--
ALTER TABLE `finish_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `repair_types`
--
ALTER TABLE `repair_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `service_types`
--
ALTER TABLE `service_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `warranty_terms`
--
ALTER TABLE `warranty_terms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `device_models`
--
ALTER TABLE `device_models`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `device_types`
--
ALTER TABLE `device_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `finish_orders`
--
ALTER TABLE `finish_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `repair_types`
--
ALTER TABLE `repair_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `service_types`
--
ALTER TABLE `service_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `warranty_terms`
--
ALTER TABLE `warranty_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `device_models`
--
ALTER TABLE `device_models`
  ADD CONSTRAINT `device_models_ibfk_1` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`);

--
-- Ограничения внешнего ключа таблицы `finish_orders`
--
ALTER TABLE `finish_orders`
  ADD CONSTRAINT `finish_orders_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
