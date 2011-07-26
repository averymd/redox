<h2>Purpose</h2>
<p>GameOn is a site for classifying video games created by <a href="http://ludusnovus.net">Gregory Weir</a>. Each game is put into multiple categories, based on a number of factors. The end goal is to form a useful ontology for video games that can be used for comparison and analysis. It is intended to abandon the standard "genre" method of game classification for something more useful to players and to the future of game design. GameOn is inspired by the <a href="http://www.pandora.com/mgp.shtml">Music Genome Project</a> and was suggested by <a href="http://irrsinn.net">Lissa Avery</a>. </p>

<h2>Future Work</h2>
<p>Once the collection has been more fleshed out, I intend to write some code so that someone who enjoyed a certain game can find other games that were similarly classified. </p>

<h2>Where to Start</h2>
<a href="/game">List of all games</a><br />
<a href="/category">List of all categories</a>

<h3>Main categories</h3>
<ul>
	<?php foreach ($categories as $category): ?>
		<li><a href="/categories/<?php echo $category->ID; ?>"<?php echo $category->name; ?></a></li>
	<?php endforeach; ?>
</ul>