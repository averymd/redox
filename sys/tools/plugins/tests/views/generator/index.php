<h2>Create a New Test</h2>
<form action="<?php echo FOLDER; ?>/generator/tests/addtest" method="post">
<dl>
	<dt><label for="test">Name:</label></dt>
		<dd><input type="text" class="text" name="test" id="test" value="" /></dd>
</dl>
<div class="inline"><input type="submit" class="button" value="Add Test" /></div>
</form>

<?php if ($tests) { ?>
<h2>Existing Tests</h2>
<table cellspacing="0" class="itemlist">
	<thead>
		<tr><th>Test Name</th>
			<th>Modified</th>
			<th>Edit</th>
			<th>Delete</th>
			<th>Run</th></tr>
	</thead>
	<tbody>
		<?php
		foreach ($tests as $test) {
			echo '<tr>';
			echo '<td>',$test[0],'</td>';
			echo '<td>',$test[1],'</td>';
			echo '<td><a class="button" href="',FOLDER,'/generator/tests/edit/',$test[0],'">Edit</a></td>';
			echo '<td><form action="',FOLDER,'/generator/tests/deletetest" method="post"><div><input type="hidden" name="name" value="',$test[0],'" /><input type="submit" class="button" value="Delete" /></div></form></td>';
			echo '<td><form action="',FOLDER,'/generator/tests/test" method="post"><div><input type="hidden" name="name" value="',$test[0],'" /><input type="submit" class="button" value="Run" /></div></form></td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>
<?php } ?>