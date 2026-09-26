<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$data_heading = isset( $attributes['dataHeading'] ) ? $attributes['dataHeading'] : 'Kontaktdaten';
$form_heading = isset( $attributes['formHeading'] ) ? $attributes['formHeading'] : 'Kontaktformular';
$submit_label = isset( $attributes['submitLabel'] ) && trim( wp_strip_all_tags( $attributes['submitLabel'] ) ) ? $attributes['submitLabel'] : 'Absenden';

sk_section_open( $attributes );
?>
		<div class="sk-grid sk-grid--2 sk-kontakt-grid">
			<div>
				<?php sk_block_heading( $data_heading, 'h3' ); ?>
				<p><strong>Adresse:</strong><br>
				<?php echo esc_html( get_theme_mod( 'sk_address_street', 'Musterstrasse 12' ) ); ?>,<br>
				<?php echo esc_html( get_theme_mod( 'sk_address_city', '8000 Zürich' ) ); ?></p>

				<p><strong>Telefon:</strong><br>
				<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', get_theme_mod( 'sk_phone', '+41 234 43 21' ) ) ); ?>"><?php echo esc_html( get_theme_mod( 'sk_phone', '+41 234 43 21' ) ); ?></a></p>

				<p><strong>E-Mail:</strong><br>
				<a href="mailto:<?php echo esc_attr( get_theme_mod( 'sk_email', 'info@solarkraftwerk.ch' ) ); ?>"><?php echo esc_html( get_theme_mod( 'sk_email', 'info@solarkraftwerk.ch' ) ); ?></a></p>

				<p><strong>Öffnungszeiten:</strong><br>
				<?php echo esc_html( get_theme_mod( 'sk_hours', 'Mo–Fr 08:00–17:00 Uhr' ) ); ?></p>

				<div class="sk-map">
					<?php $maps_embed = get_theme_mod( 'sk_maps_embed', '' ); ?>
					<?php if ( $maps_embed ) : ?>
						<iframe src="<?php echo esc_url( $maps_embed ); ?>" width="100%" height="260" style="border:0;" loading="lazy"></iframe>
					<?php else : ?>
						<span>Kartenausschnitt (Google Maps)</span>
					<?php endif; ?>
				</div>
			</div>

			<div>
				<?php sk_block_heading( $form_heading, 'h3' ); ?>
				<?php if ( isset( $_GET['sk_contact'] ) && 'ok' === $_GET['sk_contact'] ) : ?>
					<p class="sk-form-notice sk-form-notice--ok">Vielen Dank für Ihre Nachricht! Wir melden uns in Kürze.</p>
				<?php elseif ( isset( $_GET['sk_contact'] ) && 'error' === $_GET['sk_contact'] ) : ?>
					<p class="sk-form-notice sk-form-notice--error">Bitte alle Pflichtfelder ausfüllen.</p>
				<?php endif; ?>
				<form class="sk-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="sk_contact_form">
					<?php wp_nonce_field( 'sk_contact_form', 'sk_contact_nonce' ); ?>
					<input type="text" name="website" class="sk-hp-field" tabindex="-1" autocomplete="off">

					<label for="sk-name">Name</label>
					<input type="text" id="sk-name" name="name" placeholder="Ihr Name" required>

					<label for="sk-email">E-Mail</label>
					<input type="email" id="sk-email" name="email" placeholder="ihre@email.ch" required>

					<label for="sk-phone">Telefon</label>
					<input type="tel" id="sk-phone" name="phone" placeholder="+41 79 000 00 00">

					<label for="sk-message">Nachricht</label>
					<textarea id="sk-message" name="message" rows="5" placeholder="Ihre Nachricht" required></textarea>

					<button type="submit" class="sk-button"><?php echo wp_kses_post( $submit_label ); ?></button>
				</form>
			</div>
		</div>
<?php
sk_section_close( $attributes );
