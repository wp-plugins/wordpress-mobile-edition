<html>
<head>
<title><?php wp_title('|'); ?></title>
<meta name="HandheldFriendly" value="true" />
<style><!-- @import url(<?php print(get_stylesheet_uri()); ?>); // --></style>
</head>

<body>

<h3><?php bloginfo('name'); ?></h3>

<hr />

<p>
	<a href="<?php bloginfo('home'); ?>">Home</a> |
	<a href="#recent">Recent Posts</a> |
	<a href="#pages">Pages</a>
</p>

<hr />

<?php

$parent = 0;
$i = 0;

have_posts();

if (have_posts()) : 
	while (have_posts()) : 
		the_post();
		if (is_single() || $wp_query->is_single || $wp_query->is_singular) {
			if (!is_page()) {
?>
<p>&laquo; <?php next_post('%', 'Next: ', 'yes'); ?> | <?php previous_post('%', 'Previous: ', 'yes'); ?> &raquo;</p>				
<?php
			}
?>

<h1><?php the_title(); ?></h1>

<p><?php _e('Posted in: '); the_category(', '); ?></p>

<?php 
			the_content();
			if ($wp_query->is_page && !is_home()) {
				$parent = $post->ID;
?>

<p>Posted by <?php the_author(); ?><br /><?php the_time('F jS, Y @ g:i A') ?></p>

<?php
			}
			comments_template();
			if (!is_page()) {
?>
<p>&laquo; <?php next_post('%', 'Next: ', 'yes'); ?> | <?php previous_post('%', 'Previous: ', 'yes'); ?> &raquo;</p>				
<?php
			}
		}
		else {
			if ($i == 0) {
				if (is_category()) {
?>
	<h1><?php _e('Posts in: '); ?><?php single_cat_title(''); ?></h1>
<?php
				}
				else if (is_day()) {
?>
	<h1><?php _e('Posts on: '); ?><?php the_time('l, F jS, Y'); ?></h1>
<?php
				}
				else if (is_month()) {
?>
	<h1><?php _e('Posts in: '); ?><?php the_time('F, Y'); ?></h1>
<?php
				}
				else if (is_year()) {
?>
	<h1><?php _e('Posts in: '); ?><?php the_time('Y'); ?></h1>
<?php
				}
				else if (is_search()) {
?>
	<h1><?php _e('Results for: '); ?><?php print(htmlspecialchars($s)); ?></h1>
<?php
				}
				print('<ul>');
			}
			$i++;
?>
	<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> <?php the_time("Y-m-d"); ?></li>
<?php
			if ($i == count($posts)) {
				print('</ul>');
			}
		}
	endwhile; 
endif;
?>

<hr />

<h2 id="recent"><?php _e('Recent Posts'); ?></h2>

<?php ak_recent_posts(10); ?>

<hr />

<h2 id="pages"><?php _e('Pages'); ?></h2>

<ul>
<?php
wp_list_pages('title_li=&depth=1&child_of='.$parent);
?>
</ul>

<hr />

<p>
	<a href="<?php bloginfo('home'); ?>">Home</a> |
	<a href="#recent">Recent Posts</a> |
	<a href="#pages">Pages</a>
</p>

<hr />

<p><a href="<?php bloginfo('wpurl'); ?>/wp-admin/options-general.php?ak_action=reject_mobile">Exit the Mobile Edition</a> (view the standard browser version).</p>

<hr />

<p>Powered by <a href="http://wordpress.org"><strong>WordPress</strong></a>. <a href="http://alexking.org/projects/wordpress">WordPress Mobile Edition</a> available at alexking.org.</p>

</body>
</html>