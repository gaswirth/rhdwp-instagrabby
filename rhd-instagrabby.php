<?php
/**
 * Plugin Name: Instagrabby
 * Description: A simple Instagram widget
 * Author: Roundhouse Designs
 * Author URI: https://roundhouse-designs.com
 * Version: 1.0.1
**/


/* ==========================================================================
	Plugin Setup
   ========================================================================== */


define( 'RHD_INSTA_DIR', plugins_url( null, __FILE__ ) );

require 'rhd-instagrabby-options.php';

use MetzWeb\Instagram\Instagram;


/* ==========================================================================
	Instagrabby Widget
   ========================================================================== */

class RHD_Instagrabby extends WP_Widget {
	function __construct() {
		parent::__construct(
				'rhd_instagrabby', // Base ID
			__('Instagrabby', 'rhd'), // Name
			array( 'description' => __( 'A simple Instagram widget.', 'rhd' ), ) // Args
		);

		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'display_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'display_styles' ) );
	}

    public function admin_scripts( $hook ) {

	}

	public function admin_styles() {
		// wp_enqueue_style( 'rhd-instagrabby', RHD_INSTA_DIR . '/css/rhd-instagrabby-admin.css' );
	}

	public function display_scripts() {
		wp_enqueue_script( 'cycle2', RHD_INSTA_DIR . '/js/cycle2/jquery.cycle2.min.js', array( 'jquery' ), '2.1.6', true );
		wp_enqueue_script( 'cycle2-carousel', RHD_INSTA_DIR . '/js/cycle2/jquery.cycle2.carousel.min.js', array( 'jquery', 'cycle2' ), '2.1.6', true );

		if ( rhd_is_mobile() )
			wp_enqueue_script( 'jquery-mobile', RHD_INSTA_DIR . '/js/jquery.mobile.min.js', array( 'jquery' ), '1.4.5', true );

		wp_enqueue_script( 'rhd-instagrabby', RHD_INSTA_DIR . '/js/rhd-instagrabby.js', array( 'jquery', 'modernizr', 'cycle2', 'cycle2-carousel' ), null, true );
	}

	public function display_styles() {
		wp_enqueue_style( 'rhd-instagrabby', RHD_INSTA_DIR . '/css/rhd-instagrabby.css' );
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;

		$instance['title'] = ( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['visible'] = ( $new_instance['visible'] ) ? absint( $new_instance['visible'] ) : '';
		$instance['limit'] = ( $new_instance['limit'] ) ? absint( $new_instance['limit'] ) : '';
		$instance['id'] = ( $new_instance['id'] ) ? absint( $new_instance['id'] ) : '';

		return $instance;
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		extract( $args );

		$title = ( $instance['title'] ) ? apply_filters('widget_title', $instance['title']) : '';
		$visible = absint( $instance['visible'] ) + 1;
		$limit = absint( $instance['limit'] );
		$id = ( $instance['id'] ) ? absint( $instance['id'] ) : $this->id;

		$options = get_option( 'rhd_instagrabby_settings' );
		$token = $options['rhd_instagrabby_access_token'];
		$userID = $options['rhd_instagrabby_user_id'];
		$instagram = new Instagram(array(
			'apiKey'      => $options['rhd_instagrabby_client_id'],
			'apiSecret'   => $options['rhd_instagrabby_client_secret'],
			'apiCallback' => admin_url() . 'options-general.php?page=rhd_instagrabby_settings'
		));

		$instagram->setAccessToken( $token );
		$feed = $instagram->getUserMedia( 'self', $limit );
		$user = $instagram->getUser();

		echo $before_widget;

		echo $title;

		if ( $feed ) {
			$output = "<div id='rhd_instagrabby_container-$id' class='rhd-instagrabby-container'>\n"
					. "<a href='#' class='rhd-instagrabby-pager cycle-prev'><img src='" . RHD_INSTA_DIR . "/img/leftarrow.svg' alt='Carousel left'></a><a href='#' class='rhd-instagrabby-pager cycle-next'><img src='" . RHD_INSTA_DIR . "/img/rightarrow.svg' alt='Carousel right'></a>\n"
					. "<ul class='rhd-instagrabby' data-cycle-carousel-visible='$visible'>\n";

			foreach ($feed->data as $post) {
				$caption = ( $post->caption->text ) ? $post->caption->text : 'Instagram: no caption';

				$output .= "<li class='rhd-instagrabby-post'>\n"
						. "<a href='{$post->link}' target='_blank'><img src='{$post->images->standard_resolution->url}' alt='$caption'></a>"
						. "</li>";
			}

			$output .= "</ul>\n</div>\n";
		}

		echo $output;

		echo $after_widget;
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$args['title'] = esc_attr( $instance['title'] );
		$args['visible'] = ( $instance['visible'] ) ? absint( $instance['visible'] ) : 5;
		$args['limit'] = ( $instance['limit'] ) ? absint( $instance['limit'] ) : 10;
		$args['id'] = ( $this->id ) ? $this->id : $args['id'];
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $args['title']; ?>" >
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'visible' ); ?>"><?php _e( 'Visible photos <em>(Default: 5)</em>:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'visible' ); ?>" name="<?php echo $this->get_field_name( 'visible' ); ?>" type="text" value="<?php echo $args['visible']; ?>" >
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Instagram load limit <em>(Default: 10)</em>:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $args['limit']; ?>" >
		</p>
	<?php
	}
}

function register_rhd_instagrabby_widget() {
    register_widget( 'RHD_Instagrabby' );
}
add_action( 'widgets_init', 'register_rhd_instagrabby_widget' );


/* ==========================================================================
	Instagrabby Shortcode
   ========================================================================== */

/**
 * rhd_instagrabby_shortcode function.
 *
 * @access public
 * @param mixed $atts
 * @return void
 */
add_shortcode( 'instagrabby', 'rhd_instagrabby_shortcode' );
function rhd_instagrabby_shortcode( $atts ) {
	extract( shortcode_atts( array(
								'title' => '',
								'id' => 'NO-ID',
								'visible' => 5,
								'limit' => 10
							),
							$atts, 'instagrabby' ) );

	$args = array(
		'before_title'	=> '<h2 class="widget-title">',
		'after_title'	=> '</h2>',
		'before_widget' => '<div id="rhd_instagrabby-' . $id . '" class="widget widget_rhd_instagrabby_widget">',
		'after_widget'  => '</div>'
	);

	ob_start();
	the_widget( 'RHD_Instagrabby', $atts, $args );
	$output = ob_get_clean();

	return $output;
}
