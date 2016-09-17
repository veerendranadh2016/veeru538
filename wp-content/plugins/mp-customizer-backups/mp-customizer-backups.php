<?php
/*
Plugin Name: MP Customizer Backups
Plugin URI: http://mintplugins.com
Description: Backup the Theme Mods in your Customizer with either a click or by triggering a function.
Version: 1.0.3
Author: Mint Plugins
Author URI: http://mintplugins.com
Text Domain: mp_customizer_backups
Domain Path: languages
License: GPL2
*/

/*  Copyright 2015  Phil Johnston  (email : phil@mintplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Mint Plugins Core.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/
// Plugin version
if( !defined( 'MP_CUSTOMIZER_BACKUPS_VERSION' ) )
	define( 'MP_CUSTOMIZER_BACKUPS_VERSION', '1.0.3' );

// Plugin Folder URL
if( !defined( 'MP_CUSTOMIZER_BACKUPS_PLUGIN_URL' ) )
	define( 'MP_CUSTOMIZER_BACKUPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Plugin Folder Path
if( !defined( 'MP_CUSTOMIZER_BACKUPS_PLUGIN_DIR' ) )
	define( 'MP_CUSTOMIZER_BACKUPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin Root File
if( !defined( 'MP_CUSTOMIZER_BACKUPS_PLUGIN_FILE' ) )
	define( 'MP_CUSTOMIZER_BACKUPS_PLUGIN_FILE', __FILE__ );

/*
|--------------------------------------------------------------------------
| GLOBALS
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| INTERNATIONALIZATION
|--------------------------------------------------------------------------
*/

function mp_customizer_backups_textdomain() {

	// Set filter for plugin's languages directory
	$mp_customizer_backups_lang_dir = dirname( plugin_basename( MP_CUSTOMIZER_BACKUPS_PLUGIN_FILE ) ) . '/languages/';
	$mp_customizer_backups_lang_dir = apply_filters( 'mp_customizer_backups_languages_directory', $mp_customizer_backups_lang_dir );


	// Traditional WordPress plugin locale filter
	$locale        = apply_filters( 'plugin_locale',  get_locale(), 'mp-customizer-backups' );
	$mofile        = sprintf( '%1$s-%2$s.mo', 'mp-customizer-backups', $locale );

	// Setup paths to current locale file
	$mofile_local  = $mp_customizer_backups_lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/mp-customizer-backups/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/mp-customizer-backups folder
		load_textdomain( 'mp_customizer_backups', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/mp-customizer-backups/languages/ folder
		load_textdomain( 'mp_customizer_backups', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'mp_customizer_backups', false, $mp_customizer_backups_lang_dir );
	}

}
add_action( 'init', 'mp_customizer_backups_textdomain', 1 );

/*
|--------------------------------------------------------------------------
| INCLUDES
|--------------------------------------------------------------------------
*/
function mp_customizer_backups_include_files(){
	/**
	 * If mp_core isn't active, stop and  as the user to install it now. We use some of its classes to generate the icon font output and picker.
	 */
	if (!function_exists('mp_core_textdomain') ){
		
		/**
		 * Include Plugin Checker
		 */
		require( MP_CUSTOMIZER_BACKUPS_PLUGIN_DIR . '/includes/plugin-checker/class-plugin-checker.php' );
		
		/**
		 * Include Plugin Installer
		 */
		require( MP_CUSTOMIZER_BACKUPS_PLUGIN_DIR . '/includes/plugin-checker/class-plugin-installer.php' );
		
		/**
		 * Check if mp_core in installed
		 */
		require( MP_CUSTOMIZER_BACKUPS_PLUGIN_DIR . 'includes/plugin-checker/included-plugins/mp-core-check.php' );
				
	}
	/**
	 * Otherwise, if mp_core is active, carry out the plugin's functions
	 */
	else{
								
		/**
		 * icon creator
		 */
		require( MP_CUSTOMIZER_BACKUPS_PLUGIN_DIR . 'includes/misc-functions/customizer-backups.php' );
				
	}
}
add_action('plugins_loaded', 'mp_customizer_backups_include_files', 9);