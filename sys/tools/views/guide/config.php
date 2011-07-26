<h1>The Config Directory</h1>
<p>There is very little configuration in Redox. A few things in there are very important though.</p>
<dl>
	<dt>Base</dt>
		<dd>In the base config file you will find an array whose indices are 'controller','folder', and 'profiler'. Controller just corresponds to the default controller when someone doesn't type in the full URL. Folder is for defining the name of the folder that you put Redox in... if there isn't one just leave it as empty quotes(''). Lastly, profiler is a bool value which determines if you want to show the benchmarking stuff at the bottom of the page. (Set to false to get rid of it).</dd>
	<dt>DB</dt>
		<dd>The db config file is pretty straight-forward and asks for your database information. There is one part of it however that requires some explanation: the schema. Basically, instead of constantly querying the database to find out the structure of your tables to accomplish ORM style models, Redox takes information supplied in this schema and builds the base queries for you. A sample schema will look something like this:
<pre>
&lt;table name=&quot;user&quot;&gt;
	&lt;field name=&quot;first&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
	&lt;field name=&quot;last&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
	&lt;field name=&quot;username&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
	&lt;field name=&quot;password&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; password=&quot;true&quot; /&gt;
	&lt;field name=&quot;email&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
&lt;/table&gt;

&lt;table name=&quot;forumpost&quot;&gt;
	&lt;field name=&quot;subject&quot; type=&quot;VARCHAR(255) NOT NULL&quot; editable=&quot;true&quot; /&gt;
	&lt;field name=&quot;body&quot; type=&quot;TEXT&quot; editable=&quot;true&quot; /&gt;
	&lt;has_one table=&quot;user&quot;&gt;
		&lt;field name=&quot;username&quot; /&gt;
	&lt;/has_one&gt;
	&lt;has_one table=&quot;thread&quot;&gt;
		&lt;field name=&quot;name&quot; /&gt;
	&lt;/has_one&gt;
&lt;/table&gt;</pre>
			What this allows you to do is create the database on your MySQL server with the click of a button, and use model functions without writing a single line of SQL. More on schema stuff in the models section.</dd>
	<dt>State</dt>
		<dd>The last config file needs a bit of explanation. If you haven't read the section on controllers/statemachine, I suggest you do so. Here you can set up any states that you know from the beginning will exist and also any session variables you may want to carry around. Typically you would set things like loggedIn and username in here as a state and a var.</dd>
</dl>