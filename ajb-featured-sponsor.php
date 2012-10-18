<?php
/*
Plugin Name: ajb Featured Sponsor
Plugin URL: http://bamity.wordpress.com/ajb_Featured_Sponsor
Description: Adds a widget to display image, name, and link for a featured sponsor
Version: 0.1
Author: Amity Binkert
Author URI: http://bamity.wordpress.com/
License: GPLv2

------------------------------------------------------------------------
Copyright 2012 Amity Binkert amity@binkert.org

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

require_once(WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)) . "/widget.php");

// Debugging support
if (!function_exists('_log')) {
	function _log( $message ) {
		if (WP_DEBUG === true) {
			if (is_array( $message ) || is_object( $message ) ) {
				error_log( print_r( $message, true ) );
			} else {
				error_log( $message );
			}
		}
	}
}


add_action('plugins_loaded', 'AJB_Featured_Sponsor_init');

function AJB_Featured_Sponsor_init() {
	if(is_admin()) {
		global $AJB_Featured_Sponsor;
		$AJB_Featured_Sponsor = new AJB_Featured_Sponsor();
	}
}

class AJB_Featured_Sponsor {

	/*	private static $name = 'AJB Featured Sponsor';
		private static $slug = 'ajb_featured_sponsor';
		private static $version = '1.0';
	*/
	
	public function AJB_Featured_Sponsor() {
		register_activation_hook( __FILE__, array('AJB_Featured_Sponsor', 'activate'));
		register_deactivation_hook( __FILE__, array('AJB_Featured_Sponsor', 'deactivate'));

		// Add options menu
		add_action( 'admin_menu', array('AJB_Featured_Sponsor', 'create_menu'));
		add_action( 'admin_init', array('AJB_Featured_Sponsor', 'options_init'));
		
	}

	// Plugin activation function - checks that the correct version
	// of WordPress is supported
	// Could also setup some global plugin options here to their default values (pg. 19)
	public static function activate() {
		if ( version_compare( get_bloginfo( 'version' ), '3.1', '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
		}
		
		// Set default settings
		self::default_options();
	}

	public static function deactivate() {
		_log("AJB_Featured_Sponsor Deactivated");
	}
	
	public function create_menu() {
		// create custom top-level menu for this plugin
		add_options_page( 'Featured Sponsor Settings', 'Featured Sponsor',
				'manage_options', __FILE__, array('AJB_Featured_Sponsor', 'settings_page') );
	}
	
	public function settings_page() {
	?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Featured Sponsor Settings</h2>
		<?php settings_errors( 'ajb-settings-errors' ); ?>
		<form id="form-ajb-featured-sponsor-options" action="options.php" method="post" enctype="multipart/form-data">
			<?php
				settings_fields('ajb_featured_sponsor_options');
				do_settings_sections('ajb_featured_sponsor_options'); 
			?>
			<p class="submit">
				<input name="ajb_featured_sponsor_options[submit]" id="submit_options_form" type="submit" 
					class="button-primary" value="<?php esc_attr_e('Save Settings', 'ajb_featured_sponsor'); ?>" />
				<input name="ajb_featured_sponsor_options[reset]" type="submit" 
					class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'ajb_featured_sponsor'); ?>" />		
			</p>
		</form>
		</div>
	
	<?php
	}
	
	// When should this be called?
	public function options_init() {
		register_setting('ajb_featured_sponsor_options', 'ajb_featured_sponsor_options', 
				array('AJB_Featured_Sponsor', 'validate_options'));
		
		add_settings_section('ajb_featured_sponsor_main', __('Featured Sponsor Details', 'ajb_featured_sponsor'), 
				array('AJB_Featured_Sponsor', 'settings_header_text'), 'ajb_featured_sponsor_options');

		add_settings_field('ajb_featured_sponsor_title', __('Title for Sponsor Section', 'ajb_featured_sponsor'),
				array('AJB_Featured_Sponsor', 'sponsor_title_input'), 'ajb_featured_sponsor_options', 'ajb_featured_sponsor_main');
		
		
		add_settings_field('ajb_featured_sponsor_logo', __('Logo', 'ajb_featured_sponsor'), 
				array('AJB_Featured_Sponsor', 'sponsor_logo_input'), 'ajb_featured_sponsor_options', 'ajb_featured_sponsor_main');

		add_settings_field('ajb_featured_sponsor_sponsor', __('Sponsor Name', 'ajb_featured_sponsor'),
				array('AJB_Featured_Sponsor', 'sponsor_name_input'), 'ajb_featured_sponsor_options', 'ajb_featured_sponsor_main');

		add_settings_field('ajb_featured_sponsor_website', __('Sponsor Website', 'ajb_featured_sponsor'),
				array('AJB_Featured_Sponsor', 'sponsor_website_input'), 'ajb_featured_sponsor_options', 'ajb_featured_sponsor_main');
		
	}
	
	public function settings_header_text() {
		?>
			<p><?php _e('Please enter information about the featured sponsor below. '); ?></p>
		<?php
	}
	
	public function sponsor_logo_input() {
		$options = get_option('ajb_featured_sponsor_options');
		$logo = $options['logo'];
		?>
		<input type="text" id="logo_url" name="ajb_featured_sponsor_options[logo]" 
			value="<?php echo esc_url( $logo ); ?>" />
		<input id="upload_logo_button" type="button" class="button" value="<?php _e('Upload Logo', 'ajb_featured_sponsor'); ?>" />
		<span class="description"><?php _e("Upload an image for the sponsor's logo", 'ajb_featured_sponsor'); ?></span>
		<?php
	}
	
	public function sponsor_name_input() {
		$options = get_option('ajb_featured_sponsor_options');
		$sponsor = $options['sponsor'];
		?>
		<input type="text" id="sponsor_sponsor" name="ajb_featured_sponsor_options[sponsor]"
			value="<?php echo esc_html( $sponsor ); ?>" />
		<?php
	}
	
	public function sponsor_website_input() {
		$options = get_option('ajb_featured_sponsor_options');
		$website = $options['website'];
		?>
		<input type="text" id="sponsor_website" name="ajb_featured_sponsor_options[website]"
			value="<?php echo esc_url( $website ); ?>" />
		<?php		
	}
	
	public function sponsor_title_input() {
		$options = get_option('ajb_featured_sponsor_options');
		$title = $options['title'];
		?>
		<input type="text" id="sponsor_title" name="ajb_featured_sponsor_options[title]"
			value="<?php echo esc_html( $title ); ?>" />
		<?php		
	}
	
	public function get_default_options() {
		$defaults = array(
				'title' => 'Featured Sponsor',
				'logo' => '',
				'website' => 'http://www.mydonorcycle.com',
				'sponsor' => 'My Donor Cycle'
		);
		return $defaults;		
	}
	
	public function default_options() {
		$ajb_options = get_option( 'ajb_featured_sponsor_options');
		$defaults = array( 
				'title' => 'Featured Sponsor', 
				'logo' => '', 
				'website' => 'http://www.mydonorcycle.com', 
				'sponsor' => 'My Donor Cycle'
		);
		$ajb_options = wp_parse_args( (array) $ajb_options, $defaults );
		update_option('ajb_featured_sponsor_options', $ajb_options); // Not sure if this is necessary - save defaults to db?		
	}

	public function validate_options($options) {
		$valid = array();
		$submit = !empty($options['submit']) ? true : false;
		$reset = !empty($options['reset']) ? true : false;
		
		if ($submit) {
			$valid['title'] = wp_strip_all_tags($options['title']);
			$valid['website'] = esc_url_raw($options['website']);
			$valid['sponsor'] = wp_strip_all_tags($options['sponsor']);
			$valid['logo'] = $options['logo'];
		} elseif ($reset) {
			$valid = self::get_default_options();
		}
		
		return $valid;
	}
	
}

?>