<?php

class Migration_2011_08_05_19_02_05 extends MpmMigration
{

	public function up(PDO &$pdo)
	{
				$pdo->exec("INSERT INTO system_users (`name`, `password`, `salt`, `admin`) VALUES ('pica', 'kozena', SHA1('prdel'), 1)");
	}

	public function down(PDO &$pdo)
	{
		$pdo->exec("DELETE FROM system_users WHERE (`name` like 'pica')");
	}

}

?>
