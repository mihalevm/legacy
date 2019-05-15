-- --------------------------------------------------------
-- Хост:                         legacy.dtelecom.ru
-- Версия сервера:               5.7.26-0ubuntu0.18.04.1 - (Ubuntu)
-- Операционная система:         Linux
-- HeidiSQL Версия:              9.5.0.5447
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных legacy
DROP DATABASE IF EXISTS `legacy`;
CREATE DATABASE IF NOT EXISTS `legacy` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `legacy`;

-- Дамп структуры для таблица legacy.lgc_bcards
DROP TABLE IF EXISTS `lgc_bcards`;
CREATE TABLE IF NOT EXISTS `lgc_bcards` (
  `cid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификаторномера карты',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания карты',
  `cnum` int(11) NOT NULL COMMENT 'Номер карты',
  `bsumm` float NOT NULL DEFAULT '0' COMMENT 'Бонусный баланс',
  `days` int(11) NOT NULL DEFAULT '0' COMMENT 'Срок действия карты',
  `disabled` char(1) NOT NULL DEFAULT 'N' COMMENT 'Признак блокировки',
  `is_used` char(1) DEFAULT 'N' COMMENT 'Признак использования карты',
  PRIMARY KEY (`cid`),
  UNIQUE KEY `unq_cnum` (`cnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица бонусных карт';

-- Дамп данных таблицы legacy.lgc_bcards: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_bcards` DISABLE KEYS */;
/*!40000 ALTER TABLE `lgc_bcards` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_btransactions
DROP TABLE IF EXISTS `lgc_btransactions`;
CREATE TABLE IF NOT EXISTS `lgc_btransactions` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор транзакции',
  `tdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата транзакции',
  `cid` int(11) NOT NULL COMMENT 'Идентификатор бонусной карты',
  `uid` int(11) NOT NULL COMMENT 'Идентификатор пользователя',
  `ttype` char(1) NOT NULL COMMENT 'Тип операции (начисление\\списание)',
  `summ` float DEFAULT NULL COMMENT 'Сумма покупки',
  `bsumm` float DEFAULT NULL COMMENT 'Сумма бонусных баллов',
  `tdesc` varchar(255) DEFAULT NULL COMMENT 'Описание покупки',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица транзакций';

-- Дамп данных таблицы legacy.lgc_btransactions: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_btransactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `lgc_btransactions` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_clients
DROP TABLE IF EXISTS `lgc_clients`;
CREATE TABLE IF NOT EXISTS `lgc_clients` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор клиента',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания',
  `fio` varchar(255) DEFAULT NULL COMMENT 'ФИО',
  `phone` varchar(32) DEFAULT NULL COMMENT 'Номер телефона',
  `birthday` date DEFAULT NULL COMMENT 'Дата рождения',
  `sex` int(11) DEFAULT '1' COMMENT 'Пол',
  `style` varchar(255) DEFAULT NULL COMMENT 'Стиль одежды',
  `did` int(11) DEFAULT NULL COMMENT 'Идентификатор размера одежды',
  `fid` int(11) DEFAULT NULL COMMENT 'Идентификатор размера обуви',
  `cid` int(11) DEFAULT NULL COMMENT 'Идентификатор бонусной карты',
  `disabled` char(1) DEFAULT 'N' COMMENT 'Признак блокировки пользователя',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица клиентов';

-- Дамп данных таблицы legacy.lgc_clients: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `lgc_clients` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_dsize
DROP TABLE IF EXISTS `lgc_dsize`;
CREATE TABLE IF NOT EXISTS `lgc_dsize` (
  `did` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор значения',
  `value` varchar(127) DEFAULT NULL COMMENT 'Текстовое описание',
  PRIMARY KEY (`did`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица словаря размера одежды';

-- Дамп данных таблицы legacy.lgc_dsize: ~37 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_dsize` DISABLE KEYS */;
INSERT INTO `lgc_dsize` (`did`, `value`) VALUES
	(1, '34'),
	(2, '35'),
	(3, '36'),
	(4, '37'),
	(5, '38'),
	(6, '39'),
	(7, '40'),
	(8, '41'),
	(9, '42'),
	(10, '43'),
	(11, '44'),
	(12, '45'),
	(13, '46'),
	(14, '47'),
	(15, '48'),
	(16, '49'),
	(17, '50'),
	(18, '51'),
	(19, '52'),
	(20, '53'),
	(21, '54'),
	(22, '55'),
	(23, '56'),
	(24, '57'),
	(25, '58'),
	(26, '59'),
	(27, '60'),
	(28, '61'),
	(29, '62'),
	(30, '63'),
	(31, '64'),
	(32, '65'),
	(33, '66'),
	(34, '67'),
	(35, '68'),
	(36, '69'),
	(37, '70+');
/*!40000 ALTER TABLE `lgc_dsize` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_fsize
DROP TABLE IF EXISTS `lgc_fsize`;
CREATE TABLE IF NOT EXISTS `lgc_fsize` (
  `fid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор значения',
  `value` varchar(127) DEFAULT NULL COMMENT 'Текстовое описание',
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица словаря размера обуви';

-- Дамп данных таблицы legacy.lgc_fsize: ~41 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_fsize` DISABLE KEYS */;
INSERT INTO `lgc_fsize` (`fid`, `value`) VALUES
	(1, '34'),
	(2, '34.5'),
	(3, '35'),
	(4, '35.5'),
	(5, '36'),
	(6, '36.5'),
	(7, '37'),
	(8, '37.5'),
	(9, '38'),
	(10, '38.5'),
	(11, '39'),
	(12, '39.5'),
	(13, '40'),
	(14, '40.5'),
	(15, '41'),
	(16, '41.5'),
	(17, '42'),
	(18, '42.5'),
	(19, '43'),
	(20, '43.5'),
	(21, '44'),
	(22, '44.5'),
	(23, '45'),
	(24, '45.5'),
	(25, '46'),
	(26, '46.5'),
	(27, '47'),
	(28, '47.5'),
	(29, '48'),
	(30, '48.5'),
	(31, '49'),
	(32, '49.5'),
	(33, '50'),
	(34, '50.5'),
	(35, '51'),
	(36, '51.5'),
	(37, '52'),
	(38, '52.5'),
	(39, '53'),
	(40, '53.5'),
	(41, '54');
/*!40000 ALTER TABLE `lgc_fsize` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
