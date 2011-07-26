<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title></title>
	<?php echo '<?php'."\r\n"; ?>
		tag::style('default');
		tag::script('prototype');
		tag::script('global');
	<?php echo '?>'."\r\n"; ?>
</head>

<body>
	<?php echo '<?php echo $yield; ?>'."\r\n"; ?>
</body>

</html>