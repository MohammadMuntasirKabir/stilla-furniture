<?php
/**
 * Fallback template (search / archive) — uses the branded page layout for consistency.
 */
get_header();
?>
<main id="content" class="page">
	<section class="page__hero reveal">
		<div class="page__hero-inner">
			<p class="page__eyebrow">Stilla</p>
			<h1 class="page__title"><?php echo esc_html( get_the_title() ?: 'Search' ); ?></h1>
		</div>
	</section>

	<section class="page__body reveal">
		<div class="page__content">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) : the_post();
					the_title( '<h2><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
					the_excerpt();
				endwhile;
				the_posts_pagination();
			else :
				echo '<p>No results found.</p>';
			endif;
			?>
		</div>
	</section>

	<section class="page__cta reveal">
		<a class="btn-solid" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Explore the collection →</a>
		<a class="link-underline" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to home</a>
	</section>
</main>
<?php get_footer(); ?>
