<?php
class Orm_Class
{
	const TEMPLATE = 'class %NAME% { %VARIABLES% }';
	
	protected $className;
	protected $variables;
	
	public function __construct($name, $variables=array())
	{
		$this->className = $name;
		$this->variables = $variables;
	}

	public function toString()
	{
		$varString = '';
		
		foreach($this->variables as $key => $value)
		{
			$varString = "$varString $value $$key;";
		}
		
		$string = preg_replace("/%VARIABLES%/", $varString, self::TEMPLATE);
		$string = preg_replace("/%NAME%/", $this->className, $string);
		
		return $string;
	}

	public static function loadClassForTable($table, $pdo)
	{
		$query = $pdo->prepare("desc $table");
		$query->execute(array());
		$fields = $query->fetchAll();

		if(count($fields) == 0)
		{
			throw new Exception("Orm_Class: Could not load class.");
		}

		$arr = array();

		foreach($fields as $field)
		{
			$arr[$field['Field']] = 'public';
		}

		$class = new Orm_Class($table, $arr);
		eval($class->toString());
	}
}
?>
