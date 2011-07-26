<h1>Validation and Forms</h1>
<p>
	Validation and forms is one place where Redox truly excels. In many other frameworks, you have to go through the hassle of writing out every field and then setting up your form and linking everything together and blah blah blah. Redox handles form validation gracefully via the validator class found in app/support/validator.php and accomplishes its ease of use through a series of view form element functions. So let's set up an example.
</p>
<p>
	Most websites require some form of login form (let's assume we're not just going to install the login module), so let's create that scenario. First let's see how the view for the login form would look:
</p>
<pre>
&lt;form action=&quot;&quot; method=&quot;post&quot;&gt;
	&lt;dl&gt;
		&lt;dt&gt;&lt;label for=&quot;username&quot;&gt;Username:&lt;/label&gt;&lt;/dt&gt;
			&lt;dd&gt;&lt;?php echo form::text(&#x27;username&#x27;); ?&gt;&lt;/dd&gt;
		&lt;dt&gt;&lt;label for=&quot;password&quot;&gt;Password:&lt;/label&gt;&lt;/dt&gt;
			&lt;dd&gt;&lt;?php form::password(&#x27;password&#x27;); ?&gt;&lt;/dd&gt;
	&lt;/dl&gt;
	&lt;input type=&quot;Submit&quot; value=&quot;Submit&quot; /&gt;
&lt;/form&gt;
</pre>
<p>
	The only thing special in this bit of code is the use of two Redox form functions: echo form::text() and form::password(). What these two functions do for you is make sure that if the form is posted and it fails validation it will automatically return whatever was typed in by the user (passwords, being an exception). Likewise, it allows you to preload the values of these fields with a single call in the controller (we'll get to that later). There are several of these functions, one for each HTML form field, and are detailed in the API. In general for echo form::text() and form::password() all you will need to supply are their field names as parameters.
</p>
<p>
	Now that we have a form the next place to write some stuff in is the validator, so let's look and see how we are going to actually validate the form input. The following code is what you would write in the validator to make the form fields required and have a certain length.
</p>
<pre>
class Validator extends Input {

	function Validator() {
		parent::Input();
		$this->prefix = '<span>';
		$this->suffix = '</span>';
		//$this->setMessage('required','This field is required');
	}

	function login_form() {
		//username
		$this->required('username');
		$this->minLength('username',4);

		//password
		$this->required('password');
		$this->minLength('password',4);

		//credentials
		$this->checkLogin();
	}

	function checkLogin() {
		if ($this->noErrors()) {
			$this->model('user','user');
			$this->setMessage('checklogin', 'Invalid username or password');
			$credentials = $this->user->check();

			if (!$credentials) {
				$this->setError('username', 'checklogin');
			} else {
				$this->state->set('loggedIn', true);
				$this->state->setVar('user_ID', $credentials->ID);
				$this->state->setVar('username', $credentials->username);
			}
		}
	}

}
</pre>
<p>
	Alright, so let's step through this.
</p>
<pre>
function Validator() {
	parent::Input();
	$this->prefix = '&lt;span&gt;';
	$this->suffix = '&lt;/span&gt;';
	//$this->setMessage('required','This field is required');
}
</pre>
<p>
	This bit of code here is simply calling the parent's constructor and then setting what I would like errors to be wrapped in, in this case &lt;span&gt; tag. The commented line would set the message for the "required" rule to "This field is required".
</p>
<pre>
function login_form() {
	//username
	$this->required('username');
	$this->minLength('username',4);

	//password
	$this->required('password');
	$this->minLength('password',4);

	//credentials
	$this->checkLogin();
}
</pre>
<p>
	This function defines our login form. Any function within the validator that takes the pattern name_form() is considered a form object and can later be used by the validate function. Here we see a couple of Redox's built-in validation functions, namely required() and minLength(). Required simply states that the field whose name I give must be posted and not an empty string. Minlength is likewise self explanatory, however it shows that some validation functions can take in multiple parameters. The most interesting part is the use of a non-Redox defined validation method "checkLogin". Let's see how that works.
</p>
<pre>
function checkLogin() {
	if ($this->noErrors()) {
		$this->model('user','user');
		$this->setMessage('checklogin', 'Invalid username or password');
		$credentials = $this->user->check();

		if (!$credentials) {
			$this->setError('username', 'checklogin');
		} else {
			$this->state->set('loggedIn', true);
			$this->state->setVar('user_ID', $credentials->ID);
			$this->state->setVar('username', $credentials->username);
		}
	}
}
</pre>
