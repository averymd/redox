<form method="post" action="">
	<dl>
		<?php echo '<?php echo $this->partial(\'forms/'.$name.'\'); ?>'."\r\n"; ?>
	</dl>
	<input type="submit" value="Update" />
	<?php echo '<?php tag::a("'.$name.'/delete'.ucwords($name).'/$ID", "Delete"); ?>'."\r\n"; ?>
</form>