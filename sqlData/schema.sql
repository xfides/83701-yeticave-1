/* Создание самой базы данных */
DROP DATABASE IF EXISTS yeticave;
CREATE DATABASE yeticave COLLATE utf8_general_ci;
USE yeticave;

/* Создание таблицы категорий */
CREATE TABLE `categories` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Создание таблицы users */
CREATE TABLE `users` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `email` varchar(255) NOT NULL,
 `name` varchar(255) NOT NULL,
 `password` varchar(255) NOT NULL,
 `avatar_url` varchar(255) DEFAULT NULL,
 `contacts` text,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Создание таблицы лотов */
CREATE TABLE `lots` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `text` text NOT NULL,
 `url_image` varchar(255) DEFAULT NULL,
 `init_price` int(10) unsigned NOT NULL,
 `rate_step` int(10) unsigned NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `date_end` timestamp NOT NULL,
 `user_winner` int(10) unsigned DEFAULT NULL,
 `user_author` int(10) unsigned NOT NULL,
 `category` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `category` (`category`),
 KEY `user_author` (`user_author`),
 KEY `user_winner` (`user_winner`),
 CONSTRAINT `lots_ibfk_1` FOREIGN KEY (`category`) REFERENCES `categories` (`id`),
 CONSTRAINT `lots_ibfk_2` FOREIGN KEY (`user_author`) REFERENCES `users` (`id`),
 CONSTRAINT `lots_ibfk_3` FOREIGN KEY (`user_winner`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Создание таблицы ставок */
CREATE TABLE `rates` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `price` int(10) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `lot` int(10) unsigned NOT NULL,
 `user` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `user` (`user`),
 KEY `lot` (`lot`),
 CONSTRAINT `rates_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
 CONSTRAINT `rates_ibfk_2` FOREIGN KEY (`lot`) REFERENCES `lots` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;