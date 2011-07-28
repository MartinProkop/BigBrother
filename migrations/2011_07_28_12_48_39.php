<?php

class Migration_2011_07_28_12_48_39 extends MpmMigration
{

	public function up(PDO &$pdo)
	{
		// vytvoreni tabulky pro ACL
		$pdo->query("CREATE TABLE IF NOT EXISTS system_users_acl (
id int primary key auto_increment,
UserId int not null,
ObjectType varchar(255),
method varchar(255),
unique(UserId, ObjectType))");

		// pridani ciziho klice pro ACL
		$pdo->query("ALTER TABLE system_users_acl ADD CONSTRAINT user01 FOREIGN KEY (UserId) REFERENCES system_users (id)");
		
		// pridani sloupce admin do tablky uzivatelu
		$pdo->query("ALTER TABLE system_users ADD COLUMN admin bool default 0");
	}

	public function down(PDO &$pdo)
	{
		// odstraneni tabulky ACL
		$pdo->query("DROP TABLE system_users_acl");
		
		// odebrani sloupce admin
		$pdo->query("ALTER TABLE system_users DROP COLUMN admin");
	}

}

?>