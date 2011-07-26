<?php
// Start the session.
session_start();

class redox {

	function redox() {
		// Determine the absolute path to the redox installation.
		define('EXPATH',getcwd());

		// Load the very basic configuration and load global use functions.
		include(EXPATH.'/config/base.php');
		include(EXPATH.'/sys/helpers/utils.php');

		// Turn on errors using the custom error handler.
		ini_set('display_errors','1');
		ini_set('display_startup_errors','1');
		ini_set('html_errors','1');
		ini_set('docref_root','http://www.php.net/');
		error_reporting(E_ALL);
		set_error_handler('redoxErrorHandler');

		// Load the benchmarking class.
		include(EXPATH.'/sys/base/benchmark.php');
	}
	
	function setupState() {
		/*******************************************************************
		*					Statemachine
		*
		* If statemachine is not already in the session we need to put it 
		* there with passThroughURL and flash variables in it as well as the 
		* variables and states set up in the state config file
		*******************************************************************/
		
		
	}
	
	function loadBaseClasses() {
		// Load other config files.
		$dir = dir(EXPATH.'/config/');
		while (false !== ($entry = $dir->read())) {
			if (substr($entry, strlen($entry)-4, 4) == '.php' && strpos($entry, 'state') === false) {
				include_once($dir->path . $entry);
			}
		}

		// Load other base classes.
		$dir = dir(EXPATH.'/sys/base/');
		include_once($dir->path . 'confirm.php');
		while (false !== ($entry = $dir->read())) {
			if (substr($entry, strlen($entry)-4, 4) == '.php') {
				include_once($dir->path . $entry);
			}
		}

		// Load system helpers.
		$dir = dir(EXPATH.'/sys/helpers/');
		while (false !== ($entry = $dir->read())) {
			if (substr($entry, strlen($entry)-4, 4) == '.php') {
				include_once($dir->path . $entry);
			}
		}

		// Load app support classes.
		$dir = dir(EXPATH.'/app/support/');
		while (false !== ($entry = $dir->read())) {
			if (substr($entry, strlen($entry)-4, 4) == '.php') {
				include_once($dir->path . $entry);
			}
		}
		$dir->close();
		
	}
	
	function &getBm() {
		static $bm = null;
		if($bm == null) {
			$bm = new Benchmark();
		}
		return $bm;
	}
	
	function &getValidator() {
		static $vali = null;
		if($vali == null) {
			$vali = new Validator();
		}
		return $vali;
	}
	
	function &getUri() {
		static $uri = null;
		if($uri == null) {
			$uri = new Uri();
		}
		return $uri;
	}
	
	function &getDb() {
		static $db = null;
		if($db == null) {
			$db = new Database();
		}
		return $db;
	}
	
	function &getAccess() {
		static $access = null;
		if($access == null) {
			$access = new Access();
		}
		return $access;
	}
	
	function &getState() {
		static $state = null;
		if($state == null) {
			if(!isset($_SESSION['sm'])) {
				$state = new StateMachine();
				$state->setVar('passThroughURL',''); // For automatic pass through on redirects
				$state->setVar('flash',''); // For sending bits of info from page to page
				include('config/state.php'); // User configuration for statemachine
			} else {
				// State machine is already in session, unserialize it so we can use it in the controller
				$state = unserialize($_SESSION['sm']);
			}
		}
		return $state;
	}
	
	function &getController() {
		static $controller = null;
		if($controller == null) {
			if (file_exists(EXPATH.'/app/controllers/'.CONTROLLER.'_controller.php')) {
				include(EXPATH.'/app/controllers/'.CONTROLLER.'_controller.php');
				$redox_cname = str_replace('-', '_', CONTROLLER."_controller");
				$controller = new $redox_cname();
			} else if (STATUS == 0 && (CONTROLLER == 'generator' || CONTROLLER == 'scaffold' || CONTROLLER == 'guide')) {
				include(EXPATH.'/sys/tools/'.CONTROLLER.'_controller.php');
				$redox_cname = CONTROLLER."_controller";
				$controller = new $redox_cname();
			} else {			
				$controller = new Controller();
			}
		}
		return $controller;
	}
	
	function run() {

		// Instantiate the benchmarker so we can get the profiler information.
		$bm =& $this->getBm();

		$bm->start('Total'); // Capture script start time.
		$bm->start('Base Classes'); // Capture base class start time.
		ob_start();
		$this->loadBaseClasses();
		ob_end_clean();
		$bm->end('Base Classes'); // Capture base class end time.
		$bm->start('Controller'); // Capture controller start time.

		// Prepare for capturing all output.
		ob_start();
		
		// Instantiate Base Classes
		$uri =& $this->getUri();
		$validator =& $this->getValidator();
		$db =& $this->getDb();
		$access =& $this->getAccess();

		// Controller Execution
		
		$controller =& $this->getController();
		$controller->execute(); // Run the function that was called in the URL
		$controller->view(); // Show the view.

		$bm->end('Controller'); // Capture controller end time.

		$_SESSION['sm'] = serialize($this->getState()); // Serialize and store statemachine.

		$bm->end('Total'); // Capture script end time.

		// Script has finished execution, grab output.
		addProfileInfo();


		/*******************************************************************
		*					Handle cleanup/sending mail
		*******************************************************************/

		handleMail();
	}
	
}

$redox = new redox();
$redox->run();

?>