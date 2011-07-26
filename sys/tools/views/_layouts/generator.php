<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Redox Generator</title>
	<style type="text/css">@import url("<?php echo FOLDER; ?>/sys/tools/_css/default.css");</style>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/public/_js/prototype.js"></script>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/public/_js/effects.js"></script>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/sys/tools/_js/global.js"></script>
</head>

<body>	
	<div class="outerwrapper">
		<ul class="links">
			<?php foreach($plugins as $plug) { ?>
				<li><a href="<?php echo FOLDER; ?>/generator/<?php echo $plug; ?>" class="<?php echo ($plug == $pluginName ? 'focused' : ''); ?>"><?php echo $plug; ?></a></li>
			<?php } ?>
		</ul>
		<?php echo $this->parentPartial('topLinks'); ?>
		<div class="content" id="content">
			<h1>Generator</h1>
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
