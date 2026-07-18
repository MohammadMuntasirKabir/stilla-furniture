<footer class="footer">
	<div class="footer__cta">
		<h2>PEACE, LOVE &amp; GOOD DESIGN</h2>
		<a class="btn-solid" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Shop the collection →</a>
	</div>

	<div class="footer__news">
		<div class="footer__news-copy">
			<h3>NEWSLETTER</h3>
			<p>Join our mailing list to be in the know about new releases and studio stories.</p>
		</div>
		<form class="footer__form" onsubmit="return false;">
			<input type="email" placeholder="Enter your email" aria-label="Email" required>
			<button type="submit">Send</button>
		</form>
	</div>

	<div class="footer__cols">
		<div class="footer__col">
			<h4>PRODUCTS</h4>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/products/' ) ); ?>">All Products</a></li>
				<li><a href="<?php echo esc_url( home_url( '/products/?type=sofa' ) ); ?>">Seating</a></li>
				<li><a href="<?php echo esc_url( home_url( '/products/?type=table' ) ); ?>">Tables</a></li>
				<li><a href="<?php echo esc_url( home_url( '/products/?type=outdoor' ) ); ?>">Outdoor</a></li>
				<li><a href="<?php echo esc_url( home_url( '/products/?family=FRANKFURTER' ) ); ?>">Frankfurt</a></li>
				<li><a href="<?php echo esc_url( home_url( '/products/?family=SOMA' ) ); ?>">Soma</a></li>
			</ul>
		</div>
		<div class="footer__col">
			<h4>COMPANY</h4>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/company/' ) ); ?>">Our Story</a></li>
				<li><a href="<?php echo esc_url( home_url( '/sustainability/' ) ); ?>">Sustainability</a></li>
				<li><a href="<?php echo esc_url( home_url( '/inspiration/' ) ); ?>">Inspiration</a></li>
				<li><a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Tools &amp; Specs</a></li>
				<li><a href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Catalogue</a></li>
				<li><a href="mailto:info@stilla.example">Contact</a></li>
			</ul>
		</div>
		<div class="footer__col">
			<h4>GET SOCIAL</h4>
			<ul>
				<li><a href="https://www.instagram.com/" target="_blank" rel="noopener">Instagram</a></li>
				<li><a href="https://www.linkedin.com/" target="_blank" rel="noopener">LinkedIn</a></li>
				<li><a href="https://www.pinterest.com/" target="_blank" rel="noopener">Pinterest</a></li>
			</ul>
		</div>
		<div class="footer__col footer__col--brand">
			<h4>STILLA AB</h4>
			<p>Sandvaktaregatan 17<br>Åhus, Sweden</p>
			<p><a href="mailto:info@stilla.example">info@stilla.example</a></p>
			<p class="footer__since">Furniture since 1986</p>
		</div>
	</div>

	<div class="footer__legal">
		<a href="<?php echo esc_url( home_url( '/company/' ) ); ?>">Privacy &amp; Terms</a>
		<a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Delivery &amp; Support</a>
		<span>© <?php echo date( 'Y' ); ?> Stilla — a demo storefront inspired by blastation.com</span>
	</div>
</footer>

<?php wp_footer(); ?>
