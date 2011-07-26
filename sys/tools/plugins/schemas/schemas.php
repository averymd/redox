<?php

class schemas {
	
	function schemas(&$con) {
		$this->outlet = $con;
	}
	
	function index() {
		$this->outlet->set('tables', $this->outlet->getTables());
	}

	function addschema() {
		$str = $this->outlet->db->schema->createDatabase();
		$drop = $this->outlet->validator->post('droptables');
		foreach ($str as $sql) {
			if((!$drop && stripos($sql, 'DROP') === false) || $drop) {
				$this->outlet->db->query($sql);
			}
		}
		setFlash('<p class="success">Database tables created successfully.</p>');
		redirect('generator/schemas/');
	}

	function formfields() {
		$table = confirm::exists($_POST['table']);
		$table = confirm::model_name($_POST['table']);

		if ($table) {
			$table = $_POST['table'];
			$content = $this->outlet->db->schema->formFields($table);
			if ($content) {
				setFlash('<p class="success">Form fields successfully created:</p><textarea rows="10" cols="50" style="width: 100%; border: none;">'.$content.'</textarea>');
			} else {
				setFlash('<p class="error">The '.$table.' table is not defined.');
			}
		} else {
			if (isset($_SERVER) && $_SERVER['REQUEST_METHOD'] == 'POST') {
				setFlash('<p class="error">That is not a valid table name.</p>');
			} // Else fail silently.
		}
		redirect('generator/schemas/');
	}

	function staticmodel() {
		$table = confirm::exists($_POST['table']);
		$table = confirm::model_name($_POST['table']);

		if ($table) {
			$table = $_POST['table'];
			$add = $this->outlet->db->schema->sqlAdd($table);
			$update = $this->outlet->db->schema->sqlUpdate($table, '$ID');
			$delete = $this->outlet->db->schema->sqlDelete($table, '$ID');
			$get = $this->outlet->db->schema->sqlGet($table, '$ID');
			$getall = $this->outlet->db->schema->sqlGetAll($table, '$where');
			$tableFields = $this->outlet->db->schema->tables[$table]->fields;

			ob_start();
			include(EXPATH.'/sys/tools/_templates/staticmodel.php');
			$content = ob_get_contents();
			ob_clean();

			setFlash('<p class="success">Static model functions successfully created:</p>'.$content);
		} else {
			if (isset($_SERVER) && $_SERVER['REQUEST_METHOD'] == 'POST') {
				setFlash('<p class="error">That is not a valid table name.</p>');
			} // Else fail silently.
		}
		redirect('generator/schemas/');
	}
	
	function generateschema() {
	}
	
}
?>