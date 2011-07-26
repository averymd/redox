<h1>In Depth: Models</h1>
<p>
	Those versed in Computer Science know more than well enough that the model is where most of your complexity is going to be. In Redox, that regrettably still holds true, but because of the methodology of a web MVC framework, what has to be done in that model isn't really that bad. This is especially true if you take advantage of Redox's db schema. Long story short, the model is a class that represents an object that you will be working with. Most apps will, thus, have a user_model, because you generally want to have people log in and interact with your site as a user. Let's look at what we have to do to make a user_model.
</p>
<p>
	The first step is to go to the generator and create a model for your project. This will spit out the following for you:
</p>
<pre>
class user_model extends Model {

	function user_model() {
		parent::Model("user");

	}

}
</pre>
<p>
	Now at first sight this may not seem to do anything at all for us. That, however, may or may not be true. Lets say that when we filled in our database configuration file that we added the following schema:
</p>
<pre>
&lt;table name=&quot;user&quot;&gt;
	&lt;field name=&quot;first&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
	&lt;field name=&quot;last&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
	&lt;field name=&quot;username&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
	&lt;field name=&quot;password&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; password=&quot;true&quot; /&gt;
	&lt;field name=&quot;email&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
&lt;/table&gt;
</pre>
<p>
	Amazingly enough, the model that we created via the generator has the ability to do all of the following actions with no coding at all from us: get(), getAll(), update(), delete(), and add(). These functions do what 90% of most people will ever need to do from a normal model. From there you could augment these with more specialized functions like getting the number of posts in forum and so on. All of these functions will work directly out of the box, though, with the only work required being you filling out the schema. Let's take a look at how these functions get used in a controller.
</p>
<pre>
class sample_controller extends Controller {

	function sample_controller() {
		parent::Controller();
	}

	function index() {
		//load the model, and reference it as $this->user
		$this->model('user', 'user');
		
		//get user with id = 0
		$this->set('me', $this->user->get(0));
		//get all users
		$this->set('everyone', $this->user->getAll());
		//get all users WHERE last LIKE 'a%'
		$this->set('everyoneWithLastNameA', $this->user->getAll('last LIKE `a%`'));
		
		//validate our fake form
		$this->validator->validateForm('newuser') {
			//add the user whose values were submitted by the form
			$this->user->add();
			//update user 0 to have the same information as that which was submitted in the form
			$this->user->update(0);
		}
		
		$myEnemy = 1; //set user 1 as myEnemy 
		$this->user->delete($myEnemy); //delete him because he sucks.

	}

}
</pre>
<p>
	The get function returns a single object whose attributes represent the fields returned by the database query. So if I wanted to find out my last name I would do
</p>
<pre>
$me = $this->user->get(0);
echo $me->last;

//the getAll returns an array of such objects
$us = $this->user->getAll();
foreach($us as $person) {
	echo $person->first.' '.$person->last;
}
</pre>
<p>
	The insert function actually returns the insert_ID of the executed query, while update and delete return nothing.
</p>
<p>
	So clearly taking the few minutes to do the schema can save you a lot of tedious SQL query writing, but let's see how that would be accomplished, so that some of the more complicated queries that may need to be done can be written.
</p>
<pre>
class user_model extends Model
{
	function user_model() {
		parent::Model("user");

	}

	function check()
	{
		$password = md5($this->validator->forSQL('password'));
		$username = $this->validator->forSQL('username');
		$query = $this->db->query("
			SELECT
				ID,
				username
			FROM
				user
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


}
</pre>
<p>
	Here I have a function that will be fairly common among your applications, because it checks to see if a supplied username/password combination exists in our user table. There are a number of things to note here. First, we grab our username and password from the validator using the forSQL function which will insure that our form input is safe to use in a query. I md5 the password, since that is how I stored it in the database and then I write the query using the db object. It is important to keep in mind that the only object in redox that has access to the database object is a model, so you must keep your SQL in one place (shame on you for trying to do otherwise). 
</p>
