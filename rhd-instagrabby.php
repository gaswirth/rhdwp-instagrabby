<?php
/**
 * Plugin Name: Instagrabby
 * Description: A simple Instagram widget
 * Author: Roundhouse Designs
 * Author URI: https://roundhouse-designs.com
 * Version: 0.1
**/


/* ==========================================================================
	Plugin Setup
   ========================================================================== */


define( 'RHD_INSTA_DIR', plugins_url() );

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
		// wp_enqueue_style( 'rhd-instagrabby', RHD_INSTA_DIR . 'css/rhd-instagrabby-admin.css' );
	}

	public function display_scripts() {
		// wp_enqueue_script( 'rhd-instagrabby', RHD_INSTA_DIR . 'js/rhd-instagrabby.js', array( 'jquery' ) );
	}

	public function display_styles() {
		wp_enqueue_style( 'rhd-instagrabby', RHD_INSTA_DIR . 'rhd-instagrabby.css' );
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;

		$instance['title'] = ( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		$vals = get_option( 'rhd_instagrabby_settings' );

		extract( $args );

		$instagram = new Instagram(array(
			'apiKey'      => $vals['rhd_instagrabby_client_id'],
			'apiSecret'   => $vals['rhd_instagrabby_client_secret'],
			'apiCallback' => 'http://dev.roundhouse-designs.com/damasklove/wp-admin/options-general.php?page=rhd_instagrabby_settings'
		));
		$token = get_option( '_rhd_instagrabby_token' );

		$title = ( $instance['title'] ) ? apply_filters('widget_title', $instance['title']) : '';

		echo $before_widget;

		echo $title;
		?>

		<?php
			$instagram->getAccessToken();
			$result = $instagram->getUserMedia();
			print_r($result);
		?>

		<?php echo $after_widget;
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$args['title'] = esc_attr( $instance['title'] );
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $args['title']; ?>" >
		</p>
	<?php
	}
}
// register RHD_Instagrabby widget
function register_rhd_instagrabby_widget() {
    register_widget( 'RHD_Instagrabby' );
}
add_action( 'widgets_init', 'register_rhd_instagrabby_widget' );