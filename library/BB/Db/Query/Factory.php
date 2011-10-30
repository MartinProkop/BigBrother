<?php
class BB_Db_Query_Factory {
	const CLASS_PREFIX = "BB_Db_Query_Operator_";
	
	public static function factory($input) {
		// kontrola, jeslti je vstupni parametr pole
		if (is_array($input)) {
			// vstupni parametr je pole, vyhodnoti se typ objektu
			if (isset($input[BB_Db_Query_Operator_Abstract::OPERATOR_KEY])) {
				// jedna se o operator
				$className = self::CLASS_PREFIX . $input[BB_Db_Query_Operator_Abstract::OPERATOR_KEY];
				
				return new $className($input);
			} elseif (isset($input[BB_Db_Query_Expresion::EXPR_KEY])) {
				// jedna se o vyraz
				return new BB_Db_Query_Expresion($input);
			} else {
				// jedna se o sekvencni kontejner
				return new BB_Db_Query_Sequence($input);
			}
		} else {
			// vstupni parametr neni pole, vyhodnoti se jestli se jedna o primitivu nebo o referenci
			if (preg_match(BB_Db_Query_Reference::REG_EXP, $input)) {
				// objekt je reference
				return new BB_Db_Query_Reference($input);
			} else {
				// objekt je primitiva
				return new BB_Db_Query_Primitive($input);
			}
		}
	}
}
