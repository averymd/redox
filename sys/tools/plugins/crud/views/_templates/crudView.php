<?php echo '<?php if ($this->access->to("add '.$name.'")) { tag::a("'.$name.'/add'.ucwords($name).'", "Add"); } ?>'; ?>
<table cellspacing="0">
	<thead>
		<tr>
			<?php foreach($fields as $field) { ?>
			<th><?php echo $field->name; ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php echo '<?php foreach($rows as $row) { ?>'."\r\n"; ?>
		<tr>
			<?php foreach($fields as $field) { ?>
			<td><?php echo '<?php echo $row->'.$field->name.'; ?>'; ?></td>
			<?php } ?>
			<td><?php echo '<?php if($this->access->to("edit '.$name.'")) { tag::a("'.$name.'/edit'.ucwords($name).'/$row->ID", "Edit"); } ?>'; ?></td>
		</tr>
		<?php echo'<?php } ?>'."\r\n";; ?>
	</tbody>
</table>