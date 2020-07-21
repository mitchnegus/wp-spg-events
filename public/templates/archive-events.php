<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

/**
 * Loop over each period, displaying only the selected period.
 *
 * @param    string   $period    			 The period (year + annual period) of the events to be displayed.
 */
function loop_over_period( $period ) {

	// Create an array to record matching events (events in this period)
	$matching_posts = array();
	while ( have_posts() ) :
		global $post;
		the_post();
		$event_period = get_post_meta( $post->ID, 'event_period', true);
		// Only record the event if it matches the selected period
		if ( $period == $event_period ) {
			$matching_posts[] = $post;
		}
	endwhile;

	if ( $matching_posts ) {
		// Display the period title
		?>

		<div class="event-period">
			<h2><?php echo $period; ?></h2>

			<?php
			// Loop over the recorded events for display
			foreach ( $matching_posts as $matching_post ) {
				display_event( $matching_post );
			}
			?>

		</div>

		<?php
	}
}

/**
 * Displays each event.
 *
 * @param WP_POST		$matching_post		The event custom post.
 */
function display_event( $matching_post ) {

	$post_id = $matching_post->ID;
	$post_content = $matching_post->post_content;
	$event_title = get_the_title( $matching_post );
	$terms = get_the_terms( $matching_post, 'speakers' );
	$event_period = get_post_meta( $post_id, 'event_period', true);
	$event_date = get_post_meta( $post_id, 'event_date', true);
	$event_time = get_post_meta( $post_id, 'event_time', true);
	$event_location = get_post_meta( $post_id, 'event_location', true);
	$text_setting = format_setting( $event_date, $event_time, $event_location );
	$event_thumbnail_tag = get_the_post_thumbnail(
		$matching_post,
		array(100, 100),
		['class' => 'event-thumbnail']
	);
	?>

	<div class="event">		
		<div class="timeline-circle"></div>
		<div class="event-block-triangle"></div>
		<div class="event-block">
			<h3 class="event-title"><?php echo esc_html( $event_title ); ?></h3>
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

										<?php
										$speaker_id = $speaker->term_id;
										$speaker_photo_id = get_term_meta( $speaker_id, 'speaker_thumbnail', $single = true );
										if ( ! empty( $speaker_photo_id ) ) {
											$speaker_photo_url = wp_get_attachment_image_src( $speaker_photo_id )[0];
											?>

											<div class="speaker-thumbnail">
												<img class="speaker-thumbnail" src="<?php echo esc_url( $speaker_photo_url ); ?>">
											</div>

											<?php
										}
										?>

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
						<p><?php echo esc_html( $post_content ); ?></p>

						<?php
					}
					?>
	
					<p class="event-setting">
						<?php echo esc_html( $text_setting ); ?>
					</p>		
				</div>

				<?php echo $event_thumbnail_tag; ?>

			</div>
		</div>
	</div>
		
	<?php
}

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
						// Repeat the Loop (for each period in the set of years
						$first_year = get_option( 'spg_period_origin_year' );
						$last_year = date( 'Y' ) + 1;
						$years = range( $last_year, $first_year );
						foreach ( $years as $year ) {
							foreach ( array_reverse( SPG_EVENTS_PERIODS ) as $period ) {

								// Ensure that the period is active
								$db_period_active = 'spg_' . $period . '_active';
								if ( get_option( $db_period_active ) ) {

									// Iterate over the loop for each period
									$db_period_name = 'spg_' . $period . '_name';
									$event_period = $year . ' ' . get_option( $db_period_name );
									loop_over_period( $event_period );

								}

							}
						}
		
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
