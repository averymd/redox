<?php

class AccessControl {
	
	var $state;
	
	function AccessControl() {
		
		$this->state =& redox::getState();
		$this->privs = ifsetor( $this->state->getVar('privs'), false);
		$this->user_ID = ifsetor($this->state->getVar('user_ID'), null);
		$this->roles = ifsetor( $this->state->getVar('roles'), false);
	}
	
	function redirect($func, $location) {
		if(!is_callable(array(&$this,$func)))
	    {
		  	//TODO :: do some sort of error thing here
	    }
		else {
	    	if(call_user_func(array(&$this,$func))) {
				redirect($location);
			}
		}
	}
	
	function init() {
		$db = redox::getDb();
		
		$myprivs = array();
		$ID = $this->state->getVar('user_ID');
		$query = $db->query("
			SELECT
				priv.value
			FROM
				user_role
				INNER JOIN role_priv ON role_priv.role_ID = user_role.role_ID
				INNER JOIN priv ON role_priv.priv_ID = priv.ID
			WHERE
				user_role.user_ID = $ID
			GROUP BY
				priv.ID
		");
		foreach($query->result() as $p) {
			$myprivs[] = $p->value;
		}
		$this->state->setVar('privs', $myprivs);
		$this->privs =& $myprivs;
		
		$myroles = array();
		$query = $db->query("
			SELECT
				role.value
			FROM
				user_role
				INNER JOIN role ON role.ID = user_role.role_ID
			WHERE
				user_role.user_ID = $ID
			GROUP BY
				role.ID
		");
		foreach($query->result() as $p) {
			$myroles[] = $p->value;
		}
		$this->state->setVar('roles', $myroles);
		$this->roles =& $myroles;
	}
	
	function to($priv, $obj = "") {
		if($obj != '' && $this->mine($obj)) { return true; }
		if(!is_array($this->privs)) { return false; }
		return in_array($priv, $this->privs);
	}
	
	function role($role) {
		if(!is_array($this->roles)) { return false; }
		return in_array($role, $this->roles);
	}
	
	function mine($obj) {
		if(is_object($obj)) {
			return $obj->user_ID == $this->user_ID;
		} else {
			return $obj == $this->user_ID;
		}
	}
}

?>