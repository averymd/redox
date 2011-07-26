<?php tag::a('scaffold/add/'.$table, 'Add new record'); ?>
<table>
	<thead>
		<tr>
		<?php foreach($fields as $f) { ?>
			<th><?php echo $f->Field; ?></th>
		<?php } ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach($records as $r) { ?>
		<tr>
		<?php foreach($fields as $f) { ?>
			<td><?php echo $r->{$f->Field}; ?></td>
		<?php } ?>
			<td><?php tag::a("scaffold/edit/$table/$r->ID", 'Edit', 'class="button"'); ?></td>
			<td><?php tag::a("scaffold/delete/$table/$r->ID", 'delete', 'class="button" onclick="return confirm(\'are you sure you want to delete?\')"'); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>