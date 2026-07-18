<?php
/**
 * Customer account dashboard (logged-in only).
 */
get_header();
$logged_in = is_user_logged_in();
$user = wp_get_current_user();
if ( ! $logged_in ) {
	wp_redirect( home_url( '/login/' ) ); exit;
}
?>
<main id="content" class="account">
	<?php if ( ! empty( $_GET['paid'] ) ) : ?>
		<div class="order-banner">✓ Payment successful — thank you! Your order has been recorded.</div>
	<?php endif; ?>

	<header class="account__head">
		<div>
			<p class="account__eyebrow">My account</p>
			<h1><?php echo esc_html( $user->first_name ? $user->first_name : explode( '@', $user->user_email )[0] ); ?></h1>
			<p class="account__hi">Signed in as <strong><?php echo esc_html( $user->user_email ); ?></strong></p>
		</div>
		<a class="btn-auth btn-auth--ghost" href="?action=logout">Log out</a>
	</header>

	<section class="account__orders">
		<div class="account__orders-head">
			<h2>Order history</h2>
			<a class="auth-link" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Continue shopping →</a>
		</div>
		<?php
		$orders = get_posts( array( 'post_type' => 'stilla_order', 'author' => $user->ID, 'posts_per_page' => 50, 'post_status' => 'private' ) );
		if ( $orders ) : ?>
			<ul class="orderlist">
				<?php foreach ( $orders as $o ) :
					$amount = (float) ( get_post_meta( $o->ID, 'amount', true ) ?: get_post_meta( $o->ID, 'total', true ) ?: 0 );
					$paid   = (int) get_post_meta( $o->ID, 'paid', true );
					$ref    = get_post_meta( $o->ID, 'order_ref', true );
				?>
				<li>
					<div>
						<strong><?php echo esc_html( $o->post_title ); ?></strong>
						<?php if ( $ref ) : ?><span class="orderlist__ref"><?php echo esc_html( $ref ); ?></span><?php endif; ?>
						<span class="orderlist__date"><?php echo esc_html( get_the_date( 'j M Y', $o ) ); ?></span>
						<?php if ( $paid ) : ?><span class="orderlist__paid">Paid</span><?php else : ?><span class="orderlist__unpaid">Unpaid</span><?php endif; ?>
						<p><?php echo nl2br( esc_html( $o->post_content ) ); ?></p>
					</div>
					<span class="orderlist__total">$<?php echo number_format( $amount, 2 ); ?></span>
				</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<div class="account__empty">
				<p>No orders yet.</p>
				<a class="btn-auth" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Start shopping</a>
			</div>
		<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
