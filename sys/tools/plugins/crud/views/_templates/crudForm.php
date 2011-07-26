<?php foreach($fields as $f) { ?>
	<dt><label for="<?php echo $f->name; ?>"><?php echo $f->name; ?></label></dt>
		<dd><?php echo '<?php form::text(\''.$f->name.'\'); ?>'; ?></dd>
<?php } ?>