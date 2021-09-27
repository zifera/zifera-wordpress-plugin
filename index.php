<?php
/**
 * Plugin Name: Zifera
 * Plugin URI: https://zifera.io
 * Description: Zifera plugin for inserting installation script.
 * Version: 1.0
 * Author: Zifera
 */

class Zifera {
	private $zifera_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'zifera_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'zifera_page_init' ) );
	}

	public function zifera_add_plugin_page() {
		add_options_page(
			'Zifera', // page_title
			'Zifera', // menu_title
			'manage_options', // capability
			'zifera', // menu_slug
			array( $this, 'zifera_create_admin_page' ) // function
		);
	}

	public function zifera_create_admin_page() {
		$this->zifera_options = get_option( 'zifera_option_name' ); ?>

		<div class="wrap">
			<img src="<?php echo plugins_url( '/assets/img/zifera-black.svg', __FILE__ ) ;?>" style="height: 30px;" alt="Zifera logo" />
			<p>Embed the Zifera script on your website.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'zifera_option_group' );
					do_settings_sections( 'zifera-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function zifera_page_init() {
		register_setting(
			'zifera_option_group', // option_group
			'zifera_option_name', // option_name
			array( $this, 'zifera_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'zifera_setting_section', // id
			'Settings', // title
			array( $this, 'zifera_section_info' ), // callback
			'zifera-admin' // page
		);

		add_settings_field(
			'key_0', // id
			'Key', // title
			array( $this, 'key_0_callback' ), // callback
			'zifera-admin', // page
			'zifera_setting_section' // section
		);

		add_settings_field(
			'enable_1', // id
			'Enable', // title
			array( $this, 'enable_1_callback' ), // callback
			'zifera-admin', // page
			'zifera_setting_section' // section
		);
	}

	public function zifera_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['key_0'] ) ) {
			$sanitary_values['key_0'] = sanitize_text_field( $input['key_0'] );
		}

		if ( isset( $input['enable_1'] ) ) {
			$sanitary_values['enable_1'] = $input['enable_1'];
		}

		return $sanitary_values;
	}

	public function zifera_section_info() {
		
	}

	public function key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="zifera_option_name[key_0]" id="key_0" value="%s">',
			isset( $this->zifera_options['key_0'] ) ? esc_attr( $this->zifera_options['key_0']) : ''
		);
	}

	public function enable_1_callback() {
		printf(
			'<input type="checkbox" name="zifera_option_name[enable_1]" id="enable_1" value="enable_1" %s> <label for="enable_1">Enable the script on your website</label>',
			( isset( $this->zifera_options['enable_1'] ) && $this->zifera_options['enable_1'] === 'enable_1' ) ? 'checked' : ''
		);
	}

}
if ( is_admin() )
	$zifera = new Zifera();

function zifera_footer_script_embed() {
    $zifera_options = get_option( 'zifera_option_name' );
    if ($zifera_options['key_0'] && $zifera_options['enable_1']) {
    wp_enqueue_script( 'zifera_script', 'https://stats.zifera.nl/scripts/stats.latest.js', array(), null, true);
    }
}
add_action( 'wp_enqueue_scripts', 'zifera_footer_script_embed' );

add_filter( 'script_loader_tag', 'zifera_add_attributes_to_script', 10, 3 );

function zifera_add_attributes_to_script( $tag, $handle, $source ) {
    $zifera_options = get_option( 'zifera_option_name' );
    if ( 'zifera_script' === $handle ) {
        $tag = '<script async data-zifera-key="' . $zifera_options['key_0'] . '" src="' . $source . '"></script>';
    }

    return $tag;
}

;?>