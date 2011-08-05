<?php
class BB_Db_Query_Factory {
	const ESCAPE = "\\";
	
	const COLUMN = "[";
	
	const EXPRESION = "expresion";
	
	const OPERATOR = "operator";
	
	const SYMBOL = "`";
	
	const QUOTE = "'";
	
	const CLASS_PREFIX = "BB_Db_Query_Operator_";
	
	public static function factory($input) {
		if (!is_array($input)) return self::quoteScalar($input);
		
		// vyhodnoceni typu
		$isOperator = isset($input[self::OPERATOR]);
		$isExprssion = isset($input[self::EXPRESION]);
		
		if ($isExprssion == $isOperator) {
			// je pole je definovano jako vyraz i operator nebo neni definovan ani jeden priznak. Toto je nepripustne
			throw new BB_Db_Query_Exception("Unsolvable definiton");
		}
		
		// uprava parametru
		$params = array();
		
		foreach ($input as $key => $value) {
			if (is_numeric($key)) {
				$params[] = $value;
			}
		}
		
		// vyhodnoceni, jestli se jedna o operator nebo o vyraz
		if ($isExprssion) return new BB_Db_Query_Expresion($input[self::EXPRESION], $params);
		
		// pole je operator, vyhodnoti se, jestli ma dany operator zvlastni tridu
		$operator = $input[self::OPERATOR];
		
		$className = ucwords(strtolower($operator));
		$className = str_replace(" ", "", $className);
		
		if (is_file(__DIR__ . "/Operator/" . $className . ".php")) {
			$className = self::CLASS_PREFIX . $className;
			
			return new $className($input);
		}
		
		return new BB_Db_Query_Operator($input);
	}
	
	public static function quoteScalar($value) {
		if (is_numeric($value)) return $value;
		
		if ($value[0] == "[") {
			$value[0] = self::SYMBOL;
			
			return str_replace("]", self::SYMBOL, $value);
		}
		
		if ((substr($value, 0, 2) == "\\\\") || (substr($value, 0, 2) == "\\[")) {
			$value = substr($value, 1);
		}
		
		return self::QUOTE . mysql_escape_string($value) . self::QUOTE;
	}
}
