<h1>Modules</h1>
<p>
	Modules are an enormously powerful part of Redox. Many frameworks have evolved into having the ability to add large chunks of functionality into them via external sources. Often, however, these are meant for end users and not for programmers, which means they are very difficult to customize (in a programmatic sense) and often you might as well have written the functionality yourself. This, however, leads to lots of code duplication and wasted time trying to reinvent the wheel. Take a login script as an example. Most of your apps will have one and the guts of it (the model and controller functions) will be almost identical each time you do it. The smart person would just go to a previous project and copy/paste their login stuff in for each project. Redox, however, has made that easier than you could've hoped for, and you won't have to worry about the copy/paste part.
</p>
<p>
	The power inherent in modules is that you are free to extract just about anything you want into a module, and then control how it is to be installed. Some people, for instance may want a blog to be easily added to any project - this is more like a normal plugin situation, in that all the functionality of that blog is contained within the module itself - and doing so could be done with the push of a button once it is put into the module directory structure. However, there are many more times like the login script where you find yourself wanting just a tiny bit of functionality that is tedious to rewrite and found all over the place to be magically inserted into your project. Modules are capable of handling either of the situations by giving you options in how a module is to be installed. Before we get into that, however, let's take a look at how you take something you've done and make it into a module.
</p>
<p>
	The only thing particularly different about the anatomy of a module is the directory structure it is put into. It takes the following form:
</p>
<pre>
ModuleName/
	config/
		schema.php
		state.php
	controllers/
		module_controller.php
		module2_controller.php
	models/
		module_model.php
		module_functions.php
	public/
		_css/
			default.css
		_js/
			global.js
	support/
		privileges.php
		validator.php
	views/
		module/	
			index.php
		module2/
			index.php
</pre>
<p>
	Now there are a few things worth mentioning in here. First, instead of db.php in the config directory you place only your schema additions (without the wrapping &lt;database/&gt; tag). Second, anything that is not a model or controller will be injected into the file that is currently in your project, if it exists. The public and views dir stuff will create the file if it doesn't. This means, that when you extract your functionality out into a module you should only include the functions you want to be placed in, with files like privileges.php and so on. Models and Controllers, however, are a little different. As you can see in the models dir there are two files: module_controller.php and module_functions.php. What this means is that the user will have an option when they install the module. They can either inject the functions from module_functions.php into a model that they choose, or they can add the model module_model.php in its entirety to their project. This allows for a lot of flexibility. So if you wanted to have a module that just injects code, and doesn't create separate models and controllers, you could just include a name_functions.php file in either the models or controllers directory. Here are a few samples of what you might want an _functions.php to look like:
</p>
<pre>
	function check()
	{
		$password = md5($this->validator->post('password'));
		$username = $this->validator->post('username');
		$query = $this->db->query("
			SELECT
				ID,
				userlevel,
				username
			FROM
				users
			WHERE
				password = '$password'
				AND
				username = '$username'
				AND
				ID != 0
		");
		if($query->numrows() > 0){
			return $query->row(0);
		}
	
		return false;
	}
	
	function doCoolStuff() {
		echo "I'm doing cool stuff";
	}
</pre>
<p>
	You see here that it has one level of indentation, so that it matches up with what you will find in your model/controller that already exists (making it a clean formatted injection). Note also that you do not include opening or closing php tag, only the functions you want to be added. This is the same for all files that you wish to inject, or that are injectedly automatically. If you don't care about file inject, you could just throw your models and controllers in this directory structure, and then upon installation the files will be added as they are.
</p>
<p>
	For a more thorough example, take a look at the login module that is included with the default redox installation.
</p>