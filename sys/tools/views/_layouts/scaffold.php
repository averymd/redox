<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Redox Scaffold</title>
	<style type="text/css">@import url("<?php echo FOLDER; ?>/sys/tools/_css/default.css");</style>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/public/_js/prototype.js"></script>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/public/_js/effects.js"></script>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/sys/tools/_js/global.js"></script>
</head>

<body>	
	<div class="outerwrapper">
		<ul class="links">
			<li><?php tag::a('scaffold/sql', 'run query', 'class="special"'); ?></li>
			<?php foreach($tables as $table) { ?>
			<li><?php tag::a('scaffold/tables/'.$table->Name, $table->Name); ?></li>
			<?php } ?>
		</ul>
		<?php echo $this->partial('topLinks'); ?>
		<div class="content" id="content">
			<div id="sectionwrapper">
			<?php if (hasFlash()) { ?>
			<div class="results" id="results"><?php echo getFlash(); ?></div>
			<?php } ?>
			<?php echo $yield; ?>
			</div>
		</div>
	</div>
</body>
</html>
