<?php
// Resolve the showreel video source: self-hosted MP4 from the Customizer, else a bundled sample.
function stilla_film_url() {
	$custom = get_theme_mod( 'stilla_film_url', '' );
	return $custom ?: 'https://storage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4';
}

get_header();

// Helper to render a product card used in the carousels.
function stilla_card( $p ) {
	$url  = home_url( '/product/' . $p['slug'] . '/' );
	$img  = stilla_img( $p['img'], 700 );
	$code = esc_html( $p['code'] );
	$name = esc_html( $p['name'] );
	return <<<HTML
		<a class="card" href="{$url}">
			<div class="card__media"><img src="{$img}" alt="{$name}" loading="lazy"></div>
			<div class="card__meta"><span class="card__code">{$code}</span><h3>{$name}</h3></div>
		</a>
HTML;
}

$products = stilla_products();
$soma = array_filter( $products, fn( $p ) => $p['family'] === 'SOMA' );
$frank = array_filter( $products, fn( $p ) => $p['family'] === 'FRANKFURTER' );
?>

<main id="content">

	<!-- HERO with parallax -->
	<section class="hero" id="hero">
		<div class="hero__photo" data-parallax="0.18" aria-hidden="true" style="background-image:url('<?php echo stilla_img( 'hero', 1600 ); ?>')"></div>
		<div class="hero__overlay"></div>
		<div class="hero__inner reveal">
			<p class="hero__eyebrow">New collection</p>
			<h1 class="hero__title">SOMA</h1>
			<p class="hero__lead">Soma begins with a bold thesis: 2 + 2 + 2 + 2 = ∞. With just two frames, two foam moulds, two slipcovers and two tables, the system offers almost endless configurations.</p>
			<a class="hero__cta" href="#soma">READ MORE ABOUT SOMA <span class="arrow">→</span></a>
		</div>
		<div class="hero__scroll" aria-hidden="true"><span></span></div>
	</section>

	<!-- MARQUEE -->
	<div class="marquee" aria-hidden="true">
		<div class="marquee__track">
			<span>PEACE</span><span>•</span><span>LOVE</span><span>•</span><span>GOOD DESIGN</span><span>•</span>
			<span>PEACE</span><span>•</span><span>LOVE</span><span>•</span><span>GOOD DESIGN</span><span>•</span>
		</div>
	</div>

	<!-- ABOUT -->
	<section class="about reveal" id="about">
		<div class="about__inner">
			<div class="about__copy">
				<p class="carousel-sec__count">STILLA — ÅHUS, SWEDEN</p>
				<h2>Design for the long haul.</h2>
				<p>Stilla is a Scandinavian furniture studio that designs contract-grade pieces meant to outlive trends. We pair honest materials — FSC-certified timber, recyclable steel and removable slipcovers — with a distinctly Nordic calm. Every product is built in Åhus and engineered to be repaired, not replaced.</p>
				<p>From restaurants and hotels to studios and homes, our furniture is specified where durability and good design have to coexist.</p>
				<div class="about__stats">
					<div><strong>1986</strong><span>Designing since</span></div>
					<div><strong>100%</strong><span>Repairable pieces</span></div>
					<div><strong>Åhus</strong><span>Made in Sweden</span></div>
				</div>
			</div>
			<div class="about__media" style="background-image:url('<?php echo stilla_img( 'studio_a', 1000 ); ?>')"></div>
		</div>
	</section>

	<!-- EDITORIAL SPLIT -->
	<section class="split reveal">
		<a class="split__media" data-tilt href="<?php echo home_url( '/product/s42-soma/' ); ?>">
			<img class="split__photo" src="<?php echo stilla_img( 'sofa_a', 900 ); ?>" alt="SOMA sofa" loading="lazy">
		</a>
		<div class="split__body">
			<h2>SOMA — THE SLIPCOVER SOFA</h2>
			<p>Cushions take inspiration from soft bulbous shapes: the characteristic bends give a jolly, playful twist while retaining serious, timeless proportions. Few parts deliver high flexibility, keeping the system efficient and competitively priced, while comfort meets contract-level standards with a homey feel.</p>
			<p>SOMA adapts naturally to both residential and public interiors, forming classic L-shaped sofas or structuring lounges, waiting areas, and collaborative spaces.</p>
			<a class="link-underline" href="#soma">Explore the family →</a>
		</div>
	</section>

	<!-- CAROUSEL 1: SOMA FAMILY -->
	<section class="carousel-sec" id="soma">
		<div class="carousel-sec__head reveal">
			<div>
				<h2>SOMA FAMILY</h2>
				<span class="carousel-sec__count"><?php echo count( $soma ); ?> PRODUCTS</span>
			</div>
			<div class="carousel-nav">
				<button class="cbtn cbtn--prev" data-dir="prev" aria-label="Previous">←</button>
				<button class="cbtn cbtn--next" data-dir="next" aria-label="Next">→</button>
			</div>
		</div>
		<div class="carousel" data-carousel>
			<div class="carousel__track">
				<?php foreach ( $soma as $p ) echo stilla_card( $p ); ?>
			</div>
			<div class="carousel__dots" data-dots></div>
		</div>
	</section>

	<!-- SHOWREEL / FILM -->
	<section class="showreel reveal">
		<div class="showreel__media">
			<video class="showreel__video" controls preload="none" poster="<?php echo stilla_img( 'table_a', 1100 ); ?>">
				<source src="<?php echo esc_url( stilla_film_url() ); ?>" type="video/mp4">
				Your browser does not support the video tag.
			</video>
		</div>
		<div class="showreel__copy">
			<h2>ABLE TABLE</h2>
			<p>A sleek and refined table with a confident, distinctive approach that fits just as well in an office, a dining room, or a boardroom as it does at home. We refined and simplified it until the solution became self-evident.</p>
			<a class="link-underline" href="<?php echo home_url( '/product/l428-soma-table/' ); ?>">Explore the Able Table →</a>
		</div>
	</section>

	<!-- CAROUSEL 2: FRANKFURTER -->
	<section class="carousel-sec carousel-sec--alt">
		<div class="carousel-sec__head reveal">
			<div>
				<h2>FRANKFURTER</h2>
				<span class="carousel-sec__count">DESIGNED BY JOHAN ANSANDER · <?php echo count( $frank ); ?> PRODUCTS</span>
			</div>
			<div class="carousel-nav">
				<button class="cbtn cbtn--prev" data-dir="prev" aria-label="Previous">←</button>
				<button class="cbtn cbtn--next" data-dir="next" aria-label="Next">→</button>
			</div>
		</div>
		<div class="carousel" data-carousel>
			<div class="carousel__track">
				<?php foreach ( $frank as $p ) echo stilla_card( $p ); ?>
			</div>
			<div class="carousel__dots" data-dots></div>
		</div>
	</section>

	<!-- EDITORIAL: VILLHEM (PROJECTS) -->
	<section class="split split--reverse reveal" id="projects">
		<a class="split__media" data-tilt href="<?php echo home_url( '/products/' ); ?>">
			<img class="split__photo" src="<?php echo stilla_img( 'chair_b', 900 ); ?>" alt="Villhem" loading="lazy">
		</a>
		<div class="split__body">
			<h2>VILLHEM</h2>
			<p>Vill hem! — I want to go home! Villhem is an experiment between minimum and maximum and everything in between. It reveals its structural frame and exposed joints. Clean, distinct lines create a clear yet timeless expression.</p>
			<a class="link-underline" href="<?php echo home_url( '/products/' ); ?>">Explore the family →</a>
		</div>
	</section>

	<!-- FEATURED PROJECTS → CLIENT STORIES + REVIEWS -->
	<section class="clients reveal" id="clients">
		<div class="carousel-sec__head reveal">
			<div><h2>TRUSTED BY STUDIOS &amp; BRANDS</h2><span class="carousel-sec__count">FURNITURE DELIVERED WORLDWIDE</span></div>
		</div>

		<div class="clients__logos">
			<span>PINE RESTAURANT</span><span>VOLVO</span><span>ÅHUS PAVILION</span><span>NORDIC HOTELS</span><span>STUDIO BERLIN</span><span>KOEN &amp; CO</span>
		</div>

		<div class="clients__reviews">
			<figure class="review" data-rating="5">
				<div class="review__stars" aria-label="5 out of 5 stars"></div>
				<blockquote>"The Måne sofas transformed our restaurant floor — guests keep asking where we got them. Beautiful, hard-wearing, and delivered on time."</blockquote>
				<figcaption><strong>Pine Restaurant</strong><span>Stockholm, SE · 24 Måne Sofas + Solsidan Tables</span></figcaption>
			</figure>
			<figure class="review" data-rating="5">
				<div class="review__stars" aria-label="5 out of 5 stars"></div>
				<blockquote>"We outfitted our flagship lounge with Frankfurt chairs. The proportions are impeccable and the contract build holds up to daily use."</blockquote>
				<figcaption><strong>Volvo Experience Centre</strong><span>Gothenburg, SE · 60 Frankfurt Chairs</span></figcaption>
			</figure>
			<figure class="review" data-rating="4">
				<div class="review__stars" aria-label="4 out of 5 stars"></div>
				<blockquote>"Stilla furnished our entire pavilion for the summer season. The outdoor Terra range shrugged off the rain and still looked brand new."</blockquote>
				<figcaption><strong>Åhus Pavilion</strong><span>Åhus, SE · Terra Lounge + Stack + Long</span></figcaption>
			</figure>
			<figure class="review" data-rating="5">
				<div class="review__stars" aria-label="5 out of 5 stars"></div>
				<blockquote>"From quote to install, the process was effortless. Our boutique hotel now feels like a design magazine."</blockquote>
				<figcaption><strong>Nordic Hotels</strong><span>Oslo, NO · Full seating package</span></figcaption>
			</figure>
		</div>
	</section>

</main>

<?php get_footer(); ?>
