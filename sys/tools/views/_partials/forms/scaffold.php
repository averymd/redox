<dl class="scaffold">
<?php foreach($fields as $f) { ?>
	<dt><label for="<?php echo $f->Field; ?>"><?php echo $f->Field; ?></label></dt>
	<dd>
		<?php
			switch($f->Type) {
				case 'text':
					form::textarea($f->Field);
				break;
				default:
					form::text($f->Field);
				break;
			}
		?>
	</dd>
<?php } ?>
</dl>