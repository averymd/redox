<h2>Create a New Controller</h2>
<form action="<?php echo FOLDER; ?>/generator/controllers/add" method="post">
<dl>
	<dt><label for="controller">Name:</label></dt>
		<dd><input type="text" class="text" name="controller" id="controller" value="" /></dd>
</dl>
<div class="inline"><input type="submit" class="button" value="Add Controller" /></div>
</form>

<?php if ($controllers) { ?>
<h2>Existing Controllers</h2>
<table cellspacing="0" class="itemlist">
	<thead>
		<tr><th>Controller Name</th>
			<th>Modified</th>
			<th>Edit</th>
			<th>Delete</th></tr>
	</thead>
	<tbody>
		<?php
		foreach ($controllers as $controller) {
			echo '<tr>';
			echo '<td>',$controller[0],'</td>';
			echo '<td>',$controller[1],'</td>';
			echo '<td><a class="button" href="',FOLDER,'/generator/controllers/edit/',$controller[0],'">Edit</a></td>';
			echo '<td><form action="',FOLDER,'/generator/controllers/delete" method="post"><div><input type="hidden" name="name" value="',$controller[0],'" /><input type="submit" class="button" value="Delete" /></div></form></td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>
<?php } ?>