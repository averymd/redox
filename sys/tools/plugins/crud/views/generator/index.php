<h2>Welcome to the CRUD creator!</h2>
<p>This will allow you to setup a basic crud framework for a table defined in the schema. This will give you a full add/edit/view/delete setup with no work and will be completely functional right out of the box. It will also create the necessary privileges for you.</p>
<form method="post" action="<?php echo FOLDER; ?>/generator/crud/add">
	<?php form::select('table', $tables); ?>
	<input type="submit" class="button" value="Build Framework">
</form>

