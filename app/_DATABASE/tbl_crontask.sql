CREATE TABLE `crontask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` varchar(50) CHARACTER SET latin1 DEFAULT '',
  `action` varchar(50) CHARACTER SET latin1 DEFAULT '',
  `ipaddress` varchar(50) DEFAULT NULL,
  `timeprocessing` float DEFAULT NULL,
  `output` text CHARACTER SET latin1,
  `status` tinyint(2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task` (`task`),
  KEY `action` (`action`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;