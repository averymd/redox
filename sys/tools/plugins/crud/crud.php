<?php

class crud {
	
	function crud(&$con) {
		$this->outlet =& $con;
		$this->tables = $this->outlet->db->schema->tables;		
		
	}
	
	function index() {
		$this->outlet->set('tables', $this->outlet->getTables());
	}
	
	function add() {
				
		$name = $this->outlet->validator->post('table');
		$table = $this->tables[$name];
		$fields = $table->fields;
		
		//Add controller
		ob_start();
		include(EXPATH.'/sys/tools/plugins/crud/views/_templates/crudController.php');
		$content = ob_get_contents();
		ob_clean();
		
		$output = $this->outlet->write($content, EXPATH.'/app/controllers/', $name.'_controller.php');
		
		//add layout
		ob_start();
		include(EXPATH.'/sys/tools/_templates/layout.php');
		$content = ob_get_contents();
		ob_clean();
		
		$output .= $this->outlet->write($content, EXPATH.'/app/views/_layouts/', $name.'.php');
		
		//add views
		$this->outlet->_checkIfFolderExists(EXPATH.'/app/views/'.$name);
		
			//add
			ob_start();
			include(EXPATH.'/sys/tools/plugins/crud/views/_templates/crudAdd.php');
			$content = ob_get_contents();
			ob_clean();
			
			var_dump($content);
			
			$output .= $this->outlet->write($content, EXPATH.'/app/views/'.$name.'/','add'.ucwords($name).'.php');
			
			//edit
			ob_start();
			include(EXPATH.'/sys/tools/plugins/crud/views/_templates/crudEdit.php');
			$content = ob_get_contents();
			ob_clean();
			
			$output .= $this->outlet->write($content, EXPATH.'/app/views/'.$name.'/','edit'.ucwords($name).'.php');
			
			//index
			ob_start();
			include(EXPATH.'/sys/tools/plugins/crud/views/_templates/crudView.php');
			$content = ob_get_contents();
			ob_clean();
			
			$output .= $this->outlet->write($content, EXPATH.'/app/views/'.$name.'/','index.php');
			
			//form
			ob_start();
			include(EXPATH.'/sys/tools/plugins/crud/views/_templates/crudForm.php');
			$content = ob_get_contents();
			ob_clean();
		
			$this->outlet->_checkIfFolderExists(EXPATH.'/app/views/_partials/forms');	
			$output .= $this->outlet->write($content, EXPATH.'/app/views/_partials/forms/',$name.'.php');
		
		
		//add validator method
		ob_start();
		include(EXPATH.'/sys/tools/plugins/crud/views/_templates/crudValidator.php');
		$content = ob_get_contents();
		ob_clean();
		
		$output .= $this->outlet->append($content."\r\n\r\n}\r\n?>", EXPATH."/app/support/", 'validator.php', '/\}*\s+\?\>/');
		
		//add privs that result
		$privs = $this->outlet->_getPluginClass('privileges');
		$output .= $privs->_addPriv('add '.$name);
		$output .= $privs->_addPriv('edit '.$name);
		$output .= $privs->_addPriv('delete '.$name);
		
		
		
			
		setFlash($output);
		redirect('generator/crud/index');
	}

	
}


?>
