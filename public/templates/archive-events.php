<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

/**
 * Displays each project.
 *
 * @param    string   $event_type    			 The type of event to be displayed.
 * @param    string   $event_type_title    The title of the event type to be displayed.
 */

function format_datetime( $event_date, $event_time ) {
	// Express date and time as text
	$date_ymd = explode( '-', $event_date );
	if ( ! empty ( $event_time ) ) {
		$time_hm = explode(':', $event_time );
		$date_format = 'F j, g:i a';
	} else {
		$time_hm = array(0, 0);
		$date_format = 'F j';
	}

	$epoch_datetime = mktime(
	 	$time_hm[0], 
		$time_hm[1],
		0,
		$date_ymd[1],
		$date_ymd[2],
		$date_ymd[0]
	);
	$text_datetime = date( $date_format, $epoch_datetime );
	return $text_datetime;
}

function format_setting( $event_date, $event_time, $event_location ) {
	$text_datetime = format_datetime( $event_date, $event_time );
	if ( ! empty( $text_datetime ) && ! empty( $event_location ) ) {
		$text_setting = $text_datetime . ' – ' . $event_location;
	} else if ( ! empty( $text_datetime ) ) {
		$text_setting = $text_datetime;
	} else if ( ! empty( $event_location ) ) {
		$text_setting = $event_location;
	} else {
		$text_setting = '';
	}
	return $text_setting;
}

get_header(); 
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<div class="wp-spg-events">
			<div class="clearfix">
	
				<?php
				if ( have_posts() ) :
					?>
	
					<header class="entry-header">
						<h1 class="entry-title">
							<?php	echo esc_html( post_type_archive_title( '', false ) ); ?> 
						</h1>
					</header><!-- .page-header -->
		
					<div class="google-calendar">
						<h2>
							<iframe style="border: 0;" src="https://calendar.google.com/calendar/embed?src=calscipol%40gmail.com&amp;ctz=America%2FLos_Angeles" scrolling="no" width="800" height="600" frameborder="0">
							</iframe>
						</h2>
					</div>
					<div class="timeline">
	
						<?php
						// Repeat the Loop (for current, recurring, and past projects)
						while ( have_posts() ) :
							the_post();
							$post_id = $post->ID;
							$post_content = $post->post_content;
							$terms = get_the_terms( $post, 'speakers' );
							$event_date = get_post_meta( $post_id, 'event_date', true);
							$event_time = get_post_meta( $post_id, 'event_time', true);
							$event_location = get_post_meta( $post_id, 'event_location', true);
							$text_setting = format_setting( $event_date, $event_time, $event_location );
							?>
		
							<div class="event">		
								<div class="timeline-circle"></div>
								<div class="event-block-triangle"></div>
								<div class="event-block">
									<h3 class="event-title"><?php the_title(); ?></h3>
									<div class="event-info">

										<div class="event-description">

											<?php											
											if ( $terms ) {
												?>

												<div class="event-speakers">
													<div><b>Featuring: </b></div>

													<div class="speaker-list">

														<?php
														foreach ( $terms as $speaker ) {
															$speaker_name = $speaker->name;
															?>
	
															<div class="speaker">
																<div class="speaker-name"><?php echo esc_html( $speaker_name ); ?></div>
																<div class="speaker-thumbnail">
																	<img class="speaker-thumbnail" src="https://sciencepolicy.berkeley.edu/wp-content/plugins/wp-member-bios/img/headshot_template.png">
																</div>
															</div>
	
															<?php												
														}
														?>

													</div>
												</div>
											
												<?php
											}

											if ( $post_content ) {
												?>

												<div></div>
												<p><?php the_content(); ?></p>

												<?php
											}
											?>
				
											<p class="event-setting">
												<?php echo esc_html( $text_setting ); ?>
											</p>		
										</div>

										<?php the_post_thumbnail( array(100, 100), ['class' => 'event-thumbnail'] ); ?>

									</div>
								</div>
							</div>
		
							<?php
						endwhile;
		
					endif;
					?>
	
				</div>
			</div>
		</div><!-- .wp-spg-events -->

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
