<form method="post" action="">
	<?php echo $this->partial('forms/scaffold'); ?>
	<input type="submit" class="button" value="edit"/>
	<?php tag::a("scaffold/delete/$table/$ID", 'delete', array('class'=>"button", 'onclick'=>"return confirm('are you sure you want to delete?')")); ?>
</form>