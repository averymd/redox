	function login_form() {
		//username
		$this->rule('required', 'username');

		//password
		$this->rule('required', 'password');

		//credentials
		$this->rule('checklogin', 'username');
	}

	function checkLogin() {
		if (!$this->errors) {
			$this->model('user','user');
			$this->setMessage('checklogin', 'Invalid username or password');
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