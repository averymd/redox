<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <title>LudusNovus .:. GameON! .:. <?php echo $title ?></title>
	<?php
		tag::style('default');
		tag::style('scaffold');
		tag::script('global');
	?>
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
</head>

<body>
  <div id="banner"></div>
	<div id="columns">
		<div id="navigation">
			<ul>
				<li><a href="/game">Games</a></li>
				<li><a href="/category">Categories</a></li>
				<li><a href="/tag">Tags</a></li>
        
			</ul>
      
      <h3>Personal Tools</h3>
      <ul>
        <?php if (!$this->access->loggedIn()): ?>
        <li><a href="/login">Login / register</a></li>
        <?php else: ?>
        <li><a href="/user/edit/<?php echo idobfuscator::obfuscate($this->state->getVar('user_ID')); ?>">Edit profile</a></li>
        <li><a href="/login/logout">Logout</a></li>
        <?php endif; ?>        
      </ul>
      
      <?php if ($this->access->role('administrator')): ?>
      <h3>Administration</h3>
      <ul>
        <li><a href="/user">Users</a></li>
      </ul>
      <?php endif; ?>
		</div>
		
		<div id="main">
			<?php if (hasFlash()): ?>
        <p style="color: green"><?php echo getFlash(); ?></p>
      <?php endif; ?>
      <?php echo $yield; ?>
    </div>
  </div>
</body>

</html>