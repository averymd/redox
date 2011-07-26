<h2>Create a New Model</h2>
<form action="<?php echo FOLDER; ?>/generator/models/add" method="post">
<dl>
	<dt><label for="model">Name:</label></dt>
		<dd><input type="text" class="text" name="model" id="model" value="" /></dd>
</dl>
<div class="inline"><input type="submit" class="button" value="Add Model" /></div>
</form>

<?php if ($models) { ?>
<h2>Existing Models</h2>
<table cellspacing="0" class="itemlist">
	<thead>
		<tr><th>Model Name</th>
			<th>Modified</th>
			<th>Edit</th>
			<th>Delete</th></tr>
	</thead>
	<tbody>
		<?php
		foreach ($models as $model) {
			echo '<tr>';
			echo '<td>',$model[0],'</td>';
			echo '<td>',$model[1],'</td>';
			echo '<td><a class="button" href="',FOLDER,'/generator/models/edit/',$model[0],'">Edit</a></td>';
			echo '<td><form action="',FOLDER,'/generator/models/delete" method="post"><div><input type="hidden" name="name" value="',$model[0],'" /><input type="submit" class="button" value="Delete" /></div></form></td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>
<?php } ?>