<?php

/*	CLASS Controller
 *	This class is effectively the mediator between the model and the view.
 *
 *	@author Chris
 */
class Controller {
	var $uri;
	var $state;
	var $validator;
	var $access;
	var $xhtml;

	var $vars;
	var $layout;
	var $view;

	/*	CONSTRUCTOR
	 *
	 *	Loads the global classes (statemachine, uri, and validator) into the instance variables.
	 *
	 *	@author Chris
	 */
	function Controller() {
		$this->uri =& redox::getUri();
		$this->cli =& $this->uri;
		$this->state =& redox::getState();
		$this->validator =& redox::getValidator();
		$this->access =& redox::getAccess();
		$this->bm =& redox::getBm();
		
		$this->controller = CONTROLLER;
		$this->func = FUNC;
		
		$this->modeldir = '/app/models/';

		// Figure out where the view is.
		if (STATUS == 0 && ($this->controller == 'generator' || $this->controller == 'guide' || $this->controller == 'scaffold')) {
			$this->viewdir = '/sys/tools';
		} else {
			$this->viewdir = '/app';
		}
		
		$this->layoutdir = $this->viewdir;
	}

	/*	index()
	 *
	 *	Function called if no function is specified in the url request
	 *
	 *	@author Chris
	 */
	function index() {
		header("HTTP/1.0 404 Not Found");
		die;
	}

	/*	model($model, $name)
	 *
	 *	Loads the model in an instance variable to be referenced by this controller.
	 *
	 *	@param $model = The name of the model file.
	 *	@param $name = The name of the instance variable.
	 *	@author Chris
	 */
	function model($model, $name = null) {
		if (!$name) { $name = $model; }
		$model .= '_model';

		if (file_exists(EXPATH.$this->modeldir.$model.'.php')) {
			include_once(EXPATH.$this->modeldir.$model.'.php');
			$this->$name = new $model();
		} else {
			$this->$name = new Model($name);
		}
	}

	/*	execute()
	 *
	 *	runs the function that the user has request in the url. if it cannot be called it...
	 *
	 *	@author Chris
	 */
	function execute() {
		if (is_callable(array(&$this, $this->func)) && strpos($this->func,'_') !== 0 && !in_array($this->func, array('model','execute','set','view','getYield','partial','repartial'))) {
			// Exists, doesn't begin with '_', and isn't one of the meta functions.
			call_user_func(array(&$this, $this->func));
		} else {
			if (!file_exists(EXPATH.$this->viewdir.'/views/'.$this->controller.'/'.$this->func.'.php')) {
				// Not a static view.
				redirect($this->controller.'/', true);
			}
		}
	}

	/*	set($name, $var)
	 *
	 *	adds a var to the $this->vars array to be included in the view.
	 *
	 *	@param $name = name to be used to the var in the view
	 *	@param $var = value of the variable
	 *	@author Chris
	 */
	function set($name, $var) {
		$this->vars[$name] =& $var;
	}

	/*	view()
	 *
	 *	Loads the view of the page, including layout, and handles static views as well.
	 *
	 *	@author Nathan
	 */
	function view() {
		$viewfile_redox = false;
		$layoutfile_redox = false;
		
		if ($this->view !== false) {
			if (($this->view != '') && file_exists(EXPATH.$this->viewdir.'/views/'.$this->controller.'/'.$this->view.'.php')) {
				$viewfile_redox = EXPATH.$this->viewdir.'/views/'.$this->controller.'/'.$this->view.'.php';
			} elseif (($this->view == '') && file_exists(EXPATH.$this->viewdir.'/views/'.$this->controller.'/'.$this->func.'.php')) {
				$viewfile_redox = EXPATH.$this->viewdir.'/views/'.$this->controller.'/'.$this->func.'.php';
			}
		}

		if ($this->layout !== false) {
			$yield = ''; // Only needs to be defined if there is a layout.
			if (($this->layout != '') && file_exists(EXPATH.$this->layoutdir.'/views/_layouts/'.$this->layout.'.php')) {
				$layoutfile_redox = EXPATH.$this->layoutdir.'/views/_layouts/'.$this->layout.'.php';
			} elseif (($this->layout == '') && file_exists(EXPATH.$this->layoutdir.'/views/_layouts/'.$this->controller.'.php')) {
				$layoutfile_redox = EXPATH.$this->layoutdir.'/views/_layouts/'.$this->controller.'.php';
			}
		}

		// Find out if we actually are using a view or layout, if so, and if data exists, load it in.
		if (($viewfile_redox || $layoutfile_redox) && count($this->vars) > 0) {
			foreach($this->vars as $key => $value) {
				$$key = $value;
			}
		}

		if ($viewfile_redox && $layoutfile_redox) {
			// We're using a layout so we need to calculate and store the view to insert it into the view.
			ob_start();
			include($viewfile_redox);
			$yield = ob_get_contents();
			ob_end_clean();
			ob_start();
			include($layoutfile_redox);
		} elseif ($viewfile_redox) {
			include($viewfile_redox);
		} elseif ($layoutfile_redox) {
			include($layoutfile_redox);
		}
	}

	/*	partial($name, $vars = '')
	 *
	 *	loads a small view that may be universal in the site with name $name.php. Adds
	 *	each of the variables in the array $vars into the partial for use
	 *
	 *	@param $name = name of the partial in the app/views/_partials folder
	 *	@return $partial = the view code
	 *	@author Chris
	 */
	function partial($name) {
		if (count($this->vars) > 0) {
			foreach($this->vars as $key => $value) {
				$$key = $value;
			}
		}

		include(EXPATH.$this->viewdir.'/views/_partials/'.$name.'.php');
		$partial = ob_get_contents();
		ob_end_clean();
		ob_start();
		return $partial;
	}

	/*	repartial($name, $loopover)
	 *
	 *	loads a small view that may be universal in the site with name $name.php and repeats it for
	 *	each member of the array $this->vars[$loopover]. The values for the repartial will be available
	 *	at $$loopover.
	 *
	 *	@param $name = name of the partial in the app/views/_partials folder
	 *	@param $loopover = name of the variable in the $this->vars array to loop over
	 *	@return $partial = the view code
	 *	@author Chris
	 */
	function repartial($name, $loopover) {
		if (count($this->vars) > 0) {
			foreach($this->vars as $key => $value) {
				if ($key != $loopover) {
					$$key = $value;
				}
			}
		} else {
			return '';
		}

		foreach($this->vars[$loopover] as $$loopover) {
			include(EXPATH.$this->viewdir.'/views/_partials/'.$name.'.php');
		}
		$partial = ob_get_contents();
		ob_end_clean();
		ob_start();
		return $partial;
	}

	function library($name) {
		include(EXPATH.'/sys/adapters/'.$name.'.php');
		$this->$name = new $name();
	}
	
	function useLayout($name) {
		$this->layout = $name;
	}
	
	function useView($name) {
		$this->view = $name;
	}
	
	function supportItem($file, $name) {
		include(EXPATH.'/app/support/'.$file.'.php');
		$class = array_pop(explode('/', $file));
		$this->$name = new $file();
	}

}

?>