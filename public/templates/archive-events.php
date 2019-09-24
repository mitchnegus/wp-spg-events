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
	
					<?php
					// Repeat the Loop (for current, recurring, and past projects)
					while ( have_posts() ) :
						?>
	
						<div class='event-block' style="color: white;">
	
						<?php
						the_post();
						the_title();
						the_content();
						$post_id = $post->ID;
						$terms = get_the_terms( $post, 'speakers' );
						$event_date = get_post_meta( $post_id, 'event_date', true);
						$event_time = get_post_meta( $post_id, 'event_time', true);
						$event_location = get_post_meta( $post_id, 'event_location', true);
						if ( $terms ) {
							foreach ( $terms as $term ) {
								echo esc_html( $term->name );
							}
						}
	
						?>
	
						<p><b>Date: </b><?php echo esc_html( $event_date ); ?></p>		
						<p><b>Time: </b><?php echo esc_html( $event_time ); ?></p>		
						<p><b>Location: </b><?php echo esc_html( $event_location ); ?></p>		
						</div>
	
						<?php
					endwhile;
	
				endif;
				?>
	
			</div>
		</div><!-- .wp-spg-events -->

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
