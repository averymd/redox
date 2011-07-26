<?php if($mode == '') { ?>
<h2>Create a new Module</h2>
<form method="post" action="<?php echo FOLDER.'/generator/modules/create/' ?>">
	<dl>
		<dt><label for="newModuleName">Name:</label></dt>
			<dd>
				<?php echo form::text('newModuleName'); ?>
			</dd>
	</dl>
	<input type="Submit" class="button" value="Create Module Directory Structure" />
</form>

<h2>Select Tables to Dump into Module</h2>
<?php if ($modules) { ?>
<form method="post" action="<?php echo FOLDER.'/generator/modules/tables/' ?>">
	<dl>
		<dt><label for="moduleSelect">Name:</label></dt>
			<dd>
				<?php form::select('moduleSelect',$modules); ?>
			</dd>
	</dl>
	<input type="Submit" class="button" value="Select Tables for this Module" />
</form>
<?php } else { ?>
<p>There are no modules presently available. Copy module packages into <?php echo EXPATH; ?>/modules/ to use them from this panel.</p>
<?php }?>

<h2>Install a Module</h2>
<?php if ($modules) { ?>
<dl>
	<dt><label for="moduleSelect">Name:</label></dt>
		<dd>
			<?php form::select('moduleSelect2',$modules); ?>
		</dd>
</dl>
<input type="button" class="button" onclick="window.location = '<?php echo FOLDER.'/generator/modules/install/' ?>'+$('moduleSelect2').value;" value="Setup Module" />
<?php } else { ?>
<p>There are no modules presently available. Copy module packages into <?php echo EXPATH; ?>/modules/ to install them from this panel.</p>
<?php }?>

<?php } ?>



<?php if($mode == "install") { ?>
	<h2>Install the <?php echo $moduleName; ?> module</h2>
	<form method="post" action="<?php echo FOLDER; ?>/generator/modules/add">
		<input type="hidden" name="module_name" value="<?php echo $moduleName; ?>"/>
			<ul>
		<?php 
			$prevPath = "/";
			foreach($files as $f) { 	
		?>
			<?php if($f[0] != $prevPath) { 
					$prevPath = $f[0];
			?>
			</ul>
			
			<h3><?php echo $f[0]; ?></h3>
			<ul>
				
			<?php } ?>
				<?php if(!in_array($this->getModulePartName($f[1]).'_functions.php', $funcs) && stripos($f[1], '_functions.php') === false) { ?>
					<li>Add/Inject: <?php echo $f[1]; ?></li>
				<?php } else if(!in_array($this->getModulePartName($f[1]).'_functions.php', $funcs)) { ?>
					<?php if(stripos($f[0], 'controllers') !== false) { ?>
						<li>These functions will be injected: <?php echo $f[1]; ?> into <?php form::select($this->getModulePartName($f[1]), $existingControllers); ?></li>
					<?php } else { ?>
						<li>These functions will be injected: <?php echo $f[1]; ?> into <?php form::select($this->getModulePartName($f[1]), $existingModels); ?></li>
					<?php } ?>
				<?php } else if(stripos($f[1], '_functions.php') === false){ ?>
					<?php if(stripos($f[0], 'controllers') !== false) { ?>
						<li>You can either add the full <?php echo $f[1]; ?>, or inject the functions into <?php form::select($this->getModulePartName($f[1]), $existingControllers); ?></li>
					<?php } else { ?>
						<li>You can either add the full <?php echo $f[1]; ?>, or inject the functions into <?php form::select($this->getModulePartName($f[1]), $existingModels); ?></li>
					<?php } ?>
				<?php } ?>
		<?php } ?>
		 </ul>
		<input type="Submit" class="button" value="Install this module" />
	</form>
<?php } ?>


<?php if($mode == 'tables') { ?>
	<h2>Add SQL records to the module '<?php echo $moduleName ?>':</h2>
	<form method="post" action="<?php echo FOLDER; ?>/generator/modules/sql" >
		<input type="hidden" name="moduleName" value="<?php echo $moduleName ?>"/>
		<p>
			Select the tables you wish to be included in the SQL dump.
		</p>
		<dl>
		<?php foreach($tables as $t) { ?>
			<dt><label for="<?php echo $t->Name ?>"><?php echo $t->Name; ?>:</label></dt>
				<dt><?php form::checkbox($t->Name); ?></dt>
		<?php } ?>
		</dl>
		<input type="Submit" class="button" value="Create SQL"/>
	</form>
<?php } ?>