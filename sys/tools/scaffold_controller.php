<?php

class scaffold_controller extends Controller {

	var $db;

	function scaffold_controller() {
		parent::Controller();
		$this->db =& redox::getDb();
		$this->set('tables', $this->_getTables());
	}
	
	function index() {
		
	}
	
	function tables() {
		$table = $this->uri->segment(1);
		$this->set('table', $table);
		$this->set('fields', $this->_getFields($table));
		$this->set('records' ,$this->_getRecords($table));
	}
	
	function edit() {
		//cool
		$table = $this->uri->segment(1);
		$ID = $this->uri->segment(2);
		$this->set('ID', $ID);
		$this->set('table', $table);
		$this->set('fields', $this->_getFields($table));
		if(!$this->validator->posted) {
			$this->validator->preLoadFormVals($this->_getSingle($table, $ID));
		} else {
			$this->_update($table, $ID);
			redirect("scaffold/tables/$table");
		}
	}
	
	function add() {
		$table = $this->uri->segment(1);
		$this->set('table', $table);
		$this->set('fields', $this->_getFields($table));
		if($this->validator->posted) {
			$this->_add($table);
			redirect("scaffold/tables/$table");
		}
	}
	
	function delete() {
		$table = $this->uri->segment(1);
		$ID = $this->uri->segment(2);
		$this->_delete($table, $ID);
		redirect("scaffold/tables/$table");
	}
	
	function sql() {
		if($this->validator->posted) {
			$query = $this->db->query($this->validator->post('query'));
			$this->set('result', $query->result());
		}
	}
	
		function _getTables() {
			$query = $this->db->query("SHOW TABLE STATUS");
			return $query->result();
		}
		
		function _getFields($table) {
			$query = $this->db->query("SHOW FIELDS FROM $table");
			return $query->result();
		}
		
		function _getRecords($table) {
			$fields = $this->_getFields($table);
			$str = '';
			foreach($fields as $f){
				$str .= $f->Field.', ';
			}
			$str = substr($str, 0, strlen($str)-2);
			$query = $this->db->query("SELECT $str FROM $table");
			return $query->result();
		}
		
		function _getSingle($table, $ID) {
			$fields = $this->_getFields($table);
			$str = '';
			foreach($fields as $f){
				$str .= $f->Field.', ';
			}
			$str = substr($str, 0, strlen($str)-2);
			$query = $this->db->query("SELECT $str FROM $table WHERE ID = $ID");
			return $query->row(0);
		}
		
		function _add($table) {
			$fields = $this->_getFields($table);
			$str = '';
			foreach($fields as $f){
				$postVal = $this->validator->forSQL($f->Field);
				if($postVal) {
					$str .= $f->Field." = '$postVal', ";
				}
			}
			$str = substr($str, 0, strlen($str)-2);
			$query = $this->db->query("INSERT INTO $table SET $str");
			return $query->insertID();
		}
		
		function _update($table, $ID) {
			$fields = $this->_getFields($table);
			$str = '';
			foreach($fields as $f){
				$postVal = $this->validator->forSQL($f->Field);
				if($postVal) {
					$str .= $f->Field." = '$postVal', ";
				}
			}
			$str = substr($str, 0, strlen($str)-2);
			$query = $this->db->query("UPDATE $table SET $str WHERE ID = $ID");
		}
		
		function _delete($table, $ID) {
			$this->db->query("DELETE FROM $table WHERE ID = $ID");
		}

}
?>