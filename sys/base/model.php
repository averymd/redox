<?php

/*	CLASS Model
 *
 *	Class used as the database interaction layer and the place for most business logic in the app.
 *
 *	@author Chris 
 */
class Model {
	var $state; // Reference to statemachine.
	var $validator; // Reference to the form validator.
	var $db; // Reference to the database object.
	var $access; // Reference to the access object.
	var $table;
	
	/*	CONSTRUCTOR
	 *
	 *	@author Chris 
	 */
	function Model($name = '') {		
		$this->state =& redox::getState();
		$this->validator =& redox::getValidator();
		$this->db =& redox::getDb();
		$this->access =& redox::getAccess();
		$this->table = $name;
	}
	
	function load($model, $name = false) {
		if (!$name) { $name = $model; }
		$model .= '_model';

		if (file_exists(EXPATH.'/app/models/'.$model.'.php')) {
			include_once(EXPATH.'/app/models/'.$model.'.php');
			$this->$name = new $model();
		} else {
			$this->$name = new Model($name);
		}
	}
	
	function get($ID) {
		if (isset($this->table)) {
			$sql = $this->db->schema->sqlGet($this->table, $ID);
			$query = $this->db->query($sql);
			if($query->numrows() == 1) {
				return $query->row(0);
			} else {
				return $query->result();
			}
		}
	}
	
	function getAll($where = '') {
		if (isset($this->table)) {
			$sql = $this->db->schema->sqlGetAll($this->table, $where);
			$query = $this->db->query($sql);
			return $query->result();
		}
	}
	
	function update($ID) {
		if (isset($this->table)) {	
			$sql = $this->db->schema->sqlUpdate($this->table, $ID);
			$query = $this->db->query($sql);
			return $query->result();
		}
	}
	
	function delete($ID) {
		if (isset($this->table)) {
			$sql = $this->db->schema->sqlDelete($this->table, $ID);
			$query = $this->db->query($sql);
			return $query->result();	
		}
	}
	
	function add() {
		if (isset($this->table)) {
			$sql = $this->db->schema->sqlAdd($this->table);
			$query = $this->db->query($sql);
			return $query->insertID();
		}
	}

}

?>