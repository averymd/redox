<?php

/*	CLASS Uri
 *	Used to gather time information for the profiler.
 *	It will take its parameters either from stdin or from the request URI.
 *
 *	@author Chris
 */

class Uri {
	var $segments;

	function Uri() {
		/*	LIMITATIONS
		 *	Invalid controller names:
		 *	- Anything that matches FOLDER at strpos === 0 (ie. /somedirectory could be invalid).
		 *	- index.php
		 */

		$segments = array();

		if (isset($_SERVER['REQUEST_URI'])) {
			$requestURI = $_SERVER['REQUEST_URI'];

			// Remove the folder from the beginning of REQUEST_URI.
			if (FOLDER != '' && strpos($requestURI, FOLDER) === 0) {
				$requestURI = substr($requestURI, strlen(FOLDER), strlen($requestURI));
			}

			// The only script inside the redox installation that will ever be executing is index.php at root.
			if (strpos($requestURI, '/index.php') === 0) {
				$requestURI = str_replace('/index.php', '', $requestURI);
			}

			// Remove querystring arguments.
			$requestURI = (strpos($requestURI, '?') !== false ? substr($requestURI, 0, strpos($requestURI, '?')) : $requestURI);

			$tok = strtok($requestURI, '/');
			while ($tok !== false) {
				$segments[] = $tok;
				$tok = strtok('/');
			}

		} elseif ($_SERVER['argc'] && array_shift($_SERVER['argv']) == 'index.php') {
			$segments = $_SERVER['argv'];
		} else {
			echo 'An error occurred trying to run Redox.';
			exit;
		}

		// VALUES HERE NOT GUARANTEED TO BE SAFE. This is set to simply have a record of what was asked for.


		// Figure out how to go about setting the controller and function info.
		// Assumes the first two segments are controller and function.
		switch (count($segments)) {
			default:
			case 2:
				$supplied_controller = array_shift($segments);
				$supplied_function = array_shift($segments);
				define('CONTROLLER', (confirm::controller_name($supplied_controller, false) ? $supplied_controller : DEFAULT_CONTROLLER));
				define('FUNC', (confirm::function_name($supplied_function) ? $supplied_function : 'index'));
			break;
			case 1:
				$supplied_controller = array_shift($segments);
				define('CONTROLLER', (confirm::controller_name($supplied_controller, false) ? $supplied_controller : DEFAULT_CONTROLLER));
				define('FUNC', 'index');
			break;
			case 0:
				define('CONTROLLER', DEFAULT_CONTROLLER);
				define('FUNC', 'index');
			break;
		}

		$this->setSegments($segments);
	}

	function setSegments($segments) {
		$this->segments = $segments;
	}

	function segment($num) {
		return (isset($this->segments[$num-1]) ? $this->segments[$num-1] : false);
	}

	function arg($num) {
		return $this->segment($num);
	}

	function allSegments() {
		return $this->segments;
	}

}

?>