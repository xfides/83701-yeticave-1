/* выбираем базу данных*/
USE `yeticave`;

/* заполняем таблицу категорий */
INSERT INTO `categories` (`name`) VALUES
  ('Доски и Лыжи'),
  ('Крепления'),
  ('Ботинки'),
  ('Одежда'),
  ('Инструменты'),
  ('Разное');

/* заполняем таблицу пользователей */
INSERT INTO `users`
(
  `email`,
  `name`,
  `password`,
  `created_at`
)
VALUES
  (
    'ignat.v@gmail.com',
    'Игнат',
    '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka',
    '2017-12-01 00:00:00'
  ),
  (
    'kitty_93@li.ru',
    'Леночка',
    '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa',
    '2017-11-03 09:12:00'
  ),
  (
    'warrior07@mail.ru',
    'Руслан',
    '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW',
    '2017-10-12 10:00:00'
  );

/* заполняем таблицу лотов */
INSERT INTO `lots` (
  `name`,
  `text`,
  `url_image`,
  `init_price`,
  `rate_step`,
  `created_at`,
  `date_end`,
  `user_author`,
  `category`
)
VALUES
  (
    '2014 Rossignol District Snowboard',
    'Текст с описанием 2014 Rossignol District Snowboard',
    'img/lot-1.jpg',
    '10999',
    '100',
    '2017-08-01 23:00:00',
    '2018-12-20 00:00:00',
    '1',
    '1'
  ),
  (
    'DC Ply Mens 2016/2017 Snowboard',
    'Текст с описанием DC Ply Mens 2016/2017 Snowboard',
    'img/lot-2.jpg',
    '15999',
    '100',
    '2017-09-01 23:00:00',
    '2018-12-18 12:22:00',
    '2',
    '1'
  ),
  (
    'Крепления Union Contact Pro 2015 года размер L/XL',
    'Текст с описанием Крепления Union Contact Pro 2015 года размер L/XL',
    'img/lot-3.jpg',
    '8000',
    '100',
    '2017-10-01 22:00:00',
    '2018-12-22 13:01:00',
    '2',
    '2'
  ),
  (
    'Ботинки для сноуборда DC Mutiny Charocal',
    'Текст с описанием Ботинки для сноуборда DC Mutiny Charocal',
    'img/lot-4.jpg',
    '10999',
    '100',
    '2017-11-01 21:00:00',
    '2018-11-06 14:00:00',
    '3',
    '3'
  ),
  (
    'Куртка для сноуборда DC Mutiny Charocal',
    'Текст с описанием Куртка для сноуборда DC Mutiny Charocal',
    'img/lot-5.jpg',
    '7500',
    '100',
    '2017-12-01 20:00:00',
    '2018-12-30 15:00:00',
    '1',
    '4'
  ),
  (
    'Маска Oakley Canopy',
    'Текст с описанием Маска Oakley Canopy',
    'img/lot-6.jpg',
    '10999',
    '5400',
    '2017-07-01 19:00:00',
    '2018-12-17 16:22:33',
    '1',
    '6'
  );

/* заполняем таблицу ставок */
INSERT INTO `rates`
(
  `price`,
  `created_at`,
  `lot`,
  `user`
)
VALUES
  (
    '15000',
    '2018-04-03 00:20:00',
    '4',
    '2'
  ),
  (
    '22999',
    '2018-03-18 12:30:00',
    '3',
    '1'
  ),
  (
    '18342',
    '2018-06-20 23:20:22',
    '1',
    '3'
  );