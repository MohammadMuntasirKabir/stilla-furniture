<?php
/**
 * Cart page (full view). The drawer in the header is the quick version;
 * this is the dedicated /cart/ page.
 */
get_header();
$cart   = stilla_cart();
$total  = stilla_cart_total();
$empty  = empty( $cart );
?>
<main id="content" class="cartpage">
	<header class="cartpage__head">
		<h1>YOUR CART</h1>
		<p><?php echo stilla_cart_count(); ?> item(s)</p>
	</header>

	<?php if ( $empty ) : ?>
		<p class="cartpage__empty">Your cart is empty. <a class="link-underline" href="<?php echo home_url( '/products/' ); ?>">Browse products →</a></p>
	<?php else : ?>
		<div class="cartpage__grid">
			<ul class="cartpage__items" id="cartItems">
				<?php foreach ( $cart as $it ) : ?>
				<li class="citem" data-slug="<?php echo esc_attr( $it['slug'] ); ?>">
					<a class="citem__media" href="<?php echo home_url( '/product/' . $it['slug'] . '/' ); ?>">
						<img src="<?php echo stilla_img( stilla_get_product( $it['slug'] )['img'] ?? 'sofa_a', 300 ); ?>" alt="<?php echo esc_attr( $it['name'] ); ?>">
					</a>
					<div class="citem__body">
						<a class="citem__name" href="<?php echo home_url( '/product/' . $it['slug'] . '/' ); ?>"><?php echo esc_html( $it['name'] ); ?></a>
						<span class="citem__price">$<?php echo number_format( $it['price'] ); ?></span>
						<div class="citem__qty">
							<button class="qminus" aria-label="Decrease">−</button>
							<input type="number" min="1" value="<?php echo (int) $it['qty']; ?>" class="qinput">
							<button class="qplus" aria-label="Increase">+</button>
						</div>
					</div>
					<div class="citem__right">
						<span class="citem__line">$<?php echo number_format( $it['price'] * $it['qty'] ); ?></span>
						<button class="citem__remove" aria-label="Remove">Remove</button>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>

			<aside class="cartpage__summary">
				<h2>Summary</h2>
				<div class="summary__row"><span>Subtotal</span><span id="cartTotal">$<?php echo number_format( $total ); ?></span></div>
				<div class="summary__row summary__muted"><span>Shipping</span><span>Calculated at checkout</span></div>
				<button class="btn-solid cartpage__checkout" id="cartCheckout">Checkout with Stripe</button>
				<p class="cartpage__note">You'll be redirected to secure Stripe Checkout. Test card: 4242 4242 4242 4242.</p>
			</aside>
		</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
