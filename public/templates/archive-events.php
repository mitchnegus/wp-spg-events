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

		<div class="clearfix">

			<?php
			if ( have_posts() ) :
				?>

				<header class="entry-header">
					<h1 class="entry-title">
						<?php	echo esc_html( post_type_archive_title( '', false ) ); ?> 
					</h1>
				</header><!-- .page-header -->
	
				<?php
				// Repeat the Loop (for current, recurring, and past projects)
				while ( have_posts() ) :
					the_post();
					the_title();
					the_content();
				endwhile;

			endif;
			?>

		</div>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
