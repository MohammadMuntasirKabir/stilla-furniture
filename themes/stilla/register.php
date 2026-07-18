<?php
/**
 * Register page — split-screen professional layout.
 */
get_header();
$reg_err = '';
if ( isset( $_GET['reg'] ) && $_GET['reg'] === 'error' ) {
	$m = $_GET['msg'] ?? '';
	$reg_err = $m === 'exists' ? 'That email is already registered.' : ( $m === 'invalid' ? 'Enter a valid email and a password of 6+ characters.' : 'Could not create your account. Please try again.' );
}
?>
<main id="content" class="authpage">
	<section class="auth-split auth-split--reverse">
		<!-- Form panel -->
		<div class="auth-main">
			<div class="auth-card">
				<header class="auth-card__head">
					<h1>Create account</h1>
					<p>Already a member? <a class="auth-link" href="<?php echo esc_url( home_url( '/login/' ) ); ?>">Sign in</a></p>
				</header>

				<?php if ( $reg_err ) : ?>
					<div class="auth-alert" role="alert"><?php echo esc_html( $reg_err ); ?></div>
				<?php endif; ?>

				<form method="post" class="auth-form" novalidate>
					<input type="hidden" name="stilla_action" value="register">
					<div class="field">
						<input type="email" id="reg_email" name="reg_email" placeholder=" " required autocomplete="email">
						<label for="reg_email">Email address</label>
					</div>
					<div class="field">
						<input type="password" id="reg_pass" name="reg_pass" placeholder=" " required minlength="6" autocomplete="new-password">
						<label for="reg_pass">Password (min. 6 characters)</label>
					</div>
					<button type="submit" class="btn-auth">Create account</button>
				</form>

				<p class="auth-card__switch">
					By creating an account you agree to our <a class="auth-link" href="#">Terms</a> &amp; <a class="auth-link" href="#">Privacy Policy</a>.
				</p>
			</div>
		</div>

		<!-- Brand panel -->
		<aside class="auth-aside auth-aside--dark">
			<a class="auth-aside__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">STILLA<span>.</span></a>
			<div class="auth-aside__body">
				<p class="auth-aside__eyebrow">Join Stilla</p>
				<h2 class="auth-aside__title">Furniture made to last a lifetime.</h2>
				<p class="auth-aside__text">Create an account to save favourites, track orders and unlock trade pricing on contract-grade pieces.</p>
				<ul class="auth-aside__list">
					<li>Member-only launches</li>
					<li>Order tracking &amp; history</li>
					<li>Personalised recommendations</li>
				</ul>
			</div>
			<p class="auth-aside__foot">Trusted by studios &amp; brands worldwide</p>
		</aside>
	</section>
</main>
<?php get_footer(); ?>
