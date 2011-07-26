<?php
/**
 * Database class
 * 
 * This class is used to interact with a mysql database and is available only to the model class.
 *
 * @author Chris 
 **/
class Database
{
	var $schema;
	var $dbparams; //params to be loaded in from the db.php config file
	var $link; //the mysql connection object
	var $bm; // the global benchmarker for use in capturing sql queries and execution times.
	//query building pieces
	var $method;
	var $from;
	var $where;
	var $set;
	var $group;
	var $order;
	var $limit;
	
	/**
	 * Default constructor
	 *
	 * Sets up all instance vars.
	 *
	 * @param $path is used for external scripts where you need the absolute path to the db config file.
	 * @author Chris 
	 **/
	function Database($path = '')
	{
		include(EXPATH.'/config/db.php'); //grab the db configuration variables
		$schema = file_get_contents(EXPATH.'/config/schema.xml'); //grab the db schema
		
		$this->dbparams = $dbconfig[$group]; //set the config vars to the instance var
		$this->schema = new schema($schema);
		$this->bm =& redox::getBm(); //grab a reference the the global benchmarker
		$this->connect();
	}
	
	/**
	 * Begin a select query
	 *
	 * @return void
	 * @author chris
	 **/
	function select($select)
	{
		$this->method = 'SELECT '.$select;
	}
	
	/**
	 * Begin an update query
	 *
	 * @return void
	 * @author chris
	 **/
	function update($update)
	{
		$this->method = 'UPDATE '.$update;
	}
	
	/**
	 * Begin a delete query
	 *
	 * @return void
	 * @author chris
	 **/
	function delete($del)
	{
		$this->method = 'DELETE FROM '.$del;
	}
	
	/**
	 * Add from clause to query
	 *
	 * @return void
	 * @author chris
	 **/
	function from($from)
	{
		if($this->from == '') {
			$this->from = ' FROM '.$from;
		} else {
			$this->from .= ', '.$from;
		}
	}
	
	/**
	 * Add leftjoin clause to query
	 *
	 * @return void
	 * @author chris
	 **/
	function leftjoin($join)
	{
		if($this->from != '') {
			$this->from .= ' LEFT JOIN '.$join;
		}
	}
	
	/**
	 * Add set clause to query
	 *
	 * @return void
	 * @author chris
	 **/
	function set($set)
	{
		if($this->set == '') {
			$this->set = ' SET '.$set;
		}else {
			$this->set .= ', '.$set;
		}
	}
	
	/**
	 * Add where clause to query
	 *
	 * @return void
	 * @author chris
	 **/
	function where($where, $or = false)
	{
		if($this->where == ''){
			$this->where = ' WHERE '.$where;
		}else if($or) {
			$this->where .= ' OR '.$where;
		}else {
			$this->where .= ' AND '.$where;
		}
	}
	
	/**
	 * Add order by clause to query
	 *
	 * @return void
	 * @author chris
	 **/
	function order($order)
	{
		if($this->order == '') {
			$this->order = ' ORDER BY '.$order;
		}else {
			$this->order .= ', '.$order;
		}
	}
	
	/**
	 * Add group by clause to query
	 *
	 * @return void
	 * @author chris
	 **/
	function group($group)
	{
		if($this->group == '') {
			$this->group = ' GROUP BY '.$group;
		}else {
			$this->group .= ', '.$group;
		}
	}
	
	/**
	 * Add limit clause to query
	 *
	 * @return void
	 * @author chris
	 **/
	function limit($lim)
	{
		$this->limit = $lim;
	}
	
	/**
	 * query($sql)
	 *
	 * executes a the sql query held within $sql and returns the result as a ResultObject.
	 * Also benchmarks the sql query.
	 *
	 * @param $sql = the query to be executed
	 * @return ResultObject containing the results of the sql query
	 * @author Chris 
	 **/
	function query($sql = '')
	{		
		//if a connection is not established, grab one
		if(!$this->link)
			$this->connect();
			
		if($sql == '') {
			//build query from parts
			$sql = $this->method.$this->from.$this->set.$this->where.$this->group.$this->order.$this->limit;
			$this->method = '';
			$this->from = '';
			$this->set = '';
			$this->where = '';
			$this->group = '';
			$this->order = '';
			$this->limit = '';
		}
			
		$querynum = $this->bm->logQuery($sql); //benchmark the query
		$result = mysql_query($sql); //execute the sql

		$return = new ResultObject($result); //get new ResultObject

		if (!$result) {
			trigger_error('mysql: ['.mysql_errno().'] '.mysql_error().(PROFILER ? ' <a href="#queries'.($querynum + 1).'">(see profiler query '.($querynum + 1).')</a>' : ''), E_USER_WARNING);
			$this->bm->failedQuery('ERROR '.mysql_errno().': '.mysql_error());
		}
		$this->bm->endQuery($sql); //end benchmark
		
		return $return;
	}
	
	/**
	 * connect()
	 *
	 * Creates a mysql connection using the settings from the db config file
	 *
	 * @author Chris 
	 **/
	function connect()
	{
		$link = mysql_connect($this->dbparams['hostname'],$this->dbparams['username'],$this->dbparams['password']);
		mysql_select_db($this->dbparams['database'],$link);
		$this->link = $link;
	}
}


/**
 * ResultObject class
 *
 * Object that is returned after any query. The ResultObject holds an array of objects
 * representing the results returned by the mysql query.
 * 
 * @author Chris 
 **/
class ResultObject
{
	var $result = array(); //holds the results array
	var $failed = false;
	
	/**
	 * Default constructor
	 *
	 * @param $result = the mysql_result to represent
	 * @author Chris 
	 **/
	function ResultObject($result)
	{
		$return = array();
		if($result && $result != 1 && mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_object($result))
			{
				$return[] = $row;
			}
		} else if(!$result) {
			$this->failed = true;
		}
		$this->result =& $return;
	}
	
	/**
	 * row($num)
	 *
	 * Returns the object representing row $num
	 *
	 * @param $num = the number of the row to be returned
	 * @return the object representing the row $num
	 * @author Chris 
	 **/
	function row($num)
	{
		if(isset($this->result[$num])) {
			return $this->result[$num];
		}
		return false;
	}
	
	/**
	 * result()
	 *
	 * Return the entire results array
	 * 
	 * @return results array of row objects
	 * @author Chris 
	 **/
	function result()
	{
		return $this->result;
	}
	
	function numrows() {
		return count($this->result);
	}
	
	function insertID() {
		return mysql_insert_id();
	}
}

class null {}

class schema {
	
	var $xml;
	var $tables;
	var $validator;

	function schema($xml) {
		$this->validator =& redox::getValidator();
		$this->tables = array();
		if (floor(phpversion()) > 4)
		{
			$this->xml = new SimpleXMLElement($xml);
			$this->buildTables();
		}
	}
	
	function buildTables() {
		foreach($this->xml->table as $t) {
			$newTable = new table((string)$t['name']); 

			$newTable->field(array('name'=>'ID','type'=>'INT(10) NOT NULL AUTO_INCREMENT, PRIMARY KEY(ID)'));
			
			foreach($t->field as $f) {
				$newTable->field($f);
			}
			
			foreach($t->has_one as $f) {
				$newTable->has_one($f, (string)$t['name']);
			}
			
			foreach($t->has_many as $f) {
				$newTable->has_many($f, (string)$t['name']);
			}
			
			$newTable->field(array('name'=>'created_ts', 'type'=>'DATETIME NOT NULL'));
			$newTable->field(array('name'=>'modified_ts', 'type'=>'TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP'));
			
			$this->tables[(string)$t['name']] = $newTable;
		}
	}
	
	function createDatabase() {
		$ret = array();
		foreach($this->tables as $t) {
			$sql = "CREATE TABLE `$t->name` (";
			foreach($t->fields as $field) {
				$sql .= $field->forSQL('create', $this->validator);
			} 
			$sql = substr( $sql, 0, strlen($sql)-2 );
			$sql .= "); ";
			$ret[] = "DROP TABLE IF EXISTS `$t->name`;";
			$ret[] = $sql;
		}
		return $ret;
	}
	
	function formFields($name) {
		if(isset($this->tables[$name])) {
			$table = $this->tables[$name];
			$str = '';
			foreach($table->fields as $field) {
				if($field->editable) {
					$e = $field->name;
				$str .= "<div class='optional'><label for=\"$e\">$e<?php echo (\$this->validator->error(\"$e\") ? \" : \".\$this->validator->error(\"$e\") : \"\"); ?></label>
	<?php echo form::text(\"$e\"); ?></div>
";
				}
			}
			return $str;
		} 
		return false;
	}
	
	function sqlGet($name, $ID) {
		return $this->sqlGetAll($name, "$name.ID = $ID");
	}
	
	function sqlGetAll($name, $where) {
		$table =& ifsetor($this->tables[$name], null);
		if($table) {			
			$sql = "SELECT SQL_CALC_FOUND_ROWS ";
			$null = new null();
			$pieces = $table->buildSQL('get', $null);
			$sql .= $pieces['select'];
			$sql .= " FROM ";
			$sql .= $pieces['from'];
			if($where) {
				$sql .= " WHERE $where;";
			}
			return $sql;
		}
		return "";
	}
	
	function sqlUpdate($name, $ID) {
		$table =& ifsetor($this->tables[$name], null);
		if($table) {
			$sql = "UPDATE `$table->name` SET ";
			$pieces = $table->buildSQL('update', $this->validator);
			$sql .= $pieces['select'];
			$sql .= " WHERE ID = $ID;";
			return $sql;
		}
		return "";
	}
	
	function sqlDelete($name, $ID) {
		$table =& ifsetor($this->tables[$name], null);
		if($table) {
			$sql = "DELETE FROM `$table->name` WHERE ID = $ID;";
			return $sql;
		}
		return "";
	}
	
	function sqlAdd($name) {
		$table =& ifsetor($this->tables[$name], null);
		if($table != null) {
			$sql = "INSERT INTO `$table->name` SET ";
			$pieces = $table->buildSQL('add', $this->validator);
			$sql .= $pieces['select'];
			$sql .= ", created_ts = NOW(), modified_ts = NOW()";
			return $sql;
		}
		return "";
	}
	
}

class table {
	
	var $name;
	var $fields = array();
	
	function table($name) {
		$this->name = $name;
	}
	
	function field($f) {
		$f = $this->cleanXMLObject($f);
		$name = $f['name'];
		
		if(!isset($f['table'])) {
			$f['table'] = $this->name;
		}
		
		if(isset($f['phpName'])) {
			$name = $f['phpName'];
		}
		
		if(!isset($this->fields[$name])) {
			$this->fields[$name] = new field($f);
		} else {
			$this->fields[$name]->append($f);
		}
	}
	
	function has_one($f, $parent) {
		$new = array();
		$key = array();
		
		$key = $this->cleanXMLObject($f);
		unset($key['field']);
		$key['parentTable'] = ifsetor($key['parentTable'], $this->name);		
		if(!isset($key['name'])) {
			$keyfield = $key['table'];
			$key['name'] = $key['table'].'_ID';
		} else {
			$keyfield = $key['name'];
			$key['name'] = $key['name'].'_ID';
		}
		$key['relation'] = 'fkey';
		$key['type'] = 'INT(10) NOT NULL';
		
		$this->field($key);
		
		foreach($f->field as $field) {
			$new = $this->cleanXMLObject($field);
			$new['table'] = $key['table'];
			$new['key'] = $key['name'];
			$new['relation'] = 'has_one';
			$new['newName'] = $keyfield;
			$new['phpName'] = $keyfield.'_'.$field['name'];
			$new['parentTable'] = $parent;
			$this->field($new);
		}
		
		if(isset($f->has_one)) {
			foreach($f->has_one as $field) {
				$field->addAttribute('parentTable', $key['table']);
				$this->has_one($field, $key['table']);
			}
		}
	}
	
	function has_many($f, $parent) {
		$new = array();
		$key = array();
		
		$key = $this->cleanXMLObject($f);
		
		foreach($f->field as $field) {
			$new = $this->cleanXMLObject($field);
			$new['table'] = $key['table'];
			$new['relation'] = 'has_many';
			$new['phpName'] = $key['table'].'_'.$field['name'];
			$new['parentTable'] = $parent;
			
			if(isset($f['junction'])) {
				$new['junction'] = $key['junction'];
			}
			
			$this->field($new);
		}
	}
	
	function cleanXMLObject($f) {
		$return = array();
		if(is_object($f)) {
			$attrs = $f->attributes();
			foreach($attrs as $key=>$value) {
				$return[$key] = (string)$value;
			}
			return $return;
		} else {
			return $f;
		}
	}
	
	function buildSQL($type, &$validator) {
		$return = array();
		$return['select'] = '';
		$return['from'] = "`$this->name`";
		
		foreach($this->fields as $f) {
			if(!$f->relation || $f->relation == 'fkey') {
				$return['select'] .= $f->forSQL($type, $validator);
			} else if ($f->relation == 'has_one' || $f->relation == 'has_many') {
				$sql = $f->forSQL($type, $validator);
				if(is_array($sql)) {
					$return['select'] .= $sql[0];
					if($return['from'] == '' || stristr($return['from'], $sql[1]) === false) {
						$return['from'] .= $sql[1];
					}
				}
			}	
		}
		
		$return['select'] = substr( $return['select'], 0, strlen($return['select']) -2);
		
		return $return;
	}
	
}

class field {
	var $name;
	var $relation;
	var $editable = false;
	var $key;
	var $newName;
	
	function field($array) {
		foreach($array as $key=>$value) {
			$this->$key = (string)$value;
		}
	}
	
	function name() {
		return ifsetor($this->displayName, $this->name);
	}
	
	function append($array) {
		$this->field($array);
	}
	
	function forSQL($type, &$validator) {
		switch($type) {
			case 'add':
			case 'update':
				if(!$this->key) {
					$postVal = $validator->forSQL($this->name);
				} else {
					$postVal = $validator->forSQL($this->key);
				}
				
				if($postVal === false) { return ''; }
				
				if(!$this->relation) {
					return "$this->table.$this->name = '$postVal', ";
				} else if($this->relation == 'fkey') {					
					if($this->key) {
						return "$this->parentTable.$this->key = '$postVal', ";
					} else {
						return "$this->parentTable.$this->name = '$postVal', ";
					}
				}
			break;
			case 'get':
			case 'getAll':
				if(!$this->relation) {
					return "$this->table.$this->name, ";
				} else if($this->relation == 'fkey') {
					return "$this->parentTable.$this->name, ";
				} else if ($this->relation == 'has_one') {
					return array("$this->newName.$this->name AS $this->phpName, "," LEFT JOIN `$this->table` AS $this->newName ON $this->parentTable.$this->key = $this->newName.ID");
				} else if ($this->relation == 'has_many') {
					if(!isset($this->junction)) {
						return array("$this->table.$this->name AS $this->phpName, "," LEFT JOIN `$this->table` ON $this->table.$this->parentTable"."_ID = $this->parentTable.ID");
					} else {
						return array("$this->table.$this->name AS $this->phpName, ", " LEFT JOIN `$this->junction` ON $this->junction.$this->parentTable"."_ID = $this->parentTable.ID LEFT JOIN $this->table ON $this->junction.$this->table"."_ID = $this->junction.$this->table"."_ID");
					}
				}
			break;	
			case 'create':
				if(!$this->relation || $this->relation == 'fkey') {
					return "`$this->name` $this->type, ";
				}
			break;
		}
		return '';
	}
}
?>