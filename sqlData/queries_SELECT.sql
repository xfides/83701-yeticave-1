/* выбираем базу данных */
USE `yeticave`;

/*-------------------------------------------------------------------*/
/* получить список всех категорий */
SELECT *
FROM `categories`;


/*-------------------------------------------------------------------*/
/* получить самые новые, открытые лоты.
Каждый лот должен включать
  - название,                   => lots.name
  - стартовую цену,             => lots.init_price
  - ссылку на изображение,      => lots.url_image
  - цену,                       => rates.price
  - количество ставок,          => rates.id_lot
  - название категории;         => categories.name
  ``````````````````````````````````````````````````````````
  - новые;                      => lots.date_create
  - открытые;                   => lots.user_winner
*/

SELECT
  lots.name,
  lots.init_price,
  lots.url_image,
  MAX(rates.price) AS max_price,
  COUNT(rates.lot)   AS count_rates,
  categories.name

FROM `lots`
  JOIN `rates` ON lots.id = rates.lot
  JOIN `categories` ON lots.category = categories.id

WHERE lots.created_at > '2017.08.01'
      AND lots.user_winner IS NULL
GROUP BY lots.id;


/*-------------------------------------------------------------------*/
/* найти лот по его названию или описанию; */
SELECT *
FROM `lots`
WHERE lots.name LIKE '%Snowboard%'
      OR
      lots.text LIKE '%District%'
GROUP BY lots.name;


/*-------------------------------------------------------------------*/
/* получить список самых свежих ставок для лота по его идентификатору */
SELECT *
FROM `rates`
WHERE rates.lot = 3
      AND rates.created_at > '2017.08.01'
ORDER BY rates.created_at DESC
LIMIT 4;