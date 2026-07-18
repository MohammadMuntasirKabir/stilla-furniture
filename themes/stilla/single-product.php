<?php
/**
 * Single product detail (single-product.php) — minimal, clean layout.
 */
get_header();
$slug = get_query_var( 'stilla_product' );
$p = stilla_get_product( $slug );
if ( ! $p ) {
	echo '<main id="content" style="max-width:800px;margin:120px auto;text-align:center"><h1>Product not found</h1><p><a class="link-underline" href="' . home_url( '/products/' ) . '">Back to all products →</a></p></main>';
	get_footer();
	return;
}
$products = stilla_products();
$related  = array_filter( $products, fn( $x ) => $x['family'] === $p['family'] && $x['slug'] !== $p['slug'] );
$related  = array_slice( array_values( $related ), 0, 4 );
$gallery  = $p['gallery'] ?? array( $p['img'] );
?>
<main id="content" class="product">

	<?php if ( ! empty( $_GET['paid'] ) ) :
		$ref = isset( $_GET['ref'] ) ? preg_replace( '/[^A-Za-z0-9\-]/', '', $_GET['ref'] ) : '';
	?>
	<div class="order-banner">✓ Payment successful — thank you!<?php if ( $ref ) : ?> Your order reference is <strong><?php echo esc_html( $ref ); ?></strong>.<?php endif; ?> A confirmation has been recorded.</div>
	<?php endif; ?>

	<nav class="product__crumb">
		<a href="<?php echo home_url( '/' ); ?>">Home</a> /
		<a href="<?php echo home_url( '/products/' ); ?>">Products</a> /
		<span><?php echo esc_html( $p['code'] . ' ' . $p['name'] ); ?></span>
	</nav>

	<section class="product__hero reveal">
		<!-- GALLERY -->
		<div class="product__gallery">
			<div class="product__stage">
				<img id="stageImg" src="<?php echo stilla_img( $gallery[0], 1000 ); ?>" alt="<?php echo esc_attr( $p['name'] ); ?>">
				<?php if ( ( $p['stock'] ?? 0 ) > 0 ) : ?><span class="product__badge">In stock</span><?php endif; ?>
			</div>
			<div class="product__thumbs">
				<?php foreach ( $gallery as $i => $g ) : ?>
				<button class="pthumb <?php echo $i === 0 ? 'is-active' : ''; ?>" data-full="<?php echo stilla_img( $g, 1000 ); ?>">
					<img src="<?php echo stilla_img( $g, 200 ); ?>" alt="thumb" loading="lazy">
				</button>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- INFO -->
		<div class="product__info">
			<p class="product__family"><?php echo esc_html( $p['family'] ); ?> FAMILY · <?php echo esc_html( $p['code'] ); ?></p>
			<h1 class="product__title"><?php echo esc_html( $p['name'] ); ?></h1>
			<p class="product__price">$<?php echo number_format( $p['price'] ?? 0 ); ?> <span class="product__price-note">incl. VAT</span></p>
			<p class="product__blurb"><?php echo esc_html( $p['blurb'] ); ?></p>

			<div class="product__buyrow">
				<div class="qty">
					<button type="button" class="qty__btn" id="qtyMinus" aria-label="Decrease">−</button>
					<input id="buyQty" class="qty__input" type="number" min="1" value="1" inputmode="numeric">
					<button type="button" class="qty__btn" id="qtyPlus" aria-label="Increase">+</button>
				</div>
				<button type="button" class="btn-cart" id="addToCart" data-slug="<?php echo esc_attr( $p['slug'] ); ?>">Add to cart</button>
			</div>
			<div class="product__buyrow">
				<button type="button" class="btn-buy" id="buyNow" data-slug="<?php echo esc_attr( $p['slug'] ); ?>">Buy now · $<?php echo number_format( $p['price'] ?? 0 ); ?></button>
				<button type="button" class="btn-quote" id="openQuote">Request a quote</button>
			</div>
			<span class="buy-status" id="buyStatus" role="status"></span>

			<details class="product__acc" open>
				<summary>Specifications</summary>
				<dl class="specs">
					<?php foreach ( $p['specs'] as $k => $v ) : ?>
					<div class="spec-row"><dt><?php echo esc_html( trim( $k ) ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
					<?php endforeach; ?>
					<div class="spec-row"><dt>Availability</dt><dd><?php echo ( ( $p['stock'] ?? 0 ) > 0 ) ? 'In stock (' . (int) $p['stock'] . ')' : 'Made to order'; ?></dd></div>
				</dl>
			</details>
			<details class="product__acc">
				<summary>Delivery &amp; care</summary>
				<p class="product__care">Made to order in Åhus, Sweden. Lead time 4–6 weeks. Removable slipcovers are machine washable; frames are repairable for a long service life.</p>
			</details>
			<a class="product__retailer" href="#">Find a retailer near you →</a>
		</div>
	</section>

	<!-- QUOTE MODAL -->
	<div class="modal" id="quoteModal" hidden>
		<div class="modal__backdrop" data-close></div>
		<div class="modal__box" role="dialog" aria-modal="true" aria-label="Request a quote">
			<button class="modal__close" data-close aria-label="Close">×</button>
			<h3>Request a quote</h3>
			<p class="modal__sub">For <strong><?php echo esc_html( $p['code'] . ' ' . $p['name'] ); ?></strong></p>
			<form id="quoteForm" class="qform">
				<?php wp_nonce_field( 'stilla_quote', 'q_nonce' ); ?>
				<input type="hidden" name="q_product" value="<?php echo esc_attr( $p['code'] . ' ' . $p['name'] ); ?>">
				<label>Name<input type="text" name="q_name" required></label>
				<label>Email<input type="email" name="q_email" required></label>
				<label>Message<textarea name="q_msg" rows="4" required placeholder="Tell us about your project, quantities, location…"></textarea></label>
				<button type="submit" class="btn-solid">Send request</button>
				<p class="qform__msg" id="quoteMsg" role="status"></p>
			</form>
		</div>
	</div>

	<?php if ( $related ) : ?>
	<section class="product__related">
		<h2>MORE FROM <?php echo esc_html( strtoupper( $p['family'] ) ); ?></h2>
		<div class="related__grid">
			<?php foreach ( $related as $r ) : ?>
			<a class="rcard" href="<?php echo home_url( '/product/' . $r['slug'] . '/' ); ?>">
				<div class="rcard__media"><img src="<?php echo stilla_img( $r['img'], 500 ); ?>" alt="<?php echo esc_attr( $r['name'] ); ?>" loading="lazy"></div>
				<span class="rcard__code"><?php echo esc_html( $r['code'] ); ?></span>
				<h3><?php echo esc_html( $r['name'] ); ?></h3>
			</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
