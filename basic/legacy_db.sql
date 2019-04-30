-- --------------------------------------------------------
-- Хост:                         91.227.140.69
-- Версия сервера:               5.0.84 - Source distribution
-- Операционная система:         redhat-linux-gnu
-- HeidiSQL Версия:              9.5.0.5447
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных legacy
CREATE DATABASE IF NOT EXISTS `legacy` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `legacy`;

-- Дамп структуры для таблица legacy.lgc_bcards
CREATE TABLE IF NOT EXISTS `lgc_bcards` (
  `cid` int(11) NOT NULL auto_increment COMMENT 'Идентификаторномера карты',
  `cdate` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'Дата создания карты',
  `cnum` int(11) NOT NULL COMMENT 'Номер карты',
  `bsumm` float NOT NULL default '0' COMMENT 'Бонусный баланс',
  `days` int(11) NOT NULL default '0' COMMENT 'Срок действия карты',
  `disabled` char(1) NOT NULL default 'N' COMMENT 'Признак блокировки',
  `is_used` char(1) default 'N' COMMENT 'Признак использования карты',
  PRIMARY KEY  (`cid`),
  UNIQUE KEY `unq_cnum` (`cnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица бонусных карт';

-- Дамп данных таблицы legacy.lgc_bcards: ~12 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_bcards` DISABLE KEYS */;
INSERT INTO `lgc_bcards` (`cid`, `cdate`, `cnum`, `bsumm`, `days`, `disabled`, `is_used`) VALUES
	(1, '2019-04-29 14:23:50', 1, 0, 0, 'N', 'Y'),
	(2, '2019-04-29 14:23:50', 2, 0, 0, 'N', 'Y'),
	(3, '2019-04-29 14:36:36', 3, 0, 0, 'N', 'Y'),
	(4, '2019-04-29 14:36:36', 4, 0, 0, 'N', 'Y'),
	(5, '2019-04-29 14:36:36', 5, 0, 0, 'N', 'Y'),
	(6, '2019-04-29 14:38:32', 6, 0, 0, 'N', 'Y'),
	(7, '2019-04-29 14:38:38', 7, 0, 0, 'N', 'Y'),
	(8, '2019-04-29 14:44:59', 8, 10, 33, 'N', 'N'),
	(9, '2019-04-29 14:44:59', 9, 10, 33, 'N', 'N'),
	(10, '2019-04-29 14:44:59', 10, 10, 33, 'N', 'N'),
	(11, '2019-04-29 14:49:13', 11, 0, 0, 'N', 'N'),
	(12, '2019-04-29 14:49:13', 12, 0, 0, 'N', 'N');
/*!40000 ALTER TABLE `lgc_bcards` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_btransactions
CREATE TABLE IF NOT EXISTS `lgc_btransactions` (
  `tid` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор транзакции',
  `tdate` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'Дата транзакции',
  `cid` int(11) NOT NULL COMMENT 'Идентификатор бонусной карты',
  `uid` int(11) NOT NULL COMMENT 'Идентификатор пользователя',
  `ttype` char(1) NOT NULL COMMENT 'Тип операции (начисление\\списание)',
  `summ` float default NULL COMMENT 'Сумма покупки',
  `bsumm` float default NULL COMMENT 'Сумма бонусных баллов',
  `tdesc` varchar(255) default NULL COMMENT 'Описание покупки',
  PRIMARY KEY  (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица транзакций';

-- Дамп данных таблицы legacy.lgc_btransactions: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_btransactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `lgc_btransactions` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_clients
CREATE TABLE IF NOT EXISTS `lgc_clients` (
  `uid` int(11) NOT NULL auto_increment COMMENT 'Идентификатор клиента',
  `cdate` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'Дата создания',
  `fio` varchar(255) default NULL COMMENT 'ФИО',
  `phone` varchar(32) default NULL COMMENT 'Номер телефона',
  `birthday` date default NULL COMMENT 'Дата рождения',
  `sex` int(11) default '1' COMMENT 'Пол',
  `style` varchar(255) default NULL COMMENT 'Стиль одежды',
  `did` int(11) default NULL COMMENT 'Идентификатор размера одежды',
  `fid` int(11) default NULL COMMENT 'Идентификатор размера обуви',
  `cid` int(11) default NULL COMMENT 'Идентификатор бонусной карты',
  PRIMARY KEY  (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица клиентов';

-- Дамп данных таблицы legacy.lgc_clients: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_clients` DISABLE KEYS */;
INSERT INTO `lgc_clients` (`uid`, `cdate`, `fio`, `phone`, `birthday`, `sex`, `style`, `did`, `fid`, `cid`) VALUES
	(3, '2019-04-30 11:06:52', 'Михалев Максим', '79039589783', '0000-00-00', 1, 'Свой стиль', 3, 13, 1),
	(4, '2019-04-30 11:25:04', 'Михалев Максим', '79039589783', '0000-00-00', 1, 'fhhhhhhhhf', 1, 1, 2),
	(5, '2019-04-30 11:26:19', 'Михалев Максим', '79039589783', '0000-00-00', 1, 'fhhhhhhhhf', 1, 1, 2),
	(6, '2019-04-30 11:37:10', 'Михалев Максим', '79039589783', '1979-01-10', 1, 'fhhhhhhhhf', 1, 1, 2),
	(7, '2019-04-30 11:40:53', 'Михалев Максим', '33333333333', '2001-10-02', 1, 'gfgdfgd', 1, 1, 3),
	(8, '2019-04-30 11:49:52', 'Михалев Максим', '66666666666', NULL, 1, 'еукеупкепук', 1, 1, 4),
	(9, '2019-04-30 11:59:35', 'Михалев Максим', '66666666666', '2014-05-10', 1, '46545645', 1, 1, 5),
	(10, '2019-04-30 12:04:57', '', '', '0000-00-00', 1, '', 1, 1, 6),
	(11, '2019-04-30 12:12:38', 'Михалев Максим', '79039589783', '1979-01-10', 1, 'eeeeee', 3, 13, 7);
/*!40000 ALTER TABLE `lgc_clients` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_dsize
CREATE TABLE IF NOT EXISTS `lgc_dsize` (
  `did` int(10) unsigned NOT NULL auto_increment COMMENT 'Идентификатор значения',
  `value` varchar(127) default NULL COMMENT 'Текстовое описание',
  PRIMARY KEY  (`did`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица словаря размера одежды';

-- Дамп данных таблицы legacy.lgc_dsize: ~11 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_dsize` DISABLE KEYS */;
INSERT INTO `lgc_dsize` (`did`, `value`) VALUES
	(1, '40'),
	(2, '42'),
	(3, '44'),
	(4, '46'),
	(5, '48'),
	(6, '50'),
	(7, '52'),
	(8, '54'),
	(9, '56'),
	(10, '58'),
	(11, '60');
/*!40000 ALTER TABLE `lgc_dsize` ENABLE KEYS */;

-- Дамп структуры для таблица legacy.lgc_fsize
CREATE TABLE IF NOT EXISTS `lgc_fsize` (
  `fid` int(11) NOT NULL auto_increment COMMENT 'Идентификатор значения',
  `value` varchar(127) default NULL COMMENT 'Текстовое описание',
  PRIMARY KEY  (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица словаря размера обуви';

-- Дамп данных таблицы legacy.lgc_fsize: ~13 rows (приблизительно)
/*!40000 ALTER TABLE `lgc_fsize` DISABLE KEYS */;
INSERT INTO `lgc_fsize` (`fid`, `value`) VALUES
	(1, '35'),
	(2, '35.5'),
	(3, '35-36'),
	(4, '36'),
	(5, '36.5'),
	(6, '37'),
	(7, '37.5'),
	(8, '38'),
	(9, '39'),
	(10, '39.5'),
	(11, '40'),
	(12, '41'),
	(13, '41.5');
/*!40000 ALTER TABLE `lgc_fsize` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
