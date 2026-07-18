<?php
/**
 * Login page — split-screen professional layout.
 * Sign-in view shows only up to the "Forgot password?" link. Clicking it swaps to a
 * separate reset-password view (not inline on the sign-in page).
 */
get_header();
$err  = isset( $_GET['login'] ) && $_GET['login'] === 'error';
$sent = isset( $_GET['checkemail'] );
// If the success message is present, open directly on the reset view.
$view = $sent ? 'reset' : 'login';
?>
<main id="content" class="authpage">
	<section class="auth-split">
		<!-- Brand panel -->
		<aside class="auth-aside">
			<a class="auth-aside__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">STILLA<span>.</span></a>
			<div class="auth-aside__body">
				<p class="auth-aside__eyebrow">Member access</p>
				<h2 class="auth-aside__title">Welcome back to Stilla.</h2>
				<p class="auth-aside__text">Sign in to view your order history, track deliveries and pick up where you left off.</p>
				<ul class="auth-aside__list">
					<li>Order history &amp; invoices</li>
					<li>Saved addresses &amp; preferences</li>
					<li>Faster checkout on every order</li>
				</ul>
			</div>
			<p class="auth-aside__foot">Åhus, Sweden · Peace, love &amp; good design</p>
		</aside>

		<!-- Form panel -->
		<div class="auth-main">
			<!-- VIEW 1: SIGN IN (up to the Forgot password? link only) -->
			<div class="auth-card" id="loginCard" data-view="login"<?php echo $view === 'login' ? '' : ' hidden'; ?>>
				<header class="auth-card__head">
					<h1>Sign in</h1>
					<p>New to Stilla? <a class="auth-link" href="<?php echo esc_url( home_url( '/register/' ) ); ?>">Create an account</a></p>
				</header>

				<?php if ( $err ) : ?>
					<div class="auth-alert" role="alert">Wrong email or password. Please try again.</div>
				<?php endif; ?>

				<form method="post" class="auth-form" novalidate>
					<input type="hidden" name="stilla_action" value="login">
					<div class="field">
						<input type="email" id="login_email" name="login_email" placeholder=" " required autocomplete="email">
						<label for="login_email">Email address</label>
					</div>
					<div class="field">
						<input type="password" id="login_pass" name="login_pass" placeholder=" " required autocomplete="current-password">
						<label for="login_pass">Password</label>
					</div>
					<button type="submit" class="btn-auth">Sign in</button>
				</form>

				<a href="#" class="auth-forgot" id="forgotToggle">Forgot password?</a>

				<p class="auth-card__switch">
					Don't have an account? <a class="auth-link" href="<?php echo esc_url( home_url( '/register/' ) ); ?>">Register →</a>
				</p>
			</div>

			<!-- VIEW 2: RESET PASSWORD (separate view, shown after clicking Forgot password) -->
			<div class="auth-card" id="resetCard" data-view="reset"<?php echo $view === 'reset' ? '' : ' hidden'; ?>>
				<header class="auth-card__head">
					<h1>Reset password</h1>
					<p>We'll email you a link to set a new password.</p>
				</header>

				<?php if ( $sent ) : ?>
					<div class="auth-ok" role="status">If that email exists, a reset link is on its way. Check your inbox.</div>
				<?php else : ?>
					<form method="post" class="auth-form" novalidate>
						<input type="hidden" name="stilla_action" value="lostpassword">
						<p class="auth-forgot__hint">Enter your account email and we'll send a reset link.</p>
						<div class="field">
							<input type="email" id="lost_email" name="lost_email" placeholder=" " required autocomplete="email">
							<label for="lost_email">Email address</label>
						</div>
						<button type="submit" class="btn-auth">Send reset link</button>
					</form>
				<?php endif; ?>

				<a href="#" class="auth-forgot" id="forgotBack">← Back to sign in</a>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
