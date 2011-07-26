<?php if (count($tables) > 0) { ?>
<h2>Create Tables in Database</h2>
<form method="post" action="<?php echo FOLDER; ?>/generator/schemas/addschema">
		<label for="table">Drop the tables if they already exist:</label>
		<?php form::checkbox('droptables'); ?>
	<input type="submit" class="button" value="Create Tables"/>
</form>

<h2>Create Form Fields</h2>
<form action="<?php echo FOLDER; ?>/generator/schemas/formfields" method="post">
	<dl>
		<dt><label for="table">From Table:</label></dt>
			<dd><?php form::select('table', $tables); ?></dd>
	</dl>
	<div class="inline"><input type="submit" class="button" value="Create" /></div>
</form>

<h2>Create Static Model Functions</h2>
<form action="<?php echo FOLDER; ?>/generator/schemas/staticmodel" method="post">
	<dl>
		<dt><label for="table">From Table:</label></dt>
			<dd><?php form::select('table', $tables); ?></dd>
	</dl>
	<div class="inline"><input type="submit" class="button" value="Create" /></div>
</form>
<?php } else { ?>
<h2>Generate Schema</h2>
<form action="<?php echo FOLDER; ?>/generator/schemas/generateschema" method="post">
	<dl>
		<dt><label for="table">Existing Database:</label></dt>
			<dd><?php form::select('table', $tables); ?></dd>
	</dl>
	<div class="inline"><input type="submit" class="button" value="Create" /></div>
</form>
<?php } ?>