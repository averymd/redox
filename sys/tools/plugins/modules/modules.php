<?php

class modules {

	function modules(&$con) {
		$this->outlet =& $con;
		$this->outlet->set('modules', $this->outlet->getModules());
		$this->outlet->set('mode', $this->outlet->uri->segment(1));		
	}

	function index() {	
	}

	function tables() {
		if($this->outlet->validator->post('moduleSelect')) {
			$tables = $this->outlet->db->query('SHOW TABLE STATUS');
			$this->outlet->set('moduleName', $this->outlet->validator->post('moduleSelect'));
			$this->outlet->set('tables', $tables->result());
		} 
	}
	
	function sql() {
		if($this->outlet->validator->post('moduleName')) {
			$path = EXPATH.'/modules/'.$_POST['moduleName'].'/sql/';
			unset($_POST['moduleName']);

			$output = '';
			$dbargs = $this->outlet->db->dbparams;

			foreach($_POST as $key=>$value) {
				$file = $path.$key.'.sql';
				$command = "mysqldump --compact -h$dbargs[hostname] -u$dbargs[username] -p$dbargs[password] $dbargs[database] --tables $key > $file";
				system($command);
				$output .= "<p class='success'> the sql file for the table $key was added to $path</p>";
			}
			setFlash($output);
		} 
		redirect('generator/modules');
	}
	
	function create() {
		$name = $this->outlet->validator->post('newModuleName');
		if($name) {
			if(file_exists(EXPATH.'/modules/'.$name)) {
				setFlash("<p class='error'>The module '$name' already exists</p>");
				redirect('generator/modules');
			} else {
				mkdir(EXPATH.'/modules/'.$name);
				mkdir(EXPATH.'/modules/'.$name.'/config');
				mkdir(EXPATH.'/modules/'.$name.'/controllers');
				mkdir(EXPATH.'/modules/'.$name.'/models');
				mkdir(EXPATH.'/modules/'.$name.'/public');
				mkdir(EXPATH.'/modules/'.$name.'/support');
				mkdir(EXPATH.'/modules/'.$name.'/sql');
				mkdir(EXPATH.'/modules/'.$name.'/views');
				setFlash("<p class='success'>The directories for module '$name' have been created</p>");
				redirect('generator/modules');
			}
		}
		redirect('generator/modules');
	}

	function install() {
		$module = $this->outlet->uri->segment(2);
		
		if($module) {	
			$_POST['moduleSelect2'] = $module;
			$files = $this->outlet->getFiles(EXPATH.'/modules/'.$module);
			$funcs = $this->outlet->findFunctions($files);

			$con[''] = "Do not inject";
			$eCon = $this->outlet->getControllers();
			if($eCon) {
				foreach($eCon as $v) {
					$con[$v[0].'_controller.php'] = $v[0];
				}
			}

			$mod[''] = "Do not inject";
			$eMod = $this->outlet->getModels();
			if($eMod) {
				foreach($eMod as $v) {
					$mod[$v[0].'_model.php'] = $v[0];
				}
			}

			$this->outlet->set('existingControllers', $con);
			$this->outlet->set('existingModels', $mod);

			$this->outlet->set('files', $files);
			$this->outlet->set('funcs', $funcs);

			$this->outlet->set('moduleName', $module);
		} 
	}

	function add() {
			$PATH = 0;
			$NAME = 1;
			
			$injections = array();
			
			$module = $_POST['module_name'];
			
			$files = $this->outlet->getFiles(EXPATH.'/modules/'.$module);
			
			foreach($_POST as $k=>$v) {
				if($v != '') {
					$injections[] = $k;
				}
			}
			
			$output = '';
			
			foreach($files as $f) {
				$moduleDir = explode('/',$f[$PATH]);
				$content = file_get_contents(EXPATH."/modules/$module/$f[$PATH]/$f[$NAME]");
				if($this->outlet->_acceptableFile($f[$NAME])) {
					switch($moduleDir[$PATH]) {
						case 'config':							
					
							if($f[$NAME] != 'schema.xml') {
							
								$output .= $this->outlet->append($content."\r\n\r\n?>", EXPATH."/$f[$PATH]", $f[$NAME], '/\?\>/');
							
							} else {
							
								$output .= $this->outlet->append($content."\r\n\r\n</database>", EXPATH."/$f[$PATH]", $f[$NAME], '/<\/database>/');
							
							}
						break;
						case 'controllers':
							$funcName = $this->outlet->getModulePartName($f[$NAME]);
							
							if(in_array($funcName,$injections) && stripos($f[$NAME], '_functions') !== false) {
						
								$output .= $this->outlet->append($content."\r\n\r\n}\r\n?>", EXPATH."/app/$f[$PATH]", $_POST[$funcName], '/\}*\s+\?\>/', $f[$NAME]);
						
							} else if(!in_array($funcName, $injections) && stripos($f[$NAME], '_functions') === false){
							
								$this->outlet->_checkIfFolderExists(EXPATH."/app/$f[$PATH]");
								$output .= $this->outlet->write($content, EXPATH."/app/$f[$PATH]", $f[$NAME]);
						
							}
						break;
						case 'models':
							$funcName = $this->outlet->getModulePartName($f[$NAME]);
					
							if(in_array($funcName,$injections) && stripos($f[$NAME], '_functions') !== false) {
						
								$output .= $this->outlet->append($content."\r\n\r\n}\r\n?>", EXPATH."/app/$f[$PATH]", $_POST[$funcName], '/\}*\s+\?\>/', $f[$NAME]);
						
							} else if(!in_array($funcName, $injections) && stripos($f[$NAME], '_functions') === false){
							
								$this->outlet->_checkIfFolderExists(EXPATH."/app/$f[$PATH]");
								$output .= $this->outlet->write($content, EXPATH."/app/$f[$PATH]", $f[$NAME]);
						
							}
						break;
						case 'support':
					
							$content = file_get_contents(EXPATH."/modules/$module/$f[$PATH]/$f[$NAME]");
							$output .= $this->outlet->append($content."\r\n\r\n}\r\n?>", EXPATH."/app/$f[$PATH]", $f[$NAME], '/\}*\s+\?\>/');
							
						break;
						case 'views':
						
							if($moduleDir[$NAME] != '_layouts') {
								$funcName = $moduleDir[$NAME];
								
							} else {
								$funcName = substr($f[$NAME], 0, stripos($f[$NAME], '.php'));
							}
							
							if(in_array($funcName,$injections)) {
								if($moduleDir[$NAME] != '_layouts') {
									$conDir = $this->outlet->getModulePartName($_POST[$funcName]);								
									$f[$PATH] = preg_replace('/'.$moduleDir[1].'/', $conDir, $f[$PATH]);
									$this->outlet->_checkIfFolderExists(EXPATH."/app/$f[$PATH]");
									$output .= $this->outlet->write($content, EXPATH."/app/$f[$PATH]", $f[$NAME]);
								}
							} else {
						
								$this->outlet->_checkIfFolderExists(EXPATH."/app/$f[$PATH]");
								$output .= $this->outlet->write($content, EXPATH."/app/$f[$PATH]", $f[$NAME]);
							}
							
							
						break;
						case 'public':
														
							if(file_exists(EXPATH."/$f[$PATH]/$f[$NAME]")) {
						
								$output .= $this->outlet->append($content, EXPATH."/$f[$PATH]", $f[$NAME]);
							
							} else {
							
								$this->outlet->_checkIfFolderExists(EXPATH."/$f[$PATH]");
								$output .= $this->outlet->write($content, EXPATH."/$f[$PATH]", $f[$NAME]);
							}
						break;
						case 'sql':
							$output .= $this->_executModuleSQL($content, $f[$NAME]);
						break;
					}
				}
				
			}
			
			setFlash($output);
	}
	
	function _executModuleSQL($content, $name) {
		
		$queries = explode(";\n", $content);
		$out = '';
		
		foreach($queries as $q) {
			$result = $this->outlet->db->query($q);
			
			if($result->failed && stripos($q, 'CREATE TABLE') !== false) {
				$out .= "<p class='error'>The file $name failed to be added to the DB correctly, probably because the table exists</p>";
			} else if($out != '' && stripos($q, 'INSERT INTO') !== false && !$result->failed) {
				$out .= "<p class='success'>The values in $name were still added to your DB</p>";
			}
		}
		if($out == '') {
			$out .= "<p class='success'>The file $name was added to your DB</p>";
		}
		return $out;
	}
	
	

}	
?>