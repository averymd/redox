<h1>In Depth: Views</h1>
<p>
	For a PHP programmer views should seem the most familiar of all the MVC pieces to you. This is because they are nothing more than a smattering echo statement woven into standard HTML. Redox, does, however, have a couple thing to help out and make your presentation layer a breeze to implement.
</p>
<p>
	The most obvious of these additions is the use of view injection. Most websites have bits of code that continue throughout the whole site and are usually extracted out into included headers, footers, navbars and so on. Redox has taken that idea and improved upon it in the style of many popular frameworks (.NET, Ruby on Rails..) by having a page template that view code simply gets injected into. It's very simple to use, and basically occurs completely automagically. So let's take a look at what you need to know.
</p>
<p>
	It is important to note that layouts are specific to each controller and are loaded automatically without you writing any code. So a good way to look at this is that every controller has one layout and every controller function then has one corresponding view. Lets dissect this and understand what is happening - below is the code for a simple layout that is generated via the generator's add controller method.
</p>
<pre>
&lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Strict//EN&quot; 
					&quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd&quot;&gt;
					
&lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot; xml:lang=&quot;en&quot; lang=&quot;en&quot;&gt;

&lt;head&gt;
	&lt;title&gt;&lt;/title&gt;
	&lt;?php
		echo $this-&gt;xhtml-&gt;gen(&#x27;style&#x27;, &#x27;default&#x27;);
		echo $this-&gt;xhtml-&gt;gen(&#x27;script&#x27;, &#x27;prototype&#x27;);
		echo $this-&gt;xhtml-&gt;gen(&#x27;script&#x27;, &#x27;global&#x27;);
	?&gt;
&lt;/head&gt;

&lt;body&gt;
	&lt;?php echo $yield; ?&gt;
&lt;/body&gt;

&lt;/html&gt;
</pre>
<p>
	There are a couple of things to note here, namely, the call to $this->xhtml->gen() and echo $yield. The first of these is simply a call to generate the appropriate XHTML tag based on the information you supply to it. So when you tell it the type of tag you want to generate (the first argument) it then takes the preceding arguments and uses them intelligently. In this case it will generate style and script tag that point to default.css, prototype.js, and global.js. The $yield variable, however, is something entirely different. Yield in this case can be thought of simply as the content of the view. So, whatever you put in your view file will be injected in between the two body tag. Simple enough, right?
</p>
<p>
	Since the default function for any controller is index(), index.php will be automatically created for you when you add a new controller to your project. Initially, it will be completely blank, but if you put something simple in there you and look at your site in a browser you will see that it will be thrown in the body part of the html as you would expect. Views naturally have full access to all the php you want to put in them, however, the varaibles that they have access to are limited to those that you've explicitly set in the controller using the $this->set('varname', value) function. These variables are also available to the layout. Essentially, anything that can be done in a layout can be done in a view and vice-versa (except for view loading, obviously).
</p>
<p>
	So as stated before, view and layout loading are all automatic. This means that when someone goes to http://domain/mycon/myfunc it will execute the controller function myfunc in the controller mycon and then load the layout mycon.php from the app/views/_layouts directory and then inject the view app/views/mycon/myfunc.php into the yield variable. This makes the common header/footer scenario incredibly easy and seamless for you. Thus encouraging the reduction of code repetition, but there are plenty of times when things will be repeated across a couple of views and you don't want to put a control statement in the layout to handle it. We have a solution for that as well: partials.
</p>
<p>
	Partials are simply bits of view code that you want to extract out and use in multiple places. One such example might be a toolbar that only appears in certain views. To make this happen all you have to do is create a new file in app/views/_partials/ and put your code in there. Then you will need to add one line to your view or layout:
</p>
<pre>
&lt;?php echo $this-&gt;partial(&#x27;mypartial&#x27;); ?&gt;
</pre>
<p>
	This statement will go in app/views/_partials/ and look for mypartial.php and then echo it out. Just like views, partials have access to all the variables you've explicitly set in the controller. Another thing you can do with partials is use them in place of foreach statements like so:
</p>
<pre>
&lt;?php echo $this-&gt;repartial(&#x27;mypartial&#x27;, &#x27;people&#x27;); ?&gt;
</pre>
<p>
	Assuming you set a variable in your controller called people and it is an array, your partial 'mypartial.php' will be echoed for every member in $people. To access each member in your partial you simply use the variable name you referenced like so:
</p>
<pre>
&lt;li&gt;&lt;?php echo $people-&gt;firstname.&#x27; &#x27;.$people-&gt;lastname; ?&gt; - &lt;?php echo $people-&gt;phone; ?&gt;&lt;/li&gt;
</pre>
