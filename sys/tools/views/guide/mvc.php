<h1>MVC Framework</h1>
<p>For those who have never heard of MVC style programming, it was invented in the 70's as a new methodology for making logical programs that were very easy to put down and come back to sometime later. Ultimately it boils down to the separation of the different aspects of a program: Model, View, and Controller. Below is an explanation of each of these aspects as they relate to Redox.</p>

<dl>
	<dt>Model</dt>
		<dd>The Model is effectively the database layer. All of your queries and data manipulation are done in the model. In Redox the model is the only object that has access to the database object (db) and thus you are somewhat forced to adhere to this standard. It should be noted that the model really is only for database interaction and should be devoid of HTML or anything not related to data processing. They key is to ensure that the model remains unaware of how the information it provides is being used (via the controller and view).</dd>
	<dt>View</dt>
		<dd>This is the presentation layer and is the basis for everything you see. The controller feeds the view the information necessary for it to display the appropriate html. As such only control structures and variables should be found in the view. Logic of any kind should be reserved for the model (aside from display logic of course). For most people this is just your standard old html code with a few PHP echo statements thrown in here or there.</dd>
	<dt>Controller</dt>
		<dd>The Controller is the messenger of your app. It's responsible for all of the basic setup of the php for the page and is the bridge between the model and the view. No presentation is ever done in the controller (even for ajax calls), which means that the controller should remain completely devoid of html. Ultimately, the controller mostly just handles requests and making sure the information is provided for the right response. Thus, it is usually just take care of form posting, setting the correct variables from model functions, and handling urls.</dd>
</dl>
<p>As you can see, everything has a clearly defined place in Redox.</p>