<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$group = isset( $attributes['group'] ) ? $attributes['group'] : 'team';

$sk_args               = sk_section_query_args( 'team_member' );
$sk_args['meta_key']   = '_sk_group';
$sk_args['meta_value'] = $group;
$sk_q                  = new WP_Query( $sk_args );

sk_section_open( $attributes );
?>
		<div class="sk-team-group">
			<?php if ( $sk_q->have_posts() ) : ?>
				<?php while ( $sk_q->have_posts() ) : $sk_q->the_post();
					$role  = get_post_meta( get_the_ID(), '_sk_role', true );
					$phone = get_post_meta( get_the_ID(), '_sk_phone', true );
					$email = get_post_meta( get_the_ID(), '_sk_email', true );
					?>
					<div class="sk-team-member">
						<div class="sk-team-member__photo">
							<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'sk-card' ); else : ?>
								<svg viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="38" r="22" fill="#c7cccc"/><path d="M12 96c4-24 26-34 38-34s34 10 38 34" fill="#c7cccc"/></svg>
							<?php endif; ?>
						</div>
						<div class="sk-team-member__info">
							<h3><?php the_title(); ?></h3>
							<?php if ( $role ) : ?><p><?php echo esc_html( $role ); ?></p><?php endif; ?>
							<?php if ( $phone ) : ?><p><?php echo esc_html( $phone ); ?></p><?php endif; ?>
							<?php if ( $email ) : ?><p><?php echo esc_html( $email ); ?></p><?php endif; ?>
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			<?php else : ?>
				<p>Noch niemand in dieser Gruppe erfasst.</p>
			<?php endif; ?>
		</div>
<?php
sk_section_close( $attributes );
