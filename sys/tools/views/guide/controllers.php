<h1>In Depth: Controllers</h1>
<p>
	Controllers are the gatekeepers in redox. They are essentially the middle man between model and controller and handle all the requests essential to application interaction. As such, they are in charge of sending data to the view, handling form posts, and sending emails.
</p>
<p>
	The best way to handle controllers is to write one. So let's create a new controller using the generator called "cool." Here's what gets created for us in our app/controllers/cool_controller.php file.
</p>
<pre>
class cool_controller extends Controller {

	function cool_controller() {
		parent::Controller();
	}

	function index() {

	}

}
</pre>
<p>
	Now this basically doesn't do anything for us except allow us to view our site at http://domain/cool/, but let's just add in a simple echo statement to see what it does.
</p>
<pre>
class cool_controller extends Controller {

	function cool_controller() {
		parent::Controller();
	}

	function index() {
		echo "cool";
	}

}
</pre>
<p>
	When you go to that same url, you should now see cool on the screen. So why did we put that echo statement in the index function? Simply put when a controller is supplied in the url and no function is given, it defaults to the index function. So if we added an awesome function like below, you could then go to http://domain/cool/awesome/ and it would echo awesome to the screen.
</p>
<pre>
class cool_controller extends Controller {

	function cool_controller() {
		parent::Controller();
	}

	function index() {
		echo "cool";
	}
	
	function awesome() {
		echo "awesome";
	}

}
</pre>
<p>
	Before we take a look at a more realistic and useful controller, let's consider what is going on in the background. First, by accessing our site at http://domain/cool/awesome/ we see that the first two uri segments are controller name and function name respectively. Because redox is about making things just work, you are also accomplishing two other things by accessing that url. First, you are loading in the layout associated with that controller. If you look at the source, you will see that it's not just an echo of "awesome" that you see, but instead, some basic XHTML as well. This is supplied by the layout that was created when you made your controller via the generator (it can be found in app/views/_layouts/cool.php). Second, by supplying a function it would load in a corresponding view file as well. Go to app/views/cool/index.php and add in an echo statement. Now go to http://domain/cool/ or http://domain/cool/index/ and you will see that your echo will show up. You can read more about this views and layouts stuff in the In depth: views section, but for now let's get back to looking at a real controller.
</p>
<pre>
class blog_controller extends Controller
{

	function blog_controller() {
		parent::Controller();

		//state variables
		$this->user_ID = $this->state->getVar('user_ID');

		//load model
		$this->model('user','user');
		$this->model('post','post');

		//load users for the sidebar
		$this->set('users', $this->user->getAllWithPosts());
		
		//load all tag for the sidebar
		$this->set('tag', $this->post->getTags());


	}

	function index() {			
		//find out if we need to filter our posts by tag or user
		$userfilter = $this->uri->segment(1);
		$tagfilter = $this->uri->segment(2);

		//get all our posts with the filters
		$this->set('posts', $this->post->getAll($userfilter, $tagfilter));

		//make the filter values available to the view
		$this->set('userfilter', ($userfilter == 'all' ? false : $userfilter));
		$this->set('tagfilter', $tagfilter);

		//validate that if the login form was posted, it is correct
		if($this->validator->validate('login')) {
			
			//initialize our privileges object with all of this user's access information
			$this->access->init();
			
			//redirect to whatever they last tried to get into, or default to blog/
			passThrough('blog/');
		}
		
		//let the view know that a form was posted here
		if($this->validator->posted) {
			$this->set('posted', true);			
		}

	}
}
</pre>