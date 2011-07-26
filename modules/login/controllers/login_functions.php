	/********************************************************
	*			login and logout functions
	* for your constructor:
	* if(!$this->access->loggedIn() && FUNC != 'login') { redirect('login/login'); }
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