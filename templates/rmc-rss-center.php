<h1><?php _e('RSS Center','rmcommon'); ?></h1>
<p>
<?php echo sprintf(__('All %s RSS feeds are listed below. Click an option for feed.','rmcommon'), '<strong>'.$xoopsConfig['sitename'].'</strong>'); ?>
</p>
<hr noshade="noshade" />
<br />
<?php foreach($feeds as $feed): ?>
<h2 class="feed_name"><?php echo $feed['data']['title']; ?></h2>
<p class="feed_url"><?php _e('URL:','rmcommon'); ?> <a href="<?php echo $feed['data']['url']; ?>"><?php echo $feed['data']['url']; ?></a></p>

	<!-- Feed options -->
	<ul class="feed_options">
	<?php foreach($feed['options'] as $option): ?>
		<li class="feed_option">
			<?php if(isset($option['params'])): ?>
			<a class="name" href="<?php echo XOOPS_URL; ?>/backend.php?action=showfeed&amp;mod=<?php echo $feed['data']['module']; ?>&amp;<?php echo $option['params']; ?>"><?php echo $option['title']; ?></a>
			<?php else: ?>
			<span class="name"><?php echo $option['title']; ?></span>
			<?php endif; ?>
			<span class="description"><?php echo $option['description']; ?></span>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endforeach; ?>