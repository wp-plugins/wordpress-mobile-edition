<?php

// WordPress Mobile Edition
// version 1.7, 2005-01-12
//
// Copyright (c) 2002-2005 Alex King
// http://www.alexking.org/software/wordpress/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

ini_set ('display_errors', '0');
ini_set ('error_reporting', E_PARSE);

$now = date("Y-m-d H:i:s");

function next_post_m($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
	global $tableposts, $p, $posts, $id, $post, $siteurl, $blogfilename, $wpdb;
	global $time_difference, $single;
	global $querystring_start, $querystring_equal, $querystring_separator;
	if(($p) || ($posts==1) || 1 == $single) {

		$current_post_date = $post->post_date;
		$current_category = $post->post_category;

		$sqlcat = '';
		if ($in_same_cat != 'no') {
			$sqlcat = " AND post_category='$current_category' ";
		}

		$sql_exclude_cats = '';
		if (!empty($excluded_categories)) {
			$blah = explode('and', $excluded_categories);
			foreach($blah as $category) {
				$category = intval($category);
				$sql_exclude_cats .= " AND post_category != $category";
			}
		}

		$now = date('Y-m-d H:i:s');

		$limitnext--;

		$nextpost = @$wpdb->get_row("SELECT ID,post_title "
		                           ."FROM $tableposts WHERE "
		                           ."post_date > '$current_post_date' "
		                           ."AND post_date < '$now' "
		                           ."AND post_status = 'publish' "
		                           .$sqlcat.' '
		                           .$sql_exclude_cats.' '
		                           ."ORDER BY post_date ASC "
		                           ."LIMIT $limitnext,1"
		                           );
		if ($nextpost) {
			$string = '<a href="'.$PHPSELF.'?p='.$nextpost->ID.'&more=1">'.$next;
			if ($title=='yes') {
				$string .= wptexturize(stripslashes($nextpost->post_title));
			}
			$string .= '</a>';
			$format = str_replace('%', $string, $format);
			echo $format;
		}
	}
}

function previous_post_m($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {
	global $tableposts, $id, $post, $siteurl, $blogfilename, $wpdb;
	global $p, $posts, $posts_per_page, $s, $single;
	global $querystring_start, $querystring_equal, $querystring_separator;

	if (($p) || ($posts_per_page == 1) || 1 == $single) {

		$current_post_date = $post->post_date;
		$current_category = $post->post_category;

		$sqlcat = '';
		if ($in_same_cat != 'no') {
			$sqlcat = " AND post_category = '$current_category' ";
		}

		$sql_exclude_cats = '';
		if (!empty($excluded_categories)) {
			$blah = explode('and', $excluded_categories);
			foreach($blah as $category) {
				$category = intval($category);
				$sql_exclude_cats .= " AND post_category != $category";
			}
		}

		$limitprev--;
		$lastpost = @$wpdb->get_row("SELECT ID, post_title "
		                           ."FROM $tableposts "
		                           ."WHERE post_date < '$current_post_date' "
		                           ."AND post_status = 'publish' "
		                           .$sqlcat.' '
		                           .$sql_exclude_cats.' '
		                           ."ORDER BY post_date DESC "
		                           ."LIMIT $limitprev, 1"
		                           );
		if ($lastpost) {
			$string = '<a href="'.$PHPSELF.'?p='.$lastpost->ID.'&more=1">'.$previous;
			if ($title == 'yes') {
                $string .= wptexturize(stripslashes($lastpost->post_title));
            }
			$string .= '</a>';
			$format = str_replace('%', $string, $format);
			echo $format;
		}
	}
}

require('./wp-blog-header.php');

if (!isset($_REQUEST["p"])) {
	$latest = 1;
}
else {
	$latest = 0;
}

$main_blogfilename = get_settings('blogfilename');
if (strrpos($HTTP_SERVER_VARS["PHP_SELF"], "/") != false) {
	$blogfilename = substr($HTTP_SERVER_VARS["PHP_SELF"], strrpos($HTTP_SERVER_VARS["PHP_SELF"], "/") + 1);
}
else {
	$blogfilename = $HTTP_SERVER_VARS["PHP_SELF"];
}
?>
<html>
<head>
<title><?php bloginfo('name'); ?> mobile edition</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="HandheldFriendly" value="true" />
<style><!-- @import url(wp-mobile.css); // --></style>
</head>

<body>

<h1><?php bloginfo('name'); ?></h1>

<hr />

<?php
// show archive view?
if (isset($_REQUEST["view"])) {
	switch ($_REQUEST["view"]) {
		case "archives":
?>
<p>
<a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>">Latest Post</a> |
<a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>#last_10">Last 10 Posts</a> |
Archives 
</p>

<hr />

<h2>Archives by Month</h2>

<ul>
<?php
// borrowed from b2archives.php
			$arc_sql="SELECT DISTINCT YEAR(post_date), MONTH(post_date) FROM $tableposts "
			        ."WHERE post_date < '$now' "
			        ."AND post_status = 'publish' "
			        ."ORDER BY post_date DESC";
			$querycount++;
			$arc_result=mysql_query($arc_sql) or die($arc_sql.'<br />'.mysql_error());
			while($arc_row = mysql_fetch_array($arc_result)) {
				$arc_year  = $arc_row['YEAR(post_date)'];
				$arc_month = $arc_row['MONTH(post_date)'];
				echo '<li><a href="'.$HTTP_SERVER_VARS["PHP_SELF"].'?view=month&y='.$arc_year.'&m='.$arc_month.'">';
				echo $month[zeroise($arc_month,2)].' '.$arc_year;
				echo '</a></li>'."\n";
			}
?>
</ul>
<?php
			break;
		case "month":
			$selected_month = mktime(0, 0, 0, $_REQUEST["m"], 1, $_REQUEST["y"]);
?>
<p>
<a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>">Latest Post</a> |
<a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>#last_10">Last 10 Posts</a> |
<a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>?view=archives">Archives</a> 
</p>

<hr />

<h2><?php echo date("F Y", $selected_month); ?> Posts</h2>
<ul>
<?php
			$month = mysql_query("SELECT ID, post_title, post_date "
			                    ."FROM $tableposts "
			                    ."WHERE MONTH(post_date) = '".$_REQUEST["m"]."' "
			                    ."AND YEAR(post_date) = '".$_REQUEST["y"]."' "
			                    ."AND post_status = 'publish' "
			                    ."ORDER BY post_date DESC"
			                    );
			while ($post = mysql_fetch_array($month)) {
				echo '<li><a href="'.$HTTP_SERVER_VARS["PHP_SELF"].'?p='.$post["ID"].'&more=1">'
                    .stripslashes($post["post_title"])
                    .'</a> ('.substr($post["post_date"],5,5).")\n";
			}
?>
</ul>
<?php
			break;
	}
}
else {
?>
<p>
<?php
	if ($latest == 1) {
?>
Latest Post |
<?php
	}
	else {
?>
<a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>">Latest Post</a> |
<?php
	}
?>
<a href="#last_10">Last 10 Posts</a> |
<a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>?view=archives">Archives</a> 
</p>

<hr />

<?php /* // loop start */ ?>
<?php if ($posts) { $i = 0; foreach ($posts as $post) { if ($i < 1) { $i++; start_wp(); ?>

<p><?php previous_post_m('%','<b>Previous Post:</b> '); ?><br />
<?php next_post_m('%','<b>Next Post:</b> '); ?></p>

<h2><?php the_title(); ?></h2>

<p>Posted in:</p>

<ul>
<?php
$cats = get_the_category();
foreach ($cats as $dog) {
?>
	<li><?php print(htmlspecialchars($dog->cat_name)); ?></li>
<?php
}
?>
</ul>

<?php
$more = 1; // always show complete post;
the_content(); 
?>
<?php link_pages("<br />Pages: ","<br />","number") ?>
<p>
posted by <?php the_author() ?><br />
<?php the_date() ?> @  <?php the_time() ?>
</p>

<?php
// begin comments

if (empty($post->post_password) || 
    $HTTP_COOKIE_VARS['wp-postpass'] == $post->post_password) {  
    // and it doesn't match the cookie

	$comment_author = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "" : $HTTP_COOKIE_VARS["comment_author"];
	$comment_author_email = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "" : trim($HTTP_COOKIE_VARS["comment_author_email"]);
	$comment_author_url = (empty($HTTP_COOKIE_VARS["comment_author"])) ? "" : trim($HTTP_COOKIE_VARS["comment_author_url"]);
	
	$comments = $wpdb->get_results("SELECT * 
	                                FROM $tablecomments 
	                                WHERE comment_post_ID = '$id' 
	                                AND comment_approved = '1'
	                                ORDER BY comment_date"
	                              );
?>

<h3>Comments</h3>

<ol id="comments">
<?php 
// this line is WordPress' motor, do not delete it.
	if ($comments) {
		foreach ($comments as $comment) {
?>

<li id="comment-<?php comment_ID() ?>">
<?php comment_text() ?>
<p><cite><?php comment_type(); ?> by <?php comment_author_link() ?> <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite></p>
</li>

<?php /* end of the loop, don't delete */ 
		} 
	} 
	else { 
?>

<li>No comments on this post so far.</li>

<?php /* if you delete this the sky will fall on your head */ 
	} 
?>
</ol>

<h3>Add a comment:</h3>

<?php 
	if ('open' == $post->comment_status) { 
?>

<!-- form to add a comment -->

<form action="<?php echo get_settings('siteurl'); ?>/wp-comments.post.php" method="post">

	<p>
	Your Name:
	<br />
	<input type="text" name="author" value="<?php echo $comment_author ?>" size="20" />
	<br />
	Email:
	<br />
	<input type="text" name="email" value="<?php echo $comment_author_email ?>" size="20" />
	<br />
	Web Site:
	<br />
	<input type="text" name="url" value="<?php echo $comment_author_url ?>" size="20" />
	<br />
	Comments:
	<br />
	<textarea cols="40" rows="4" name="comment"></textarea>
	</p>

	<p>
	<input type="submit" name="submit" value="Post Comment" />
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($HTTP_SERVER_VARS["REQUEST_URI"]); ?>" />
	<input type="hidden" name="comment_autobr" value="1" />
	</p>

</form>
<?php 
	} 
	else { // comments are closed 
?>
<p>Sorry, comments are closed at this time.</p>
<?php 
	}
}
?>

<p><?php previous_post_m('%','<b>Previous Post:</b> '); ?><br />
<?php next_post_m('%','<b>Next Post:</b> '); ?></p>

<?php // if you delete this the sky will fall on your head
// this is just the end of the motor - don't touch that line either :)
		}
	}
}
?> 

<hr />

<h2>Last 10 posts:</h2>
<ul id="last_10">
<?php
$last_10 = mysql_query("SELECT ID, post_title, post_date "
                      ."FROM $tableposts "
                      ."WHERE post_status = 'publish' "
                      ."AND post_date < '$now' "
                      ."ORDER BY post_date DESC "
                      ."LIMIT 10"
                      );
while ($data = mysql_fetch_object($last_10)) {
	if ($p == $data->ID) {
		echo '	<li>'.$data->post_title.' ('.substr($data->post_date,5,5).")</li>\n";
	}
	else {
		echo '	<li><a href="'.$HTTP_SERVER_VARS["PHP_SELF"].'?p='.$data->ID.'&amp;more=1">'
            .stripslashes($data->post_title)
            .'</a> ('.substr($data->post_date,5,5).")</li>\n";
	}
}
?>
</ul>

<p><a href="<?php echo $HTTP_SERVER_VARS["PHP_SELF"]; ?>?view=archives">more Posts (Archives)</a></p>

<?php
// closes else from view if/switch
}
?>
<p><a href="http://www.alexking.org/software/wordpress/" target="_blank">WordPress Mobile Edition</a> available at alexking.org.</p>

<p>powered by <a href="http://wordpress.org" target="_blank"><b>WordPress</b></a>.</p>

</body>
</html>
