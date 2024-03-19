<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php bloginfo('name'); ?> | <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		
		<header>
			<h1>
				<a href="<?php echo site_url(); ?>"><img src="<?php bloginfo('template_url') ?>/images/logo-light.png" alt="" width="160" height="40" /></a>
			</h1>
			<div class="search_top">
				<form method="GET" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">  
				    <input type="text" autocomplete="off" class="search-field" placeholder="Search" value="" name="s" title="Search for:" required>
				    <input type="submit" class="search-submit" value="Search">
				</form>
			</div>
		</header>