<?php
/**
 * Stilla theme functions.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ---------- ASSETS ---------- */
add_action( 'wp_enqueue_scripts', function () {
	$theme = wp_get_theme();
	$css   = get_stylesheet_directory() . '/assets/css/main.css';
	$js    = get_stylesheet_directory() . '/assets/js/main.js';
	$cver  = file_exists( $css ) ? filemtime( $css ) : $theme->get( 'Version' );
	$jver  = file_exists( $js ) ? filemtime( $js ) : $theme->get( 'Version' );
	wp_enqueue_style( 'stilla-main', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), $cver );
	wp_enqueue_script( 'stilla-main-js', get_stylesheet_directory_uri() . '/assets/js/main.js', array(), $jver, array( 'strategy' => 'defer' ) );
	wp_localize_script( 'stilla-main-js', 'stillaAjax', array(
		'url' => admin_url( 'admin-ajax.php' ),
		'stripePk' => defined( 'STILLA_STRIPE_PK' ) ? STILLA_STRIPE_PK : '',
	) );
} );

add_action( 'init', function () {
	register_nav_menus( array( 'primary' => __( 'Primary Menu', 'stilla' ) ) );
} );

add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

/* ---------- SEO ---------- */
// Resolve the SEO context for the current request (title, description, image, type, canonical).
function stilla_seo_context() {
	$home = home_url( '/' );
	$ctx = array(
		'title'       => '',
		'description' => 'Stilla designs contract-grade Scandinavian furniture — sofas, chairs, tables and outdoor pieces made in Åhus, Sweden. Shop the full catalogue with secure Stripe checkout.',
		'image'       => stilla_img( 'hero', 1200 ),
		'type'        => 'website',
		'canonical'   => $home,
		'og_title'    => 'Stilla — Scandinavian Furniture',
	);
	$ctx['canonical'] = ( is_ssl() ? 'https://' : 'http://' ) . ( $_SERVER['HTTP_HOST'] ?? 'localhost' ) . ( $_SERVER['REQUEST_URI'] ?? '/' );

	if ( get_query_var( 'stilla_product' ) ) {
		$p = stilla_get_product( get_query_var( 'stilla_product' ) );
		if ( $p ) {
			$ctx['title']       = $p['code'] . ' ' . $p['name'];
			$ctx['description'] = wp_trim_words( $p['blurb'], 28, '…' );
			$ctx['image']       = stilla_img( $p['img'], 1200 );
			$ctx['type']        = 'product';
			$ctx['og_title']    = $p['code'] . ' ' . $p['name'];
		}
	} elseif ( get_query_var( 'stilla_products' ) ) {
		$ctx['title']    = 'All Products';
		$ctx['og_title'] = 'Shop Stilla Furniture';
	} elseif ( get_query_var( 'stilla_auth' ) ) {
		$which = get_query_var( 'stilla_auth' );
		$ctx['title']    = ( $which === 'register' ? 'Create account' : 'Sign in' );
		$ctx['og_title'] = $ctx['title'];
	} elseif ( is_front_page() ) {
		$ctx['title']    = 'Stilla — Scandinavian Furniture, Made to Last';
		$ctx['og_title'] = 'Stilla — Scandinavian Furniture';
	}
	return $ctx;
}

// Document <title> (uses title-tag support).
add_filter( 'document_title_parts', function ( $parts ) {
	$ctx = stilla_seo_context();
	if ( $ctx['title'] ) { $parts['title'] = $ctx['title']; }
	return $parts;
} );

// Output meta description, canonical, Open Graph + Twitter cards, and JSON-LD.
add_action( 'wp_head', function () {
	$ctx = stilla_seo_context();
	$url = $ctx['canonical'];
	echo "\n<!-- SEO -->\n";
	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	echo '<meta name="description" content="' . esc_attr( $ctx['description'] ) . '">' . "\n";
	echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
	// Open Graph
	echo '<meta property="og:type" content="' . esc_attr( $ctx['type'] ) . '">' . "\n";
	echo '<meta property="og:site_name" content="Stilla">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $ctx['og_title'] ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $ctx['description'] ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $ctx['image'] ) . '">' . "\n";
	echo '<meta property="og:locale" content="en_US">' . "\n";
	// Twitter
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $ctx['og_title'] ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $ctx['description'] ) . '">' . "\n";
	echo '<meta name="twitter:image" content="' . esc_url( $ctx['image'] ) . '">' . "\n";

	// JSON-LD: Organization (site-wide) + Product (product pages) + BreadcrumbList.
	$ld = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => 'Stilla',
		'url'      => home_url( '/' ),
		'logo'     => stilla_img( 'hero', 400 ),
		'description' => 'Scandinavian furniture designed in Åhus, Sweden.',
		'sameAs'   => array( 'https://www.instagram.com/', 'https://www.linkedin.com/' ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";

	if ( get_query_var( 'stilla_product' ) ) {
		$p = stilla_get_product( get_query_var( 'stilla_product' ) );
		if ( $p ) {
			$prod = array(
				'@context' => 'https://schema.org',
				'@type'    => 'Product',
				'name'     => $p['code'] . ' ' . $p['name'],
				'description' => $p['blurb'],
				'brand'    => array( '@type' => 'Brand', 'name' => 'Stilla' ),
				'image'    => stilla_img( $p['img'], 1200 ),
				'offers'   => array(
					'@type'         => 'Offer',
					'priceCurrency' => 'USD',
					'price'         => $p['price'] ?? 0,
					'availability'  => ( ( $p['stock'] ?? 0 ) > 0 ) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
					'url'           => home_url( '/product/' . $p['slug'] . '/' ),
				),
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $prod, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
		}
		// Breadcrumb
		$bc = array(
			'@context' => 'https://schema.org',
			'@type'    => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => home_url( '/products/' ) ),
				array( '@type' => 'ListItem', 'position' => 3, 'name' => $p['code'] . ' ' . $p['name'], 'item' => home_url( '/product/' . $p['slug'] . '/' ) ),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $bc, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
	echo "<!-- /SEO -->\n";
}, 1 );

// XML sitemap at /sitemap.xml
add_action( 'init', function () {
	add_rewrite_rule( '^sitemap\.xml$', 'index.php?stilla_sitemap=1', 'top' );
} );
add_filter( 'query_vars', function ( $vars ) { $vars[] = 'stilla_sitemap'; return $vars; } );
// Don't let WP add a trailing slash to /sitemap.xml (redirect_canonical 301).
add_filter( 'redirect_canonical', function ( $redirect ) {
	if ( get_query_var( 'stilla_sitemap' ) ) return false;
	return $redirect;
} );
add_action( 'template_redirect', function () {
	if ( ! get_query_var( 'stilla_sitemap' ) ) return;
	header( 'Content-Type: application/xml; charset=utf-8' );
	$urls = array( home_url( '/' ), home_url( '/products/' ) );
	foreach ( stilla_products() as $p ) { $urls[] = home_url( '/product/' . $p['slug'] . '/' ); }
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	foreach ( $urls as $u ) {
		echo "\t<url><loc>" . esc_url( $u ) . "</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>\n";
	}
	echo '</urlset>';
	exit;
} );

// robots.txt — allow indexing, point at the sitemap.
add_action( 'do_robotstxt', function () {
	echo "User-agent: *\n";
	echo "Disallow: /wp-admin/\n";
	echo "Disallow: /wp-includes/\n";
	echo "Disallow: /cart/\n";
	echo "Disallow: /account/\n";
	echo "Disallow: /login/\n";
	echo "Disallow: /register/\n";
	echo "Sitemap: " . home_url( '/sitemap.xml' ) . "\n";
} );

/* ---------- IMAGE HELPER (real Unsplash photos) ---------- */
function stilla_images() {
	return array(
		'sofa_a'      => '1555041469-a586c61ea9bc',
		'sofa_b'      => '1493663284031-b7e3aefcae8e',
		'sofa_c'      => '1493809842364-78817add7ffb',
		'sofa_d'      => '1524758631624-e2822e304c36',
		'chair_a'     => '1503602642458-232111445657',
		'chair_b'     => '1532372320572-cda25653a26d',
		'chair_c'     => '1513506003901-1e6a229e2d15',
		'chair_d'     => '1540574163026-643ea20ade25',
		'chair_e'     => '1550226891-ef816aed4a98',
		'table_a'     => '1567538096630-e0c55bd6374c',
		'table_b'     => '1538688525198-9b88f6f53126',
		'table_c'     => '1586023492125-27b2c045efd7',
		'stool_a'     => '1567016432779-094069958ea5',
		'lamp_a'      => '1611967164521-abae8fba4668',
		'hero'        => '1524758631624-e2822e304c36',
		'interior_a'  => '1586023492125-27b2c045efd7',
		'interior_b'  => '1538688525198-9b88f6f53126',
		'interior_c'  => '1611967164521-abae8fba4668',
		// Outdoor
		'out_a'       => '1600607687939-ce8a6c25118c',
		'out_b'       => '1600566753086-00f18fb6b3ea',
		'out_c'       => '1540932239986-30128078f3c5',
		'out_d'       => '1505693416388-ac5ce068fe85',
		'out_e'       => '1517705008128-361805f42e86',
		'out_f'       => '1558618666-fcd25c85cd64',
		'out_g'       => '1531835551805-16d864c8d311',
		'out_h'       => '1618220179428-22790b461013',
		'out_i'       => '1604578762246-41134e37f9cc',
	);
}
function stilla_img( $key = 'hero', $w = 800 ) {
	$map = stilla_images();
	$id  = $map[ $key ] ?? $map['hero'];
	return 'https://images.unsplash.com/photo-' . $id . '?auto=format&fit=crop&w=' . $w . '&q=70';
}

/* ---------- PRODUCT DATA (single source of truth) ---------- */
function stilla_products() {
	$raw = array(
		// SOMA family
		array( 'slug' => 's42-soma',        'code' => 'S42',  'name' => 'Måne Sofa',          'family' => 'SOMA',        'type' => 'sofa',  'img' => 'sofa_a',
			'blurb' => 'A slipcover sofa that turns two frames and two foam moulds into almost endless configurations.',
			'specs' => array( 'Width' => '212 cm', 'Depth' => '92 cm', 'Height' => '74 cm', 'Seat height' => '42 cm', 'Frame' => 'Beech, lacquered', ' Cover' => 'Removable slipcover' ),
			'gallery' => array( 'sofa_a', 'interior_a' ) ),
		array( 'slug' => 's420-soma',       'code' => 'S420', 'name' => 'Liten Måne',         'family' => 'SOMA',        'type' => 'sofa',  'img' => 'sofa_b',
			'blurb' => 'The two-seat Måne — compact comfort with the same contract-level feel.',
			'specs' => array( 'Width' => '168 cm', 'Depth' => '92 cm', 'Height' => '74 cm', 'Seat height' => '42 cm', 'Frame' => 'Beech, lacquered', ' Cover' => 'Removable slipcover' ),
			'gallery' => array( 'sofa_b', 'interior_b' ) ),
		array( 'slug' => 's422-soma',       'code' => 'S422', 'name' => 'Måne Hörn',          'family' => 'SOMA',        'type' => 'sofa',  'img' => 'sofa_c',
			'blurb' => 'The Måne corner module for structuring lounges and collaborative spaces.',
			'specs' => array( 'Width' => '240 cm', 'Depth' => '160 cm', 'Height' => '74 cm', 'Seat height' => '42 cm', 'Frame' => 'Beech, lacquered', ' Cover' => 'Removable slipcover' ),
			'gallery' => array( 'sofa_c', 'interior_c' ) ),
		array( 'slug' => 'l428-soma-table', 'code' => 'L428', 'name' => 'Solsidan Table',     'family' => 'SOMA',        'type' => 'table', 'img' => 'table_a',
			'blurb' => 'A confident low table that completes the Måne system.',
			'specs' => array( 'Diameter' => '90 cm', 'Height' => '42 cm', 'Top' => 'Oak veneer', 'Frame' => 'Powder-coated steel' ),
			'gallery' => array( 'table_a', 'interior_a' ) ),
		array( 'slug' => 'l429-soma-table', 'code' => 'L429', 'name' => 'Stor Solsidan',      'family' => 'SOMA',        'type' => 'table', 'img' => 'table_b',
			'blurb' => 'The Solsidan coffee table in a larger format for generous living rooms.',
			'specs' => array( 'Diameter' => '110 cm', 'Height' => '38 cm', 'Top' => 'Oak veneer', 'Frame' => 'Powder-coated steel' ),
			'gallery' => array( 'table_b', 'interior_b' ) ),
		array( 'slug' => 's424-soma',       'code' => 'S424', 'name' => 'Pilpall Pouffe',     'family' => 'SOMA',        'type' => 'stool', 'img' => 'stool_a',
			'blurb' => 'A soft slipcover footstool — the playful finishing touch.',
			'specs' => array( 'Width' => '70 cm', 'Depth' => '70 cm', 'Height' => '42 cm', 'Frame' => 'Beech', ' Cover' => 'Removable slipcover' ),
			'gallery' => array( 'stool_a', 'interior_c' ) ),
		array( 'slug' => 's428-soma',       'code' => 'S428', 'name' => 'Läs Lounge Chair',   'family' => 'SOMA',        'type' => 'chair', 'img' => 'chair_a',
			'blurb' => 'An easy chair that carries the Måne language into the armchair.',
			'specs' => array( 'Width' => '78 cm', 'Depth' => '82 cm', 'Height' => '80 cm', 'Seat height' => '42 cm', 'Frame' => 'Beech' ),
			'gallery' => array( 'chair_a', 'interior_a' ) ),
		// FRANKFURTER family
		array( 'slug' => 'b525-frankfurter',       'code' => 'B525', 'name' => 'Frankfurt Chair',   'family' => 'FRANKFURTER', 'type' => 'chair', 'img' => 'chair_b',
			'blurb' => 'A chair that at first glance seems obvious — until you notice the focus on body, material and proportions.',
			'specs' => array( 'Width' => '54 cm', 'Depth' => '54 cm', 'Height' => '80 cm', 'Seat height' => '46 cm', 'Designer' => 'Johan Ansander', ' Frame' => 'Oak' ),
			'gallery' => array( 'chair_b', 'interior_b' ) ),
		array( 'slug' => 'b526-frankfurter-dressed', 'code' => 'B526', 'name' => 'Frankfurt Klädd',  'family' => 'FRANKFURTER', 'type' => 'chair', 'img' => 'chair_c',
			'blurb' => 'Frankfurt with an upholstered shell — comfort that doesn’t try to be more than it is.',
			'specs' => array( 'Width' => '56 cm', 'Depth' => '56 cm', 'Height' => '82 cm', 'Seat height' => '46 cm', 'Designer' => 'Johan Ansander', ' Shell' => 'Upholstered' ),
			'gallery' => array( 'chair_c', 'interior_c' ) ),
		array( 'slug' => 'l525-frankfurter-table', 'code' => 'L525', 'name' => 'Frankfurt Side',     'family' => 'FRANKFURTER', 'type' => 'table', 'img' => 'table_c',
			'blurb' => 'A slim table to pair with the Frankfurt chair.',
			'specs' => array( 'Diameter' => '70 cm', 'Height' => '46 cm', 'Top' => 'Laminate', 'Frame' => 'Steel' ),
			'gallery' => array( 'table_c', 'interior_a' ) ),
		array( 'slug' => 'o230-maximus', 'code' => 'O230', 'name' => 'Maximus Lounge', 'family' => 'FRANKFURTER', 'type' => 'chair', 'img' => 'chair_d',
			'blurb' => 'Maximus — maximum comfort, minimum fuss.',
			'specs' => array( 'Width' => '62 cm', 'Depth' => '60 cm', 'Height' => '84 cm', 'Seat height' => '44 cm', 'Designer' => 'Johan Ansander', ' Frame' => 'Oak' ),
			'gallery' => array( 'chair_d', 'interior_b' ) ),
		array( 'slug' => 'o235-max', 'code' => 'O235', 'name' => 'Max Everyday', 'family' => 'FRANKFURTER', 'type' => 'chair', 'img' => 'chair_e',
			'blurb' => 'Max — the everyday chair that earns its place.',
			'specs' => array( 'Width' => '50 cm', 'Depth' => '52 cm', 'Height' => '82 cm', 'Seat height' => '46 cm', 'Designer' => 'Johan Ansander', ' Frame' => 'Beech' ),
			'gallery' => array( 'chair_e', 'interior_c' ) ),
		array( 'slug' => 'b520-om', 'code' => 'B520', 'name' => 'Omkrets Chair', 'family' => 'FRANKFURTER', 'type' => 'chair', 'img' => 'chair_a',
			'blurb' => 'Omkrets — a circle of comfort in solid wood.',
			'specs' => array( 'Width' => '58 cm', 'Depth' => '56 cm', 'Height' => '80 cm', 'Seat height' => '46 cm', 'Designer' => 'Johan Ansander', ' Frame' => 'Oak' ),
			'gallery' => array( 'chair_a', 'interior_a' ) ),
		array( 'slug' => 'b521-om', 'code' => 'B521', 'name' => 'Omkrets Arms', 'family' => 'FRANKFURTER', 'type' => 'chair', 'img' => 'chair_b',
			'blurb' => 'Omkrets with armrests — quiet presence, serious proportions.',
			'specs' => array( 'Width' => '60 cm', 'Depth' => '56 cm', 'Height' => '82 cm', 'Seat height' => '46 cm', 'Designer' => 'Johan Ansander', ' Frame' => 'Oak' ),
			'gallery' => array( 'chair_b', 'interior_b' ) ),
		array( 'slug' => 'b522-om', 'code' => 'B522', 'name' => 'Omkrets Klädd', 'family' => 'FRANKFURTER', 'type' => 'chair', 'img' => 'chair_c',
			'blurb' => 'Omkrets upholstered — the homey feel of a contract-level chair.',
			'specs' => array( 'Width' => '58 cm', 'Depth' => '58 cm', 'Height' => '82 cm', 'Seat height' => '46 cm', 'Designer' => 'Johan Ansander', ' Shell' => 'Upholstered' ),
			'gallery' => array( 'chair_c', 'interior_c' ) ),
		// OUTDOOR family
		array( 'slug' => 'o100-terra-lounge', 'code' => 'O100', 'name' => 'Terra Lounge', 'family' => 'OUTDOOR', 'type' => 'outdoor', 'img' => 'out_a',
			'blurb' => 'An all-weather lounge module built for terraces and rooftops — powder-coated aluminium that shrugs off the rain.',
			'specs' => array( 'Width' => '190 cm', 'Depth' => '85 cm', 'Height' => '70 cm', 'Seat height' => '40 cm', 'Frame' => 'Powder-coated aluminium', ' Cushion' => 'Quick-dry foam' ),
			'gallery' => array( 'out_a', 'out_b' ) ),
		array( 'slug' => 'o110-terra-chair', 'code' => 'O110', 'name' => 'Terra Stack', 'family' => 'OUTDOOR', 'type' => 'outdoor', 'img' => 'out_b',
			'blurb' => 'A stackable outdoor chair with a teak seat and a frame that patinas beautifully.',
			'specs' => array( 'Width' => '56 cm', 'Depth' => '58 cm', 'Height' => '82 cm', 'Seat height' => '45 cm', 'Frame' => 'Teak', ' Stackable' => 'Yes' ),
			'gallery' => array( 'out_b', 'out_c' ) ),
		array( 'slug' => 'o120-terra-table', 'code' => 'O120', 'name' => 'Terra Long', 'family' => 'OUTDOOR', 'type' => 'outdoor', 'img' => 'out_c',
			'blurb' => 'A dining table for eight, made to live outside all year round.',
			'specs' => array( 'Diameter' => '140 cm', 'Height' => '74 cm', 'Top' => 'Ceramic', 'Frame' => 'Powder-coated steel' ),
			'gallery' => array( 'out_c', 'out_d' ) ),
	);
	// Assign a price (USD) + stock to each product in a stable, varied way.
	$base = array(
		'sofa'    => 1290, 'chair' => 440, 'stool' => 320, 'table' => 690, 'outdoor' => 980,
	);
	$stock_base = array( 'sofa' => 12, 'chair' => 30, 'stool' => 25, 'table' => 18, 'outdoor' => 14 );
	foreach ( $raw as $i => $p ) {
		$type = $p['type'];
		$price = ( $base[ $type ] ?? 500 ) + ( $i * 35 );           // deterministic spread
		$raw[ $i ]['price'] = $price;
		$raw[ $i ]['stock'] = ( $stock_base[ $type ] ?? 20 ) - ( $i % 5 ); // deterministic stock
	}
	return $raw;
}
function stilla_get_product( $slug ) {
	foreach ( stilla_products() as $p ) {
		if ( $p['slug'] === $slug ) return $p;
	}
	return null;
}

/* ---------- ROUTING: /products and /product/<slug> ---------- */
add_action( 'init', function () {
	add_rewrite_rule( '^products/?$', 'index.php?stilla_products=1', 'top' );
	add_rewrite_rule( '^product/([^/]+)/?$', 'index.php?stilla_product=$matches[1]', 'top' );
} );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'stilla_products';
	$vars[] = 'stilla_product';
	return $vars;
} );
add_filter( 'template_include', function ( $template ) {
	if ( get_query_var( 'stilla_products' ) ) return get_stylesheet_directory() . '/archive-products.php';
	if ( get_query_var( 'stilla_product' ) )  return get_stylesheet_directory() . '/single-product.php';
	return $template;
} );

/* ---------- AJAX QUOTE FORM ---------- */
add_action( 'wp_ajax_stilla_quote', 'stilla_handle_quote' );
add_action( 'wp_ajax_nopriv_stilla_quote', 'stilla_handle_quote' );
function stilla_handle_quote() {
	$name    = sanitize_text_field( wp_unslash( $_POST['q_name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['q_email'] ?? '' ) );
	$product = sanitize_text_field( wp_unslash( $_POST['q_product'] ?? '' ) );
	$msg     = sanitize_textarea_field( wp_unslash( $_POST['q_msg'] ?? '' ) );

	if ( ! $name || ! is_email( $email ) || ! $msg ) {
		wp_send_json_error( array( 'message' => 'Please fill in name, a valid email and a message.' ) );
	}
	// Store as a private inquiry post (no email/SMTP required). Swap for wp_mail() when SMTP is set up.
	$post_id = wp_insert_post( array(
		'post_type'    => 'stilla_inquiry',
		'post_title'   => 'Quote: ' . $product . ' — ' . $name,
		'post_content' => $msg,
		'post_status'  => 'private',
		'meta_input'   => array( 'email' => $email, 'product' => $product, 'name' => $name ),
	) );
	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => 'Could not save your request. Try again.' ) );
	}
	wp_send_json_success( array( 'message' => 'Thanks, ' . $name . ' — we received your request for ' . $product . ' and will be in touch.' ) );
}
// Register the inquiry post type (hidden from menus).
add_action( 'init', function () {
	register_post_type( 'stilla_inquiry', array(
		'label' => 'Quote inquiries',
		'public' => false,
		'show_ui' => true,
		'supports' => array( 'title', 'editor', 'custom-fields' ),
		'capability_type' => 'post',
	) );
} );

/* ---------- STRIPE CHECKOUT ---------- */
// Test keys — safe to commit to a public repo (publishable + test secret).
// Replace with live keys + switch the URL below to https://api.stripe.com when going live.
define( 'STILLA_STRIPE_PK', 'pk_test_51RZHgPQZxsample00000000000000000000000000000000000000000000000' );
define( 'STILLA_STRIPE_SK', 'sk_test_51RZHgPQZxsample00000000000000000000000000000000000000000000000' );
define( 'STILLA_STRIPE_API', 'https://api.stripe.com/v1' );

add_action( 'wp_ajax_stilla_checkout', 'stilla_handle_checkout' );
add_action( 'wp_ajax_nopriv_stilla_checkout', 'stilla_handle_checkout' );
function stilla_handle_checkout() {
	$slug  = sanitize_title( wp_unslash( $_POST['slug'] ?? '' ) );
	$qty   = max( 1, (int) ( $_POST['qty'] ?? 1 ) );
	$prod  = stilla_get_product( $slug );
	if ( ! $prod || empty( $prod['price'] ) ) {
		wp_send_json_error( array( 'message' => 'Product not found.' ) );
	}
	$success = home_url( '/product/' . $slug . '/?paid=1' );
	$cancel  = home_url( '/product/' . $slug . '/' );
	$name    = $prod['code'] . ' ' . $prod['name'];
	$desc    = wp_strip_all_tags( $prod['blurb'] );
	$unit    = (int) round( $prod['price'] * 100 ); // cents
	$amount  = $unit * $qty;

	// Create a Stripe Checkout Session via the REST API (PHP native, no SDK).
	$payload = http_build_query( array(
		'mode'                  => 'payment',
		'success_url'           => $success,
		'cancel_url'            => $cancel,
		'line_items[0][price_data][currency]'           => 'usd',
		'line_items[0][price_data][product_data][name]' => $name,
		'line_items[0][price_data][product_data][description]' => $desc,
		'line_items[0][price_data][unit_amount]'        => $unit,
		'line_items[0][quantity]'                        => $qty,
		'metadata[product]'      => $slug,
		'metadata[order_ref]'    => 'STL-' . time() . '-' . wp_rand( 100, 999 ),
	) );
	$ch = curl_init( STILLA_STRIPE_API . '/checkout/sessions' );
	curl_setopt_array( $ch, array(
		CURLOPT_POST           => true,
		CURLOPT_POSTFIELDS     => $payload,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER     => array(
			'Authorization: Bearer ' . STILLA_STRIPE_SK,
			'Content-Type: application/x-www-form-urlencoded',
		),
	) );
	$resp = curl_exec( $ch );
	$err  = curl_error( $ch );
	curl_close( $ch );
	if ( $err || ! $resp ) {
		wp_send_json_error( array( 'message' => 'Could not reach Stripe: ' . $err ) );
	}
	$data = json_decode( $resp, true );
	if ( empty( $data['id'] ) || empty( $data['url'] ) ) {
		wp_send_json_error( array( 'message' => 'Stripe error: ' . ( $data['error']['message'] ?? 'unknown' ) ) );
	}
	wp_send_json_success( array( 'url' => $data['url'], 'session' => $data['id'] ) );
}

// Record completed orders (stored privately; a webhook would update status to "paid").
add_action( 'init', function () {
	register_post_type( 'stilla_order', array(
		'label' => 'Stripe orders',
		'public' => false,
		'show_ui' => true,
		'supports' => array( 'title', 'editor', 'custom-fields' ),
		'capability_type' => 'post',
	) );
} );

/* ---------- CART (session-based) ---------- */
if ( session_status() === PHP_SESSION_NONE && ! headers_sent() ) { @session_start(); }
function stilla_cart() { return $_SESSION['stilla_cart'] ?? array(); }
function stilla_cart_count() { $n = 0; foreach ( stilla_cart() as $i ) { $n += (int) $i['qty']; } return $n; }
function stilla_cart_total() { $t = 0; foreach ( stilla_cart() as $i ) { $t += $i['price'] * $i['qty']; } return $t; }

add_action( 'wp_ajax_stilla_cart_add', 'stilla_cart_add' );
add_action( 'wp_ajax_nopriv_stilla_cart_add', 'stilla_cart_add' );
function stilla_cart_add() {
	$slug = sanitize_title( wp_unslash( $_POST['slug'] ?? '' ) );
	$qty  = max( 1, (int) ( $_POST['qty'] ?? 1 ) );
	$p    = stilla_get_product( $slug );
	if ( ! $p ) wp_send_json_error( array( 'message' => 'Product not found.' ) );
	$cart = stilla_cart();
	if ( isset( $cart[ $slug ] ) ) { $cart[ $slug ]['qty'] += $qty; }
	else { $cart[ $slug ] = array( 'slug' => $slug, 'name' => $p['code'] . ' ' . $p['name'], 'price' => (int) $p['price'], 'qty' => $qty ); }
	$_SESSION['stilla_cart'] = $cart;
	wp_send_json_success( array( 'count' => stilla_cart_count(), 'total' => stilla_cart_total() ) );
}

add_action( 'wp_ajax_stilla_cart_remove', 'stilla_cart_remove' );
add_action( 'wp_ajax_nopriv_stilla_cart_remove', 'stilla_cart_remove' );
function stilla_cart_remove() {
	$slug = sanitize_title( wp_unslash( $_POST['slug'] ?? '' ) );
	$cart = stilla_cart();
	unset( $cart[ $slug ] );
	$_SESSION['stilla_cart'] = $cart;
	wp_send_json_success( array( 'count' => stilla_cart_count(), 'total' => stilla_cart_total(), 'items' => array_values( $cart ) ) );
}

add_action( 'wp_ajax_stilla_cart_qty', 'stilla_cart_qty' );
add_action( 'wp_ajax_nopriv_stilla_cart_qty', 'stilla_cart_qty' );
function stilla_cart_qty() {
	$slug = sanitize_title( wp_unslash( $_POST['slug'] ?? '' ) );
	$qty  = max( 1, (int) ( $_POST['qty'] ?? 1 ) );
	$cart = stilla_cart();
	if ( isset( $cart[ $slug ] ) ) { $cart[ $slug ]['qty'] = $qty; $_SESSION['stilla_cart'] = $cart; }
	wp_send_json_success( array( 'count' => stilla_cart_count(), 'total' => stilla_cart_total(), 'items' => array_values( $cart ) ) );
}

// Return cart contents (used to render the drawer).
add_action( 'wp_ajax_stilla_cart_get', 'stilla_cart_get' );
add_action( 'wp_ajax_nopriv_stilla_cart_get', 'stilla_cart_get' );
function stilla_cart_get() { wp_send_json_success( array( 'items' => array_values( stilla_cart() ), 'count' => stilla_cart_count(), 'total' => stilla_cart_total() ) ); }

/* ---------- MULTI-ITEM STRIPE CHECKOUT ---------- */
add_action( 'wp_ajax_stilla_checkout_cart', 'stilla_checkout_cart' );
add_action( 'wp_ajax_nopriv_stilla_checkout_cart', 'stilla_checkout_cart' );
function stilla_checkout_cart() {
	$cart = stilla_cart();
	if ( empty( $cart ) ) wp_send_json_error( array( 'message' => 'Your cart is empty.' ) );
	$success = home_url( '/account/?paid=1' );
	$cancel  = home_url( '/cart/' );
	$line_items = array();
	$i = 0;
	foreach ( $cart as $item ) {
		$line_items[ "line_items[{$i}][price_data][currency]" ]           = 'usd';
		$line_items[ "line_items[{$i}][price_data][product_data][name]" ] = $item['name'];
		$line_items[ "line_items[{$i}][price_data][unit_amount]" ]        = (int) round( $item['price'] * 100 );
		$line_items[ "line_items[{$i}][quantity]" ]                        = (int) $item['qty'];
		$i++;
	}
	$line_items['mode'] = 'payment';
	$line_items['success_url'] = $success;
	$line_items['cancel_url']  = $cancel;
	$line_items['metadata[order_ref]'] = 'STL-' . time() . '-' . wp_rand( 100, 999 );
	$payload = http_build_query( $line_items );
	$ch = curl_init( STILLA_STRIPE_API . '/checkout/sessions' );
	curl_setopt_array( $ch, array(
		CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => array( 'Authorization: Bearer ' . STILLA_STRIPE_SK, 'Content-Type: application/x-www-form-urlencoded' ),
	) );
	$resp = curl_exec( $ch ); $err = curl_error( $ch ); curl_close( $ch );
	if ( $err || ! $resp ) wp_send_json_error( array( 'message' => 'Could not reach Stripe: ' . $err ) );
	$data = json_decode( $resp, true );
	if ( empty( $data['url'] ) ) wp_send_json_error( array( 'message' => 'Stripe error: ' . ( $data['error']['message'] ?? 'unknown' ) ) );
	wp_send_json_success( array( 'url' => $data['url'] ) );
}

/* ---------- ACCOUNT / AUTH ROUTES ---------- */
add_action( 'init', function () {
	add_rewrite_rule( '^account/?$',         'index.php?stilla_account=1', 'top' );
	add_rewrite_rule( '^login/?$',            'index.php?stilla_auth=login', 'top' );
	add_rewrite_rule( '^register/?$',         'index.php?stilla_auth=register', 'top' );
	add_rewrite_rule( '^cart/?$',            'index.php?stilla_cart_page=1', 'top' );
} );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'stilla_account'; $vars[] = 'stilla_auth'; $vars[] = 'stilla_cart_page';
	return $vars;
} );
add_filter( 'template_include', function ( $template ) {
	$auth = get_query_var( 'stilla_auth' );
	if ( $auth === 'login' || $auth === 'register' ) return get_stylesheet_directory() . '/' . $auth . '.php';
	if ( get_query_var( 'stilla_account' ) )    return get_stylesheet_directory() . '/account.php';
	if ( get_query_var( 'stilla_cart_page' ) )  return get_stylesheet_directory() . '/cart.php';
	return $template;
} );

// Handle registration / login / logout.
add_action( 'init', function () {
	if ( isset( $_POST['stilla_action'] ) && $_POST['stilla_action'] === 'register' ) {
		$email = sanitize_email( wp_unslash( $_POST['reg_email'] ?? '' ) );
		$pass  = wp_unslash( $_POST['reg_pass'] ?? '' );
		if ( ! $email || ! is_email( $email ) || strlen( $pass ) < 6 ) {
			wp_redirect( home_url( '/register/?reg=error&msg=invalid' ) ); exit;
		}
		if ( email_exists( $email ) ) { wp_redirect( home_url( '/register/?reg=error&msg=exists' ) ); exit; }
		$uid = wp_create_user( $email, $pass, $email );
		if ( is_wp_error( $uid ) ) { wp_redirect( home_url( '/register/?reg=error&msg=fail' ) ); exit; }
		wp_set_current_user( $uid ); wp_set_auth_cookie( $uid );
		wp_redirect( home_url( '/account/' ) ); exit;
	}
	if ( isset( $_POST['stilla_action'] ) && $_POST['stilla_action'] === 'login' ) {
		$email = sanitize_email( wp_unslash( $_POST['login_email'] ?? '' ) );
		$pass  = wp_unslash( $_POST['login_pass'] ?? '' );
		$user  = wp_authenticate( $email, $pass );
		if ( is_wp_error( $user ) ) { wp_redirect( home_url( '/login/?login=error' ) ); exit; }
		wp_set_current_user( $user->ID ); wp_set_auth_cookie( $user->ID );
		wp_redirect( home_url( '/account/' ) ); exit;
	}
	if ( isset( $_GET['action'] ) && $_GET['action'] === 'logout' ) {
		wp_logout(); wp_redirect( home_url( '/login/' ) ); exit;
	}
	if ( isset( $_POST['stilla_action'] ) && $_POST['stilla_action'] === 'lostpassword' ) {
		$email = sanitize_email( wp_unslash( $_POST['lost_email'] ?? '' ) );
		if ( $email && email_exists( $email ) ) {
			retrieve_password( $email );
		}
		// Always redirect to the same confirmation screen (don't reveal if the email exists).
		wp_redirect( home_url( '/login/?checkemail=1' ) ); exit;
	}
} );

// Record a placed order against the logged-in user (called after returning from Stripe with ?paid=1).
add_action( 'template_redirect', function () {
	if ( get_query_var( 'stilla_account' ) && ! empty( $_GET['paid'] ) && is_user_logged_in() ) {
		$cart = stilla_cart();
		if ( $cart ) {
			$lines = array();
			foreach ( $cart as $it ) { $lines[] = $it['name'] . ' ×' . $it['qty']; }
			wp_insert_post( array(
				'post_type' => 'stilla_order', 'post_title' => 'Order ' . wp_get_current_user()->user_email,
				'post_content' => implode( "\n", $lines ), 'post_status' => 'private',
				'post_author' => get_current_user_id(),
				'meta_input' => array( 'total' => stilla_cart_total(), 'items' => count( $cart ) ),
			) );
			$_SESSION['stilla_cart'] = array();
		}
	}
} );

