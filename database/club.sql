-- База данных проекта «Единомышленники» (MySQL 8, utf8mb4).
-- Только для ЧИСТОЙ базы: сначала создайте базу `club` и выберите её, затем импортируйте файл.
-- Таблицы не удаляются, поэтому на существующей базе импорт остановится с ошибкой, данные не пострадают.
--
-- Тестовые учётные записи (только для разработки!):
--   admin / admin   — администратор
--   user  / user    — обычный пользователь
--   user2 / user2   — обычный пользователь
-- Пароли в этом файле открытым текстом только ради демо: при первом входе приложение
-- заменяет их хешем (bcrypt). У новых пользователей пароли с самого начала хранятся хешами.

SET NAMES utf8mb4;

CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `flag` tinyint NOT NULL DEFAULT 0 COMMENT '1 = администратор, 0 = обычный пользователь',
  `surname` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `patronymic` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'avatar1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `time` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL COMMENT 'дд.мм.гггг',
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `user` int NOT NULL COMMENT 'организатор',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  CONSTRAINT `event_user_fk` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `participant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event` int NOT NULL,
  `user` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event` (`event`,`user`),
  KEY `user` (`user`),
  CONSTRAINT `participant_event_fk` FOREIGN KEY (`event`) REFERENCES `event` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participant_user_fk` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Тестовые данные
INSERT INTO `user` (`id`, `login`, `password`, `flag`, `surname`, `name`, `patronymic`, `avatar`) VALUES
(1, 'admin', 'admin', 1, 'Админ', 'Админ', 'Админ', 'admin'),
(2, 'user',  'user',  0, 'Иванов', 'Иван', 'Иванович', 'avatar2'),
(3, 'user2', 'user2', 0, 'Петрова', 'Мария', 'Сергеевна', 'avatar3');

INSERT INTO `event` (`id`, `time`, `date`, `title`, `description`, `user`) VALUES
(1, '18:00 - 19:00', '20.12.2026', 'Поход в кино', 'Идём на новый фильм, билеты берём на месте. Будем рады компании!', 2),
(2, '12:00 - 13:00', '22.12.2026', 'Прогулка по набережной', 'Неспешная прогулка и кофе. Приходите всей компанией или в одиночку.', 3),
(3, '19:00 - 20:00', '27.12.2026', 'Настольные игры', 'Собираемся играть в настольные игры, новичкам всё объясним.', 2);

INSERT INTO `participant` (`id`, `event`, `user`) VALUES
(1, 1, 3),
(2, 2, 2);
