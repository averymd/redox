<?php

// Class to handle the validation of forms.
class Validate extends Confirm {
	var $uri;
	var $state;
	var $errors = false;
	var $defaults = array(
        'required' => 'This value must be provided.',
		'exists' => 'This value must be provided.',
		'number' => 'This value must be numeric.',
		'maxlength' => 'This value is too long.',
		'minlength' => 'This value is too short.',
		'phone' => 'This value must be a phone number.',
		'email' => 'This value must be an email address.',
		'password' => 'Make sure the password is at least 6 characters long with a letter and number.'
	);
	
	var $extraPost = array();
  
  var $prefix = '<span>';
  var $postfix = '</span>';

	var $posted = false; // use args
	var $preload = false; // no longer necessary to preload


	function Validate() {
		$this->uri =& redox::getUri();
		$this->state =& redox::getState();

		$this->posted = (count($_POST) > 0) || (count($_FILES) > 0);
		$this->errors = array();
		//$this->forms[$this->curForm] = new form();
	}

	function model($model, $name = null) {
		if (!$name) { $name = $model; }
		$model .= '_model';

		if (file_exists(EXPATH.'/app/models/'.$model.'.php')) {
			include_once(EXPATH.'/app/models/'.$model.'.php');
			$this->$name = new $model();
		} else {
			$this->$name = new Model($name);
		}
	}

	// Validation rules
	function rule($method, $fields, $error = false, $titles = false, $which = '_POST') {
			$numfields = 0;
			
			//$which = '_POST';

			global ${$which};

			$fieldVals = array();
			if (is_array($fields)) {
				// Fields appear first in the arguments passed to the confirm functions.
				foreach ($fields as $field) {
						$fieldVals[$field] = ifsetor(${$which}[$field], '');
						$numfields++;
				}
			} else {
				if ($titles) {
					$fieldVals[$titles] = ${$which}[$fields];
					$numfields++;
				} else {
					$fieldVals[$fields] = ${$which}[$fields];
					$numfields++;
				}
			}

	        $args = array();
			if (is_array($method)) {
				// 0th index is rule, all other indices are arguments.
				$rule = array_shift($method);
				foreach ($method as $argval) {
					$args[] = $argval;
				}
			} else {
				$rule = $method;
			}

			if (!$error && isset($this->defaults[$rule])) {
				$error = $this->defaults[$rule];
			} elseif (!$error) {
				$error = 'There is an error in this field.';
			}

	        foreach($fieldVals as $field => $value) {
				if (call_user_func_array(array(&$this, $rule), array_merge(array($value), $args))) {
					// Successfully validated.			
					continue;
				} else {				
					if(!isset($this->errors[$field]) || $this->errors[$field] == '') {
						$this->errors[$field] = $error;
					}
				}
	        }
		}

	function setForm($formName) {
		$this->curForm = $formName;
		$this->forms[$formName] = array();
	}

	function getForm($formName) {
		return $this->forms[$formName];
	}

	// Error Messages
	function setMessage($key, $value) {
		$this->defaults[$key] = $value;
	}

	function setError($key, $error) {
		if (!isset($this->errors[$key])) {
			$this->form->errors[$key] = $error;
		}
	}

	function validateForm($form) {
		if((isset($_POST) && count($_POST) > 0) || (isset($_FILES) && count($_FILES) > 0))
		{
			if(is_callable(array(&$this,$form.'_form')))
			{
				call_user_func(array(&$this,$form.'_form'));
				if(count($this->errors) == 0) {
					return true;
				}
			}
		}
		return false;
	}
	
	function error($index)
	{
		if(isset($this->errors[$index]))
			return $this->prefix . $this->errors[$index] . $this->suffix;
		else
			return '';
	}
	
	function clear() {
		$this->preload = null;
		$_POST = array();
	}

	function wholePostForSQL() {
		$return = array();
		foreach ($_POST as $key => $value) {
			$return[$key] = mysql_real_escape_string($value);
		}
		return $return;
	}

	function post($name) {
		$name = (string)$name;
		return (isset($_POST[$name]) && $_POST[$name] != '' ? $_POST[$name] : false);
	}
	
	function forSQL($name) {
		$name = (string)$name;
		$val = ((isset($_POST[$name])) ? mysql_real_escape_string($_POST[$name]) : false);
		if($val === false) {
			$val = (isset($this->extraPost[$name]) && $this->extraPost[$name] != '' ? mysql_real_escape_string($this->extraPost[$name]) : false);
		}
		return $val;
	}
	
	function addToPost($key, $value) {
		$this->extraPost[$key] = $value;
	}

	function get($name) {
		$name = (string)$name;
		return (isset($_GET[$name]) && $_GET[$name] != ''  ? $_GET[$name] : false);
	}

	function formVal($name) {
		if (isset($this->preload->$name)) {
			return treat::forDisplay($this->preload->$name);
		} else if(isset($_POST[$name])) {
			return treat::forDisplay($_POST[$name]);
		} else {
			return '';
		}
	}

	function preloadFormVals($obj) {
		$this->preload =& $obj;
	}

}

?>
