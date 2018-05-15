-- Существующий список категорий
INSERT INTO categories (id, cat_name)
values (1, 'Доски и лыжи'),(2, 'Крепления'),(3, 'Ботинки'),(4, 'Одежда'),(6, 'Инструменты'),(7, 'Разное');

-- Придумываю пару пользователей

INSERT INTO users
SET us_name = 'Врушин Тлад', us_email = 'tlad2018@test.com', us_password = MD5('strongPassword'), create_date = '2018-05-07';

INSERT INTO users
SET us_name = 'Горячвина Устина', us_email = 'ustia-look@testik.org', us_password = MD5('weekPassword'), create_date = '2018-05-07';

-- Список объявлений

INSERT INTO lots
SET lot_name = '2014 Rossignol District Snowboard', create_date = '2018-05-05', category_id = 1, start_price = 10999,
 bid_step = 100, img_url = 'img/lot-1.jpg', lot_description = 'Крутая доска для новичков', author_id = 1;

INSERT INTO lots
SET lot_name = 'DC Ply Mens 2016/2017 Snowboard', create_date = '2018-05-06', category_id = 1, start_price = 15999,
 bid_step = 200, img_url = 'img/lot-2.jpg', lot_description = 'Хорошая доска для новичков', author_id = 1;

INSERT INTO lots
SET lot_name = 'Крепления Union Contact Pro 2015 года размер L/XL', create_date = '2018-05-07', category_id = 2, start_price = 8000,
 bid_step = 50, img_url = 'img/lot-3.jpg', lot_description = 'б/у пару раз', author_id = 1;

INSERT INTO lots
SET lot_name = 'Ботинки для сноуборда DC Mutiny Charocal', create_date = '2018-05-09', category_id = 3, start_price = 10999,
 bid_step = 150, img_url = 'img/lot-4.jpg', lot_description = 'Серые ботинки с серыми шнурками. Ваш Кэп', author_id = 1;

INSERT INTO lots
SET lot_name = 'Куртка для сноуборда DC Mutiny Charocal', create_date = '2018-05-06', category_id = 4, start_price = 7500.33,
 bid_step = 200, img_url = 'img/lot-5.jpg', lot_description = 'Очень теплая', author_id = 1;

INSERT INTO lots
SET lot_name = 'Маска Oakley Canopy', create_date = '2018-05-04', category_id = 7, start_price = 10999.93,
 bid_step = 100, img_url = 'img/lot-6.jpg', lot_description = 'Дарит отличный загар', author_id = 1;

-- Пара ставок для любого объявления

INSERT INTO bids
SET bid_date = '2018-05-08', bid_price = 10999, user_id = 2, lot_id = 1;

INSERT INTO bids
SET bid_date = '2018-05-08', bid_price = 11199, user_id = 2, lot_id = 1;

--------
-- Получить все категории;

SELECT * FROM categories;  -- использовать звездочку нежелательно во встроенных запросах, поэтому ниже второй
SELECT id, cat_name FROM categories;

-- получить самые новые, открытые лоты.
-- Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, количество ставок, название категории;

SELECT l.lot_name, l.start_price, l.img_url, MAX(b.bid_price) AS cur_price, cat.cat_name, COUNT(b.lot_id) AS bids_qty
FROM lots l
  LEFT JOIN bids b ON b.lot_id=l.id
  LEFT JOIN categories cat ON cat.id=l.category_id
WHERE winner_id IS NULL
GROUP BY l.id;;

-- Показать лот по его id. Получите также название категории, к которой принадлежит лот
-- Можно со звездочкой, но так не стоит делать.

SELECT l.lot_name, l.start_price, c.cat_name FROM lots l, categories c
WHERE l.category_id=c.id AND l.id = 3;

-- Обновить название лота по его идентификатору;

UPDATE lots SET lot_name = 'Крепления Union Contact Pro 2015 года размер L/XL/ML' WHERE id = 3;

-- Получить список самых свежих ставок для лота по его идентификатору

SELECT * FROM bids WHERE lot_id = 1 ORDER BY bid_date DESC;