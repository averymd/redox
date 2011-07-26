<?php
/**
 * The Validator class contains methods that are called to validate user input on forms throughout the site.  
 * 
 * Each method represents a different form that is being validated.
 * @package ANP
 */
class Validator extends Validate {

	function Validator() {
		parent::Validate();
    $this->prefix = '<strong>';
    $this->suffix = '</strong>';

	}
	
	
	function category_form() {
		
	}


	function game_form() {
		
	}

	function tag_form() {
		
	}

	function user_form() {
		//username
    $minLength = 6;
		$this->rule('required', 'username');
    $this->setMessage('minlength', 'This value should be at least ' . $minLength . ' long.');
		$this->rule(array('minlength', $minLength), 'username');
		$this->rule('uniqueUsername', 'username');

		//password
		$this->rule('required', 'password');
		$this->rule('password', 'password');
    
    //first name
		$this->rule('required', 'first');

		//email
		$this->rule('required', 'email');
		$this->rule('email', 'email');
		$this->rule('uniqueEmail', 'email');
	}

	function login_form() {
		//username
		$this->rule('required', 'username');

		//password
		$this->rule('required', 'password');

		//credentials
    $this->setMessage('checklogin', 'Invalid username or password.');
		$this->rule('checklogin', 'username');
	}

	function checkLogin() {
		if (!$this->errors) {
			$this->model('user','user');			
			$credentials = $this->user->check();

			if (!$credentials) {
				return false;
			}
			
			$this->state->set('loggedIn', true);
			$this->state->setVar('user_ID', $credentials->ID);
			$this->state->setVar('username', $credentials->username);
			
			return true;
		}
	}
  
  function uniqueEmail() {
		if (!$this->errors) {
			$this->model('user','user');
			$this->setMessage('uniqueEmail', 'That email address is already registered in the system.');
			
      return $this->user->checkUniqueEmail();
		}
	}
  
  function uniqueUsername() {
		if (!$this->errors) {
			$this->model('user','user');
			$this->setMessage('uniqueUsername', 'That username is already taken.');
			
      return $this->user->checkUniqueUsername();
		}
	}

}
?>
