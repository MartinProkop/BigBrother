<?php

class Migration_2011_07_28_19_24_28 extends MpmMigration
{

	public function up(PDO &$pdo)
	{
		$pdo->query("CREATE TABLE IF NOT EXISTS system_users_sessions (
sessId varchar(40) primary key,
UserId int not null,
validTo datetime not null,
createdAt timestamp default CURRENT_TIMESTAMP,
params blob,
index(UserId)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci");

		$pdo->query("ALTER TABLE system_users_sessions ADD CONSTRAINT `users-users_session01` FOREIGN KEY (UserId) REFERENCES system_users (id)");
	}

	public function down(PDO &$pdo)
	{
		$pdo->query("DROP TABLE system_users_sessions");
	}

}

?>