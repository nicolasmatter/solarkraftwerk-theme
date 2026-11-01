<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$mode = isset( $attributes['mode'] ) ? $attributes['mode'] : 'teaser';

// Products have no pages of their own; teaser cards jump to their card in the overview.
$overview_url = sk_block_link_url( $attributes, 'produkte' );

$by_type = array_fill_keys( array_keys( sk_product_types() ), array() );
foreach ( get_posts( sk_section_query_args( 'product' ) ) as $sk_product ) {
	$by_type[ sk_product_type( $sk_product->ID ) ][] = $sk_product;
}

// The catalog is headed by its product types, so it has no section heading or link.
if ( 'catalog' === $mode ) {
	$attributes['heading'] = '';
}

sk_section_open( $attributes );

if ( 'catalog' === $mode ) :
	$headings = array(
		'product' => isset( $attributes['productsHeading'] ) ? $attributes['productsHeading'] : '',
		'service' => isset( $attributes['servicesHeading'] ) ? $attributes['servicesHeading'] : '',
	);
	if ( array_filter( $by_type ) ) :
		foreach ( $by_type as $type => $products ) :
			if ( ! $products ) continue;
			?>
			<div class="sk-product-group">
				<?php sk_block_heading( $headings[ $type ] ); ?>
				<div class="sk-grid sk-grid--cards">
					<?php foreach ( $products as $sk_product ) : ?>
						<article class="sk-product-card sk-product-card--<?php echo esc_attr( sk_product_color( $sk_product->ID ) ); ?>" id="produkt-<?php echo esc_attr( $sk_product->ID ); ?>">
							<div class="sk-product-card__title"><h3><?php echo esc_html( get_the_title( $sk_product ) ); ?></h3></div>
							<div class="sk-product-card__body"><?php echo apply_filters( 'the_content', $sk_product->post_content ); ?></div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		endforeach;
	else :
		?>
		<p>Noch keine Produkte veröffentlicht.</p>
		<?php
	endif;
else :
	$products = array_merge( ...array_values( $by_type ) );
	if ( $products ) :
		?>
		<div class="sk-grid sk-grid--cards">
			<?php foreach ( $products as $sk_product ) : ?>
				<a class="sk-card sk-card--product sk-card--<?php echo esc_attr( sk_product_color( $sk_product->ID ) ); ?>" href="<?php echo esc_url( $overview_url . '#produkt-' . $sk_product->ID ); ?>">
					<div class="sk-card__label"><h3><?php echo esc_html( get_the_title( $sk_product ) ); ?></h3></div>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	else :
		?>
		<p>Noch keine Produkte veröffentlicht.</p>
		<?php
	endif;
endif;

sk_section_close( $attributes, 'catalog' === $mode ? '' : 'produkte' );
