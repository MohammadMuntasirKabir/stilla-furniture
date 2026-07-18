<?php
/**
 * Header with cart drawer + account link.
 */
if ( session_status() === PHP_SESSION_NONE && ! headers_sent() ) { @session_start(); }
$cart_count = function_exists( 'stilla_cart_count' ) ? stilla_cart_count() : 0;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="nav" id="siteNav">
	<div class="nav__bar">
		<a class="nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Stilla home">STILLA<span>.</span></a>

		<nav class="nav__menu" aria-label="Primary">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__links',
				'fallback_cb'    => false,
				'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
			) );
			?>
		</nav>

		<div class="nav__tools">
			<button class="nav__icon" id="searchToggle" aria-label="Search" aria-expanded="false">
				<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
			</button>
			<a class="nav__icon nav__acct" href="<?php echo esc_url( is_user_logged_in() ? home_url( '/account/' ) : home_url( '/login/' ) ); ?>" aria-label="Account">
				<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
			</a>
			<button class="nav__cart" id="cartToggle" aria-label="Cart">
				<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M6 6L5 3H2"/></svg>
				<span class="nav__cart-count" id="cartCount"><?php echo (int) $cart_count; ?></span>
			</button>
			<button class="nav__lang" id="langToggle" aria-label="Language">EN</button>
			<button class="nav__burger" id="navBurger" aria-label="Menu" aria-expanded="false">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>

	<div class="nav__search" id="navSearch" hidden>
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav__searchform">
			<input type="search" name="s" placeholder="Search products…" aria-label="Search" autocomplete="off">
			<button type="submit">Search</button>
		</form>
	</div>
</header>

<!-- CART DRAWER -->
<div class="drawer" id="cartDrawer" hidden>
	<div class="drawer__backdrop" data-close-cart></div>
	<aside class="drawer__panel" role="dialog" aria-label="Cart">
		<div class="drawer__head">
			<h3>Your cart <span id="drawerCount">(<?php echo (int) $cart_count; ?>)</span></h3>
			<button class="drawer__close" data-close-cart aria-label="Close">×</button>
		</div>
		<div class="drawer__items" id="drawerItems"></div>
		<div class="drawer__foot">
			<div class="drawer__total"><span>Subtotal</span><strong id="drawerTotal">$<?php echo number_format( function_exists( 'stilla_cart_total' ) ? stilla_cart_total() : 0 ); ?></strong></div>
			<a class="btn-solid" href="<?php echo esc_url( home_url( '/cart/' ) ); ?>" id="drawerView">View cart</a>
			<button class="drawer__checkout" id="drawerCheckout">Checkout</button>
		</div>
	</aside>
</div>
