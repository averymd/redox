<h1>The Public Directory</h1>
<p>
	Though the public directory is more or less self explanatory here is a breakdown of what is in it, and what else might go in it. It is important to note, that because of the htaccess file included with Redox, the public folder is the only directly accessible folder, all other access attempts will be re-written to index.php.
</p>
<dl>
	<dt>_css</dt>
		<dd>This is where you should store all your Cascading Style Sheets for your site. The function $this->xhtml->get('style', 'default'); as an example will look for default.css in this folder.</dd>
	<dt>_js</dt>
		<dd>Just like _css $this->xhtml->gen('script', 'default'); will look in this folder for your JavaScript files.</dd>
	<dt>_images</dt>
		<dd>Same as above but with images</dd>
	<dt>_uploads</dt>
		<dd>While not included by default if you plan to have an app that will handle uploading it is suggested that you isolate them into an _uploads folder within the public folder.</dd>
</dl>