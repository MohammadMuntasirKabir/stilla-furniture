<?php
/**
 * Generic page template — branded hero + content.
 */
get_header();
while ( have_posts() ) : the_post();
	$title = get_the_title();
	$excerpt = has_excerpt() ? get_the_excerpt() : '';
	$thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	?>
	<main id="content" class="page">
		<section class="page__hero reveal">
			<div class="page__hero-inner">
				<p class="page__eyebrow">Stilla</p>
				<h1 class="page__title"><?php echo esc_html( $title ); ?></h1>
				<?php if ( $excerpt ) : ?><p class="page__lead"><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
			</div>
			<?php if ( $thumb ) : ?>
				<img class="page__hero-img" src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
			<?php endif; ?>
		</section>

		<section class="page__body reveal">
			<div class="page__content">
				<?php the_content(); ?>
			</div>
		</section>

		<section class="page__cta reveal">
			<a class="btn-solid" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Explore the collection →</a>
			<a class="link-underline" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to home</a>
		</section>
	</main>
<?php
endwhile;
get_footer();
