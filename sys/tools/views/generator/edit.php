<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo treat::xss($filepath); ?></title>
	<style type="text/css">@import url("<?php echo FOLDER; ?>/sys/tools/_css/default.css");</style>
	<script type="text/javascript">
		var original = '<?php echo treat::js($filecontent); ?>';

		function revert() {
			document.getElementById('filecontent').value = original;
		}
	</script>
</head>

<body class="edit">
	<div class="h1wrap"><h1><?php echo treat::xss($filepath); ?></h1></div>
	<form method="post" action="<?php echo FOLDER; ?>/generator/edit/">
		<div>
			<input type="hidden" name="filepath" value="<?php echo treat::attribute('cdata', $filepath); ?>" />
			<textarea id="filecontent" name="filecontent" rows="24" cols="100"><?php echo $filecontent; ?></textarea>

			<div class="right">
				<input type="button" class="button" value="Revert" onclick="revert();" />
				<input type="submit" class="button" value="Save" />
			</div>
		</div>
	</form>
</body>

</html>
