<?php

class Migration_2011_07_28_10_19_14 extends MpmMigration
{

	public function up(PDO &$pdo)
	{
		$pdo->query("CREATE TABLE IF NOT EXISTS `data_user_table1-1` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `name` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='data -> user1 -> tabulka1' AUTO_INCREMENT=1 ;");
		
		$pdo->query("CREATE TABLE IF NOT EXISTS `data_user_table1-2` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `name` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='data -> user1 -> tabulka1' AUTO_INCREMENT=1 ;");
		
		$pdo->query("CREATE TABLE IF NOT EXISTS `data_user_table1-3` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `IdPrvniTabulky` int(50) NOT NULL,
  `IdDruheTabulky` int(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IdDruheTabulky` (`IdDruheTabulky`),
  KEY `IdPrvniTabulky` (`IdPrvniTabulky`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='data -> user1 -> tabulka1' AUTO_INCREMENT=1 ;");
		
		$pdo->query("CREATE TABLE IF NOT EXISTS `data_user_table2-1` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='data -> user2 -> tabulka1' AUTO_INCREMENT=1 ;");
		
		$pdo->query("CREATE TABLE IF NOT EXISTS `system_altername` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `PrivateName` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `PublicName` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `PrivateName` (`PrivateName`,`PublicName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Systémová tabulka k přepisu jmen tabulek, názvů a tak' AUTO_INCREMENT=3 ;");
		
		$pdo->query("INSERT INTO `system_altername` (`id`, `PrivateName`, `PublicName`) VALUES
(2, 'Píča', 'Sekretářka klienta'),
(1, 'Zmrd', 'Příjemný zástupce klienta');");

		$pdo->query("CREATE TABLE IF NOT EXISTS `system_extern_log` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `UserId` int(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `objectType` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `objectId` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `method` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `getParam` longtext COLLATE utf8_czech_ci NOT NULL,
  `postParam` longtext COLLATE utf8_czech_ci NOT NULL,
  `action` longtext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `UserId` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;");

		$pdo->query("CREATE TABLE IF NOT EXISTS `system_intern_log` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UserId` int(50) NOT NULL,
  `TableName` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `ObjectId` int(50) NOT NULL,
  `actionType` enum('post','put','get','remove') COLLATE utf8_czech_ci NOT NULL,
  `action` longtext COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `UserId` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;");
		
		$pdo->query("CREATE TABLE IF NOT EXISTS `system_users` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `salt` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='tabulka uživatelů' AUTO_INCREMENT=4 ;");
		
		$pdo->query("INSERT INTO `system_users` (`id`, `name`, `password`, `salt`, `created`) VALUES
(1, 'user1', 'heslo1', '', '0000-00-00 00:00:00'),
(2, 'user2', 'heslo2', '', '0000-00-00 00:00:00');");
		
		$pdo->query("ALTER TABLE `data_user_table1-3`
  ADD CONSTRAINT `data_user_table1@002d3_ibfk_1` FOREIGN KEY (`IdPrvniTabulky`) REFERENCES `data_user_table1-1` (`id`),
  ADD CONSTRAINT `data_user_table1@002d3_ibfk_2` FOREIGN KEY (`IdDruheTabulky`) REFERENCES `data_user_table1-2` (`id`);");
		
		$pdo->query("ALTER TABLE `system_extern_log`
  ADD CONSTRAINT `system_extern_log_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `system_users` (`id`);");
		
		$pdo->query("ALTER TABLE `system_intern_log`
  ADD CONSTRAINT `system_intern_log_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `system_users` (`id`);");
	}

	public function down(PDO &$pdo)
	{
		$pdo->query("DROP TABLE `data_user_table1-3`");
		
		$pdo->query("DROP TABLE `data_user_table1-1`");
		
		$pdo->query("DROP TABLE `data_user_table1-2`");
		
		$pdo->query("DROP TABLE `data_user_table2-1`");
		
		$pdo->query("DROP TABLE system_altername");
		
		$pdo->query("DROP TABLE system_extern_log");
		
		$pdo->query("DROP TABLE system_intern_log");
		
		$pdo->query("DROP TABLE system_users");
	}

}

?>
