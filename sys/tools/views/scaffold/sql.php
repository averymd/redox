<form method="post" action="">
	<?php form::textarea('query','', array('style'=>'width:99%; height: 200px; margin-bottom:15px;')); ?>
	<input type="submit" class="button" value="Run query" />
</form>
<?php if(isset($result)) { ?>
<div class="result">
	<h2>Results</h2>
	<table>
		<thead>
			<tr>
			<?php foreach(get_object_vars($result[0]) as $key=>$value) { ?>
				<th><?php echo $key; ?></th>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
		<?php foreach($result as $r) { ?>
			<tr>
			<?php foreach(get_object_vars($result[0]) as $key=>$value) { ?>
				<td><?php echo $r->$key; ?></td>
			<?php } ?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
<?php } ?>