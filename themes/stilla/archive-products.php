<?php
/**
 * Catalog: startech-style product listing with sidebar filters.
 */
get_header();
$products = stilla_products();

// Collect distinct category families + types for the sidebar.
$families = array();
$types = array();
foreach ( $products as $p ) {
	$families[ $p['family'] ] = true;
	$types[ $p['type'] ]      = true;
}
$type_labels = array(
	'sofa'    => 'Sofas',
	'chair'   => 'Chairs',
	'stool'   => 'Stools & Pouffes',
	'table'   => 'Tables',
	'outdoor' => 'Outdoor',
);
$prices = array_column( array_map( fn( $p ) => $p['price'] ?? 0, $products ), null );
$min_p  = (int) floor( min( $prices ) / 50 ) * 50;
$max_p  = (int) ceil( max( $prices ) / 50 ) * 50;
?>
<main id="content" class="catalog">
	<header class="catalog__head">
		<p class="catalog__eyebrow">Catalogue</p>
		<h1>ALL PRODUCTS</h1>
		<p class="catalog__lead">Seating, tables and soft modules designed in Åhus, Sweden.</p>
	</header>

	<div class="catalog__layout">
		<!-- SIDEBAR FILTERS -->
		<aside class="filters" id="filters">
			<div class="filters__search">
				<input type="search" id="searchInput" placeholder="Search products…" aria-label="Search products">
			</div>

			<div class="filters__group">
				<h3>Category</h3>
				<ul class="checks">
					<?php foreach ( $type_labels as $t => $label ) : if ( ! isset( $types[ $t ] ) ) continue; ?>
					<li><label><input type="checkbox" class="f-type" value="<?php echo esc_attr( $t ); ?>"> <?php echo esc_html( $label ); ?></label></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="filters__group">
				<h3>Collection</h3>
				<ul class="checks">
					<?php foreach ( array_keys( $families ) as $fam ) : ?>
					<li><label><input type="checkbox" class="f-family" value="<?php echo esc_attr( $fam ); ?>"> <?php echo esc_html( $fam ); ?></label></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="filters__group">
				<h3>Price</h3>
				<div class="price">
					<div class="price__row"><span id="priceMinLbl">$<?php echo $min_p; ?></span><span id="priceMaxLbl">$<?php echo $max_p; ?></span></div>
					<input type="range" id="priceMin" min="<?php echo $min_p; ?>" max="<?php echo $max_p; ?>" value="<?php echo $min_p; ?>" step="50">
					<input type="range" id="priceMax" min="<?php echo $min_p; ?>" max="<?php echo $max_p; ?>" value="<?php echo $max_p; ?>" step="50">
				</div>
			</div>

			<div class="filters__group">
				<h3>Availability</h3>
				<ul class="checks">
					<li><label><input type="checkbox" id="inStockOnly"> In stock only</label></li>
				</ul>
			</div>

			<button class="filters__clear" id="clearFilters">Clear all</button>
		</aside>

		<!-- PRODUCT GRID -->
		<section class="catalog__main">
			<div class="catalog__bar">
				<span id="resultCount"><?php echo count( $products ); ?> products</span>
				<label class="sort">Sort
					<select id="sortSelect">
						<option value="default">Featured</option>
						<option value="price-asc">Price: Low to High</option>
						<option value="price-desc">Price: High to Low</option>
						<option value="name-asc">Name: A–Z</option>
					</select>
				</label>
			</div>

			<div class="catalog__grid" id="prodGrid">
				<?php foreach ( $products as $p ) : ?>
				<a class="pcard" href="<?php echo home_url( '/product/' . $p['slug'] . '/' ); ?>"
				   data-type="<?php echo esc_attr( $p['type'] ); ?>"
				   data-family="<?php echo esc_attr( $p['family'] ); ?>"
				   data-price="<?php echo esc_attr( $p['price'] ?? 0 ); ?>"
				   data-name="<?php echo esc_attr( strtolower( $p['name'] . ' ' . $p['family'] . ' ' . $p['code'] ) ); ?>"
				   data-stock="<?php echo esc_attr( $p['stock'] ?? 0 ); ?>">
					<div class="pcard__media">
						<img src="<?php echo stilla_img( $p['img'], 500 ); ?>" alt="<?php echo esc_attr( $p['name'] ); ?>" loading="lazy">
						<?php if ( ( $p['stock'] ?? 0 ) > 0 ) : ?><span class="pcard__stock">In stock</span><?php endif; ?>
						<button type="button" class="pcard__add" data-slug="<?php echo esc_attr( $p['slug'] ); ?>" aria-label="Add to cart">+ Cart</button>
					</div>
					<div class="pcard__body">
						<span class="pcard__code"><?php echo esc_html( $p['code'] ); ?> · <?php echo esc_html( $p['family'] ); ?></span>
						<h3><?php echo esc_html( $p['name'] ); ?></h3>
						<span class="pcard__price">$<?php echo number_format( $p['price'] ?? 0 ); ?></span>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
			<p class="catalog__empty" id="emptyMsg" hidden>No products match your filters.</p>
		</section>
	</div>
</main>
<?php get_footer(); ?>
