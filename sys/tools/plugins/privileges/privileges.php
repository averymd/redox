<?php
class privileges {

	function privileges(&$con) {
		$this->outlet =& $con;
		$this->outlet->set('roles', $this->_getRoles());
		$this->outlet->set('privs', $this->_getPrivs());
		$this->outlet->set('roleprivs', $this->_getRolePrivs());
	}

	function index() {
	
	}

	function _getprivilegeschema() {
		$schema = <<<XML
			<database>
			<table name="role">
				<field name="value" type="VARCHAR(255)" />
			</table>

			<table name="user_role">
			<has_one table="user">
				<field name="username" />
			</has_one>
		<has_one table="role">
			<field name="value" />
		</has_one>
	</table>

	<table name="priv">
		<field name="value" type="VARCHAR(255)" />
	</table>

	<table name="role_priv">
	<has_one table="role">
		<field name="value" />
	</has_one>
	<has_one table="priv">
		<field name="value" />
	</has_one>
	</table>
	</database>
XML;

		return new Schema($schema);
	}

	function addschema() {
		$schema = $this->_getprivilegeschema();
		$str = $schema->createDatabase();
		foreach ($str as $sql) {
			$this->outlet->db->query($sql);
		}

		setFlash('<p class="success">Privilege tables successfully created.</p>');
		redirect('generator/privileges/');
	}

	function addrole() {
		$role = confirm::exists($_POST['value']);

		if ($role) {
			$role = $_POST['value'];
			$schema = $this->_getprivilegeschema();
			$this->outlet->db->query($schema->sqlAdd('role'));

			setFlash('<p class="success">The new role "' . treat::xss($role) . '" was created.</p>');
		}
		redirect('generator/privileges/');
	}

	function addprivilege() {
		$priv = $this->outlet->validator->post('value');
		setFlash($this->_addPriv($priv));
		redirect('generator/privileges/');
	}

	function updateprivileges() {
		if (isset($_SERVER) && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->_deleteOldPrivs();
			$this->_updateNewPrivs();
		}
		redirect('generator/privileges/');
	}
	
	function _addPriv($priv) {
		if ($priv) {
			$this->outlet->validator->addToPost('value', $priv);
			$schema = $this->_getprivilegeschema();
			$this->outlet->db->query($schema->sqlAdd('priv'));
			return '<p class="success">The new privilege "' . treat::xss($priv) . '" was created.</p>';
		}
	}

	function _getPrivs() {
		$query = $this->outlet->db->query("SELECT ID, value FROM priv;");
		return $query->result();
	}

	function _getRoles() {
		$query = $this->outlet->db->query("SELECT ID, value FROM role;");
		return $query->result();
	}

	function _getRolePrivs() {
		$query = $this->outlet->db->query("SELECT role_ID, priv_ID FROM role_priv;");
		$results = $query->result();
		$roleprivs = array();
		foreach($results as $r) {
			$roleprivs[$r->role_ID][] = $r->priv_ID;
		}
		return $roleprivs;
	}

	function _deleteOldPrivs() {
		$query = $this->outlet->db->query("DELETE FROM role_priv;");
	}

	function _updateNewPrivs() {
		foreach($_POST as $k=>$v) {
			$role = substr($k, 0, strpos($k,'_'));
			$priv = substr($k, strlen($role)+1, strlen($k)-strlen($role)+1);
			$query = $this->outlet->db->query("INSERT INTO role_priv SET role_ID = ".$role.", priv_ID = ".$priv.", created_ts = NOW(), modified_ts = NOW();");
		}

		setFlash('<p class="success">Your privilege changes have been saved.</p>');
	}
}	
?>