CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE users (
  id             INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  us_name        CHAR(128) NOT NULL,
  us_email       CHAR(128) NOT NULL,
  us_password    CHAR(64) NOT NULL,
  us_image       CHAR,
  us_contacts    TEXT,
  img_url        CHAR(255),
  create_date    DATETIME
);

CREATE TABLE lots (
  id              INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  lot_name        CHAR(255) NOT NULL,
  create_date     DATETIME,
  finish_date     DATETIME,
  category_id     INT NOT NULL,
  start_price     DECIMAL(65,2) UNSIGNED,
  bid_step        DECIMAL(65,2) UNSIGNED,
  lot_description TEXT,
  author_id       INT,
  winner_id       INT
);

CREATE TABLE categories (
  id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  cat_name TINYTEXT
);

CREATE TABLE winners (
  id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id  INT,
  lot_id   INT
);

CREATE TABLE bids (
  id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  bid_date  DATETIME,
  bid_price DECIMAL(65,2) UNSIGNED,
  user_id   INT,
  lot_id    INT
);

CREATE UNIQUE INDEX email ON users(us_email);

CREATE INDEX name ON lots(lot_name);

-- https://drive.google.com/file/d/1o-ofx4cooMnhJba5guPxJMsxMxpkaUfJ/view?usp=sharing