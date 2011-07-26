<?php

class generator_controller extends Controller {
	var $db;

	function generator_controller() {
		parent::Controller();
		$this->db =& redox::getDb();
		$this->func = 'index';
		$this->layoutdir = '/sys/tools';
	}

	function index() {
		$this->set('plugins', $this->_getPlugins());
		
		$pluginName = (FUNC == 'index' ? 'controllers' : FUNC);
		$this->set('pluginName', $pluginName);
		
		$this->viewdir = '/sys/tools/plugins/'.$pluginName;
		
		$pluginFunc = ($this->uri->segment(1) ? $this->uri->segment(1) : 'index');
		
		$plugin =& $this->_getPluginClass($pluginName);
		$plugin->$pluginFunc();
	}
	
	function parentPartial($name) {
		if (count($this->vars) > 0) {
			foreach($this->vars as $key => $value) {
				$$key = $value;
			}
		}

		include(EXPATH.'/sys/tools/views/_partials/'.$name.'.php');
		$partial = ob_get_contents();
		ob_end_clean();
		ob_start();
		return $partial;
	}
	
	function _getPluginClass($name) {
		include(EXPATH."/sys/tools/plugins/$name/$name.php");
		return new $name($this);
	}

	function _edit($filepath) {
		if (isset($_SERVER) && $_SERVER['REQUEST_METHOD'] == 'POST') {
			// TODO: Set filepath in a safe manner.
			$fh = fopen($_POST['filepath'], 'w');
			fwrite($fh, $_POST['filecontent']);
			fclose($fh);

			setFlash('Your file, '.$_POST['filepath'].', has been saved.');
			$this->useView('none');
		} else {
			$this->useLayout('none');

			$filename = $this->uri->segment(2);
			if (!$filename) { redirect(''); }
		
			$this->set('filepath', $filepath);
			$this->set('filecontent', file_get_contents($filepath));
					
		}
		$this->viewdir = '/sys/tools';
		$this->view = 'edit';
	}
	
	

	/*******************************************************************
	*					utility functions
	*******************************************************************/
	
	function _getPlugins() {
		$list = false;
		$dir = dir(EXPATH.'/sys/tools/plugins/');
		while (false !== ($file = $dir->read())) {
			if (stripos($file, '.') === false) {
				$list[] = $file;
			}
		}
		$dir->close();
		return $list;
	}

	function _acceptableFile($name) {
		switch($name) {
			case '.gitignore':
			case '.DS_Store':
				return false;
			break;
		}
		return true;
	}
	
	function _checkIfFolderExists($path) {
		if(!file_exists($path)) {
			echo $path;
			$newpath = explode('/',$path);
			array_pop($newpath);
			if($this->_checkIfFolderExists(implode('/',$newpath))) {
				mkdir($path);
			}
		} else {
			return true;
		}
	}

	function getModels() {
		$list = false;
		$dir = dir(EXPATH.'/app/models/');
		while (false !== ($file = $dir->read())) {
			if (stripos($file, '.php') !== false) {
				$list[] = array(substr($file,0,strlen($file)-10), date(DATE_HUMAN, filemtime($dir->path.$file)));
			}
		}
		$dir->close();
		return $list;
	}

	function getControllers() {
		$list = false;
		$dir = dir(EXPATH.'/app/controllers/');
		while (false !== ($file = $dir->read())) {
			if (stripos($file, '.php') !== false) {
				$list[] = array(substr($file,0,strlen($file)-15), date(DATE_HUMAN, filemtime($dir->path.$file)));
			}
		}
		$dir->close();
		return $list;
	}

	function getModules() {
		$list = false;
		$dir = dir(EXPATH.'/modules/');
		while (false !== ($file = $dir->read())) {
			if (stripos($file, '.') === false) {
				$list[] = $file;
			}
		}
		$dir->close();
		return $list;
	}

	function getTests() {
		$list = false;
		$dir = dir(EXPATH.'/app/tests/');
		while (false !== ($file = $dir->read())) {
			if (stripos($file, '.php') !== false) {
				$list[] = array(substr($file,0,strlen($file)-9), date(DATE_HUMAN, filemtime($dir->path.$file)));
			}
		}
		$dir->close();
		return $list;
	}

	function getTables() {
		$list = array();
		$tables = $this->db->schema->tables;
		foreach ($tables as $t) {
			$list[(string) $t->name] = $t->name;
		}
		return $list;
	}
	
	function getModulePartName($part) {
		$pos = stripos($part, "_functions");
		if($pos === false) {
			$pos = stripos($part, "_controller");
			if($pos === false) {
				$pos = stripos($part, "_model");
			}
		}
		if($pos !== false) {
			return substr($part, 0, $pos);
		}
	}
	
	function findFunctions($files) {
		$return = array();
		foreach($files as $f) {
			if(stripos($f[1],'_functions') !== false) {
				$name = $this->getModulePartName($f[1]);
				if(in_array(array($f[0], $name.'_controller.php'), $files) || in_array(array($f[0], $name.'_model.php'),$files)) {
					$return[] = $f[1];
				}
			}
		}
		return $return;
	}
	
	function getFiles($dirPath, $curPath = '') {
		$files = array();
		$dir = dir($dirPath);	
		while (false !== ($file = $dir->read())) {
			if (stripos($file, '.') === false) {
				$files = array_merge($files, $this->getFiles($dirPath.'/'.$file, $curPath.$file.'/'));
			} else if(stripos($file, '..') === false && strlen($file) > 1 && $this->_acceptableFile($file)) {
				$files[] = array($curPath, $file);
			}
		}
		return $files;
	}
	
	function append($text, $path, $filename, $regex = 'end', $source = false) {
		if(!$source) {
			$source = $filename;
		}
		
		if(!file_exists($path.$filename)) {
			return "<p class='error'>The file $filename does not exist at $path</p>";
		}

		$contents = file_get_contents($path.$filename);

		if($regex != 'end') {
			$contents = preg_replace($regex, $text, $contents);
		} else {
			$contents .= $text;
		}
		
		$fh = fopen($path.$filename, 'w+');
		fwrite($fh, $contents);
		fclose($fh);
		return "<p class='success'>The file $source was appended to $path$filename</p>";
	}
	
	function write($text, $path, $filename) {
		if(file_exists($path.$filename)) {
			return "<p class='error'>The file $filename already exists at $path</p>";
		}
		$fh = fopen($path.$filename, 'w+');
		fwrite($fh, $text);
		fclose($fh);
		return "<p class='success'>The file $filename was added to $path</p>";
	}
}

?>