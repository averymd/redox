<h2>Privileges</h2>
<a class="button" href="<?php echo FOLDER; ?>/generator/privileges/addschema">Add Privilege Tables</a>

<?php if (count($roles) > 0 && count($privs) > 0) { ?>
<h2>Update Privileges</h2>
<div style="overflow: auto;">
	<div class="left">
		<table class="privskey">
			<thead>
				<tr><th>Role</th></tr>
			</thead>
			<tbody>
				<?php foreach($roles as $r) { ?>
					<tr><td><?php echo $r->value; ?></td></tr>
				<?php } ?>
				<tr><td>
					<form action="<?php echo FOLDER; ?>/generator/privileges/addrole" method="post">
						<input type="text" class="text" name="value" value="" />
						<input type="submit" class="button" value="Add Role" />
					</form>
				</td></tr>
			</tbody>
		</table>
	</div>
	<form class="left" action="<?php echo FOLDER; ?>/generator/privileges/updateprivileges" method="post">
		<table class="privs">
			<thead>
				<tr>
					<?php foreach($privs as $p) { ?>
					<th><?php echo $p->value; ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tfoot><tr><td colspan="<?php echo count($privs); ?>"><input type="submit" class="button" value="Update" /></td></tr></tfoot>
			<tbody>
				<?php foreach($roles as $r) { ?>
				<tr>
					<?php foreach($privs as $p) { ?>
						<td><?php form::checkbox($r->ID.'_'.$p->ID,(isset($roleprivs[$r->ID]) && in_array($p->ID, $roleprivs[$r->ID]))); ?></td>
					<?php } ?>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</form>
	<form class="left" action="<?php echo FOLDER; ?>/generator/privileges/addprivilege" method="post">
		<table class="privs">
			<tbody>
				<tr><th><input type="text" class="text" name="value" value="" /><input type="submit" class="button" value="Add Privilege" /></th></tr>
			</tbody>
		</table>
	</form>
</div>
<?php } else { ?>

<h2>Add Role</h2>
<form action="<?php echo FOLDER; ?>/generator/privileges/addrole" method="post">
	<input type="text" class="text" name="value" value="" />
	<input type="submit" class="button" value="Add Role" />
</form>

<h2>Add Privilege</h2>
<form class="left" action="<?php echo FOLDER; ?>/generator/privileges/addprivilege" method="post">
	<input type="text" class="text" name="value" value="" />
	<input type="submit" class="button" value="Add Privilege" />
</form>

<?php } ?>