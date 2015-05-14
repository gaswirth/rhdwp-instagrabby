<?php
/**
 * Theme Options Admin Panel
 *
 * Theme settings page
 *
 * @package WordPress
 * @subpackage rhd
 */

require 'vendor/cosenary/instagram/src/Instagram.php';
use MetzWeb\Instagram\Instagram;

class RHD_Instagrabby_Options
{
	/**
	* Holds the values to be used in the fields callbacks
	*/
	private $options;

	/**
	* Start up
	*/
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'rhd_instagrabby_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'rhd_instagrabby_register_settings' ) );
	}

	/**
	* Add options page
	*/
	public function rhd_instagrabby_admin_menu()
	{
		add_options_page(
			'Instagrabby Plugin Settings',
			'Instagrabby Settings',
			'manage_options',
			'rhd_instagrabby_settings',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	* Options page callback
	*/
	public function create_admin_page()
	{
		// Set class property
		$this->options = get_option( 'rhd_instagrabby_settings' );

		$instagram = new Instagram(array(
			'apiKey'      => $this->options['rhd_instagrabby_client_id'],
			'apiSecret'   => $this->options['rhd_instagrabby_client_secret'],
			'apiCallback' => 'http://dev.roundhouse-designs.com/damasklove/wp-admin/options-general.php?page=rhd_instagrabby_settings'
		));

		$loginUrl = $instagram->getLoginUrl();

		$code = $_GET['code'];
		echo ( $code ) ? "Code: $code" : '';
	?>
	<div class="wrap">
		<h2>Damask Love Theme Options</h2>
		<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'rhd_instagrabby_settings_group' );
				do_settings_sections( 'rhd-instagrabby-settings' );

				if ( !$code ) {
					echo "<p>\n"
						. "<a class='login' href='$loginUrl'>» Login with Instagram</a>\n"
						. "<h4>Use your Instagram account to login.</h4>\n"
					. "</p>";
				} else {
					// receive OAuth token object
					echo "Logged in!";
					$data = $instagram->getOAuthToken($code);
					$username = $username = $data->user->username;

					// store user access token
					$instagram->setAccessToken($data);

					//update_option( '_rhd_instagrabby_token', $instagram->getAccessToken() );

					$result = $instagram->getUserMedia();
					print_r($result);
				}

				submit_button();
			?>
		</form>
	</div>
	<?php
	}

	/**
	* Register and add settings
	*/
	public function rhd_instagrabby_register_settings()
	{
		register_setting(
			'rhd_instagrabby_settings_group', // Option group
			'rhd_instagrabby_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'rhd_instagrabby_auth_section', // ID
			'Authentication', // Title
			array( $this, 'print_authentication_info' ), // Callback
			'rhd-instagrabby-settings' // Page
		);

		add_settings_field(
			'rhd_instagrabby_client_id', // ID
			'Client ID: ', // Title
			array( $this, 'client_id_cb' ), // Callback
			'rhd-instagrabby-settings', // Page
			'rhd_instagrabby_auth_section' // Section
		);

		add_settings_field(
			'rhd_instagrabby_client_secret', // ID
			'Client Secret: ', // Title
			array( $this, 'client_secret_cb' ), // Callback
			'rhd-instagrabby-settings', // Page
			'rhd_instagrabby_auth_section' // Section
		);
	}

	/**
	* Sanitize each setting field as needed
	*
	* @param array $input Contains all settings fields as array keys
	*/
	public function sanitize( $input )
	{
		$new_input = array();

		if( isset( $input['rhd_instagrabby_client_id'] ) )
			$new_input['rhd_instagrabby_client_id'] = sanitize_text_field( $input['rhd_instagrabby_client_id'] );

		if( isset( $input['rhd_instagrabby_client_secret'] ) )
			$new_input['rhd_instagrabby_client_secret'] = sanitize_text_field( $input['rhd_instagrabby_client_secret'] );

		return $new_input;
	}

	/**
	* Print the Section text
	*/
	public function print_authentication_info()
	{
		// print 'Enter full URLs, please (e.g. "https://twitter.com/@roundhouseguys"):';
	}

	/**
	* Input callbacks
	*/
	public function client_id_cb( $args )
	{
		printf(
			'<input type="text" id="rhd_instagrabby_client_id" name="rhd_instagrabby_settings[rhd_instagrabby_client_id]" value="%s" />',
			isset( $this->options['rhd_instagrabby_client_id'] ) ? esc_attr( $this->options['rhd_instagrabby_client_id']) : ''
		);
	}

	public function client_secret_cb( $args )
	{
		printf(
			'<input type="text" id="rhd_instagrabby_client_secret" name="rhd_instagrabby_settings[rhd_instagrabby_client_secret]" value="%s" />',
			isset( $this->options['rhd_instagrabby_client_secret'] ) ? esc_attr( $this->options['rhd_instagrabby_client_secret']) : ''
		);
	}
}

if( is_admin() )
	$rhd_instagrabby_settings_page = new RHD_Instagrabby_Options();