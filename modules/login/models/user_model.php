<?php
class user_model extends Model
{
	function user_model() {
		parent::Model("user");
		
	}
	
	function check()
	{
		$password = md5($this->validator->post('password'));
		$username = $this->validator->post('username');
		$query = $this->db->query("
			SELECT
				ID,
				username
			FROM
				user
			WHERE
				password = '$password'
				AND
				username = '$username'
				AND
				ID != 0
		");
		if($query->numrows() > 0){
			return $query->row(0);
		}
	
		return false;
	}
	
	
}

?>