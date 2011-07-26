<?php

class login_controller extends Controller {

	function login_controller() {
		parent::Controller();
		
		if(!$this->access->loggedIn() && FUNC != 'login') { redirect('login/login'); }
		
	}

	function index() {
		
	}
	
	/********************************************************
	*			login and logout functions
	*********************************************************/ 

	function logout() {
		$this->state->resetAll();
		redirect('forum/login');
		
	}

	function login() {
		
		if($this->validator->validateForm('login')) {
			$this->access->init();
			setFlash('you are now loggedin');
			redirect('forum/index');
		}

	}

}

?>
