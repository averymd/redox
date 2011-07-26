<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Redox Guide</title>
	<style type="text/css">@import url("<?php echo FOLDER; ?>/sys/tools/_css/default.css");</style>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/public/_js/prototype.js"></script>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/public/_js/effects.js"></script>
	<script type="text/javascript" src="<?php echo FOLDER; ?>/sys/tools/_js/global.js"></script>
</head>

<body>	
	<div class="outerwrapper">
		<ul class="links">
			<li><?php tag::a('guide/quick','Quick Start Guide'); ?></li>
			<li><?php tag::a('guide/theidea','Why Use Redox?'); ?></li>
			<li><?php tag::a('guide/generator','The Generator'); ?></li>
			<li><?php tag::a('guide/validation','Validation and Forms'); ?></li>
			<li><?php tag::a('guide/statemachine','StateMachine'); ?></li>
			<li><?php tag::a('guide/access','Privileges and Access'); ?></li>


			<li><?php tag::a('guide/mvc','MVC Framework'); ?></li>
			<li><?php tag::a('guide/models','In Depth: Models'); ?></li>
			<li><?php tag::a('guide/views','In Depth: Views'); ?></li>
			<li><?php tag::a('guide/controllers','In Depth: Controllers'); ?></li>
			<li><?php tag::a('guide/ajax','AJAX Controller'); ?></li>
			<li><?php tag::a('guide/external','External Controller'); ?></li>

			<li><?php tag::a('guide/folderstructure','Folder Structure'); ?></li>
			<li><?php tag::a('guide/public','The Public Directory'); ?></li>
			<li><?php tag::a('guide/config','The Config Directory'); ?></li>
			<li><?php tag::a('guide/modules','Modules'); ?></li>
			
			<li><?php tag::a('guide/API','API'); ?></li>
		</ul>
		<?php echo $this->partial('topLinks'); ?>
		<div class="content">
			<?php echo $yield; ?>
		</div>
	</div>
</body>
</html>