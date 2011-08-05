<?php
/**
 * zapsani testovaciho uzivatele
 */
class Migration_2011_07_29_17_23_43 extends MpmMigration
{

	public function up(PDO &$pdo)
	{
		$pdo->exec("INSERT INTO system_users (`name`, `password`, `salt`, `admin`) VALUES ('root', '1', SHA1('prdel'), 1)");
		$pdo->exec("UPDATE system_users SET `password` = SHA1(CONCAT('11111', `SALT`)) WHERE (`name` like 'root')");
	}

	public function down(PDO &$pdo)
	{
		$pdo->exec("DELETE FROM system_users WHERE (`name` like 'root')");
	}

}

?>