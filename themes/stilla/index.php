<?php
get_header();
?>
<main style="max-width:980px;margin:80px auto;padding:0 22px;text-align:center">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
			the_title( '<h1>', '</h1>' );
			the_content();
		endwhile;
	endif;
	?>
</main>
<?php get_footer(); ?>
