<?php			
/**
 * This page contains functions used to backup the Customer Theme Mods 
 *
 * @link https://mintplugins.com/doc/
 * @since 1.0.0
 *
 * @package     MP Customizer Backups
 * @subpackage  Functions
 *
 * @copyright   Copyright (c) 2015, Mint Plugins
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author      Philip Johnston
 */
 
/**
 * Create the Page under "Appearance" > "Customizer Backups" in the WordPress dashboard.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_customizer_backups() {
	add_theme_page('Customizer Backups', 'Customizer Backups', 'edit_theme_options', 'mp-customizer-backups', 'mp_customizer_backups_callback');
}
add_action('admin_menu', 'mp_customizer_backups');

/**
 * Enqueue the scripts we use for the customizer backup
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_customizer_backup_enqueue_admin_scripts(){
	
	wp_enqueue_script( 'mp_customizer_backups_admin_js', plugins_url( 'js/customizer_backups_admin.js', dirname(__FILE__) ), MP_CUSTOMIZER_BACKUPS_VERSION ); 
	
	wp_localize_script( 'mp_customizer_backups_admin_js', 'mp_customizer_backups_vars', 
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce_value' => wp_create_nonce( 'mp-customizer-backups-nonce-action-name' ), 
			'delete_are_you_sure' => __( 'Are you sure you want to delete this Customizer Backup?', 'mp_customizer_backups' )
		) 
	);	
	
}
add_action( 'admin_enqueue_scripts', 'mp_customizer_backup_enqueue_admin_scripts' );

/**
 * This is what is shown on the "Appearance" > "Customizer Backups" page.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_customizer_backups_callback(){
	
	echo '<div class="wrap">';
	
		screen_icon();
	
		//Show the title for the Page at the top.
		echo '<p>';
			echo '<h2>' . __( 'Customizer Backups.', 'mp_core' ) . '</h2>';
			echo __( 'The Customizer is where you control Theme Options and can be found under "Appearance" > "Customizer". Use this page to create backups of those options.', 'mp_customizer_backups' );
		echo '</p>';
		
		//Show option to create a backup now
		echo '<p>';
			
			//Create Backup Button
			echo '<a href="' . add_query_arg( array( 'mp_customizer_backup_create' => time(), 'mp_customizer_backup_nonce' => wp_create_nonce( 'mp_customizer_backup_create_nonce' ) ), admin_url( 'themes.php?page=mp-customizer-backups' ) ) . '" class="mp-customizer-backups-create button">' . __( 'Create Backup of current Customizer Settings.', 'mp_customizer_backups' ) . '</a>';
			
			//Upload Backup Button
			echo '<div class="mp-customizer-backups-upload button">' . __( 'Upload Backup', 'mp_customizer_backups' ) . '</div>';
			
			echo '<div id="mp-customizer-backups-upload-form" style="display:none;">';
				
				echo '<h2>' . __( 'Paste the contents of the Backup File you previously downloaded:', 'mp_core' ) . '</h2>';
				
				echo '<form method="POST">';
					echo '<textarea name="mp_customizer_backup_upload" rows="20" cols="70" ></textarea>';
					wp_nonce_field( 'upload_customizer_backup', 'mp_customizer_backup_upload_nonce' );
					echo '<p><input type="submit" class="button" value="Restore and Revert-To Backup"></p>';
				echo '</form>';
				
			echo '</div>';
			
		echo '</p>';
				
		//Get the array holding all the stored backups of the customizer
		$backups = get_option( 'mp_customizer_backups' );
		
		//If there are NO backups saved
		if ( empty( $backups ) ){
			
			//Show that there have been no Backups Saved.
			echo '<p>';
				echo __( 'No customizer backups currently exist.', 'mp_core' );
			echo '</p>';
			return false;
				
		}
			
		//If there ARE backups to show
		echo '<p>';
			echo '<h2>' . __( 'Existing Backups', 'mp_customizer_backups' ) . '</h2>';
			echo __( 'Listed below are the Customizer Backups that have been created.', 'mp_core' );
		echo '</p>';
		
		//Create the Table which displays a link to revert/download each Backup
		echo '<table class="form-table">';
			
			//Flip the saved backups array so the newest ones show at the top
			$reversed = array_reverse( $backups, true );
			
			//Loop through each backup
			foreach( $reversed as $timestamp => $backup ){
			
				//Output a title for this backup
				echo '<tr class="form-field">';	
					
					//Note: Each backup is identified by its timestamp as the key
					
					//Output the title and description for this backup
					echo '<th scope="row">' . date( 'M d, Y', $timestamp ) . __( ' at ', 'mp_core' ) . date( 'g:i a', $timestamp ) . '</th>';
             		echo '<td><p class="description">' . $backup['backup_context_note'] . '</p>';
          				
					//Output the actions the user can take for these backups
					echo '<div class="mp-customizer-backup-actions">'	;		
						
						//Revert Button
						echo ' <a class="button  mp-customizer-revert-btn" href="' . add_query_arg( array( 'mp_revert_customizer' => $timestamp, 'mp_customizer_backup_nonce' => wp_create_nonce( 'mp_customizer_backup_revert_nonce' ) ), admin_url( 'themes.php?page=mp-customizer-backups' ) ) . '">' . __( 'Revert to this Backup', 'mp_customizer_backups' ) . '</a>';
						
						//Download Button
						echo ' <a class="button  mp-customizer-download-btn" href="' . add_query_arg( array( 'mp_download_customizer' => $timestamp, 'mp_customizer_backup_nonce' => wp_create_nonce( 'mp_customizer_backup_download_nonce' ) ), admin_url( 'themes.php?page=mp-customizer-backups' ) ) . '">' . __( 'Download this Backup', 'mp_customizer_backups' ) . '</a>';
						
						//Delete Button
						echo ' <a class="button mp-customizer-delete-btn" href="' . add_query_arg( array( 'mp_delete_customizer' => $timestamp, 'mp_customizer_backup_nonce' => wp_create_nonce( 'mp_customizer_backup_delete_nonce' ) ), admin_url( 'themes.php?page=mp-customizer-backups' ) ) . '">' . __( 'Delete this Backup', 'mp_customizer_backups' ) . '</a>';
						
					echo '</div>';
				echo '</td></tr>';
					
			}
		echo '</table>';
		
	echo '</div>';	
}

/**
 * This function is used to backup the customizer and store it in the Options table.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    string $backup_context_note This is a note which provides context to the user about when/why this backup was created.
 * @param    array $theme_mods_aray Optional. If blank, the backup will be created using the current state of the customizer. If passed-in, it will create a backup using that array.
 * @return   array $backup_theme_mods This is the array containing the theme mods array we just created.
*/
function mp_backup_customizer( $backup_context_note, $theme_mods_array = NULL ){
	
	//Get the array holding all the stored backups of the customizer
	$backups = get_site_option( 'mp_customizer_backups' );
	
	//If theme mods were passed-in, use those
	if ( !empty( $theme_mods_array ) && is_array( $theme_mods_array ) ) {
		$backup_theme_mods = $theme_mods_array;
	}
	//If no theme mods were passed in, use the current state of the customizer
	else{
		$backup_theme_mods = get_theme_mods();
	}
	
	$time_right_now = time();
	
	//Create a backup of the customizer how it currently is now		
	$backups[$time_right_now] = array( 
		'backup_context_note' => $backup_context_note,
		'theme_mods' => $backup_theme_mods
	);
	
	//Update the site option which contains all backups to include this new backup
	update_site_option( 'mp_customizer_backups', $backups ); 
	
	//Return the backup_theme_mods for this backup
	return $backups[$time_right_now];
	
}

/**
 * This function is used to revert the Customizer to the user-selected backup.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    string $backup_timestamp The timestamp of the backup we are reverting-to.
 * @param    string $backup_context_note This is a note which provides context to the user about when/why this backup was created.
 * @return   bool
*/
function mp_revert_customizer( $backup_timestamp, $backup_context_note ){
	
	//Get the array holding all the stored backups of the customizer
	$backups = get_site_option( 'mp_customizer_backups' );
		
	//If a backup under this timestamp doesn't exist, return false
	if ( !isset( $backups[$backup_timestamp] ) ){
		return false;	
	}
	
	//Get the current Theme Mods
	$current_customizer = get_theme_mods();
	
	//Default for generating a new backup of the current state is True
	$generate_new_backup = true;
	
	//Loop through each backup to see if any existing backups match curent state of the customizer - an exact match.
	foreach( $backups as $backup ){
					
		//If one of our old backups is exactly the same as the current state of the customizer
		if ( $backup['theme_mods'] === $current_customizer ){
					
			//We don't need to auto-generate another backup because we'd have two identical ones
			$generate_new_backup = false;	
		}
			
	}
	
	//If the current state of the Customizer is unique
	if ( $generate_new_backup ){
	
		//Backup the current state of the customizer
		mp_backup_customizer( $backup_context_note );
		
	}
	
	//Loop through each Theme Mod in the user-selected backup and save it to the current WP
	foreach( $backups[$backup_timestamp]['theme_mods'] as $name => $value ){
		
		set_theme_mod( $name, $value );
		
	}
		
	return true;		
	
}

/**
 * This function is used to delete a backup of the customizer
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    string $backup_timestamp The timestamp of the backup we are reverting-to.
 * @return   bool
*/
function mp_customizer_delete_backup( $backup_timestamp ){
	
	//Get the array holding all the stored backups of the customizer
	$backups = get_site_option( 'mp_customizer_backups' );
	
	//Each backup's key in the array is the timestamp from when it was created. Get the timestamp of the Backup we are deleting from the URL.
	$backup_timestamp = $_GET['mp_delete_customizer'];
	
	//If a backup under this timestamp doesn't exist, return false
	if ( !isset( $backups[$backup_timestamp] ) ){
		return false;	
	}
	
	//Remove the selected backup from the array of backups
	unset( $backups[$backup_timestamp] );
	
	//Update the site option which contains all backups to no longer include this backup
	update_site_option( 'mp_customizer_backups', $backups ); 
	
	return true;		
	
}

/**
 * This function is used when the user clicks "Revert to Backup" on the "Appearance" > "Customizer Backups" page.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_revert_customizer_click_callback(){
	
	$nonce = isset( $_REQUEST['mp_customizer_backup_nonce'] ) ? $_REQUEST['mp_customizer_backup_nonce'] : NULL;
		
	//If we should revert to a backup of the customizer
	if( wp_verify_nonce( $nonce, 'mp_customizer_backup_revert_nonce' ) && isset( $_GET['mp_revert_customizer'] ) && !empty( $_GET['mp_revert_customizer'] ) ){
		
		//Revert to the selected Backup using the Timestamp as the identifier (stored in $_GET['mp_revert_customizer']).
		$revert_success = mp_revert_customizer( $_GET['mp_revert_customizer'], __( 'This backup was automaticaly created because an older Backup was clicked.', 'mp_customizer_backups') );
		
		//If the revert was successful
		if ( $revert_success ){
			//Redirect to the Customizer Backups page with an Admin Notice that the revert was successful
			wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_reverted=true' ) ); exit;
		}
		//If the revert was not successful
		else{
			
			//Redirect to the Customizer Backups page with an Admin Notice that the revert failed
			wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_reverted=false' ) ); exit;
		}
		
	}
}
add_action( 'admin_init', 'mp_revert_customizer_click_callback' );

/**
 * This function is used when the user clicks "Create Backup" on the "Appearance" > "Customizer Backups" page.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_customizer_backup_create_click_callback(){
	
	$nonce = isset( $_REQUEST['mp_customizer_backup_nonce'] ) ? $_REQUEST['mp_customizer_backup_nonce'] : NULL;
	
	//If we should create a backup of the customizer
	if( wp_verify_nonce( $nonce, 'mp_customizer_backup_create_nonce' ) && isset( $_GET['mp_customizer_backup_create'] ) && !empty( $_GET['mp_customizer_backup_create'] ) ){
		
		//Revert to the selected Backup using the Timestamp as the identifier (stored in $_GET['mp_revert_customizer']).
		$backup_success = mp_backup_customizer( __( 'This backup was created because the "Create Backup" button was clicked.', 'mp_customizer_backups') );
		
		//If the revert was successful
		if ( $backup_success ){
			
			//Redirect to the Customizer Backups page with an Admin Notice that the backup was successfuly created
			wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_created=true' ) ); exit;
			
		}
		//If the revert was not successful
		else{
			
			//Redirect to the Customizer Backups page with an Admin Notice that there was a problem creating the backup
			wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_created=false' ) ); exit;
			
		}
		
	}
}
add_action( 'admin_init', 'mp_customizer_backup_create_click_callback' );

/**
 * This function is used when the user clicks "Delete Backup" on the "Appearance" > "Customizer Backups" page.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_customizer_delete_backup_click_callback(){
	
	$nonce = isset( $_REQUEST['mp_customizer_backup_nonce'] ) ? $_REQUEST['mp_customizer_backup_nonce'] : NULL;
		
	//If we should revert to a backup of the customizer
	if( wp_verify_nonce( $nonce, 'mp_customizer_backup_delete_nonce' ) && isset( $_GET['mp_delete_customizer'] ) && !empty( $_GET['mp_delete_customizer'] ) ){
		
		//Delete the selected Backup using the Timestamp as the identifier (stored in $_GET['mp_delete_customizer']).
		$delete_success = mp_customizer_delete_backup( $_GET['mp_delete_customizer'] );
		
		//If the delete was successful
		if ( $delete_success ){
			//Redirect to the Customizer Backups page with an Admin Notice that the delete was successful
			wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_deleted=true' ) ); exit;
		}
		//If the delete was not successful
		else{
			
			//Redirect to the Customizer Backups page with an Admin Notice that the delete failed
			wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_deleted=false' ) ); exit;
		}
		
	}
}
add_action( 'admin_init', 'mp_customizer_delete_backup_click_callback' );

/**
 * This function is used to output all admin notices used by this plugin
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_customizer_backups_admin_notices(){
	
	//If neither of our URL variables are used here, get out of here.
	if ( !isset( $_GET['mp_customizer_backup_created'] )  && !isset( $_GET['mp_customizer_backup_reverted'] ) && !isset( $_GET['mp_customizer_backup_deleted'] ) && !isset( $_GET['mp_customizer_backup_uploaded'] ) ){
		return false;
	}
	
	//If a backup was just successfuly created
	if ( isset( $_GET['mp_customizer_backup_created'] ) && $_GET['mp_customizer_backup_created'] ){
		?><div class="updated">
					<p>
						<strong><?php echo __( 'Customizer Backup Created.', 'mp_customizer_backups' ); ?></strong>
					</p>
				  </div><?php
	}
	//If a backup was attempted but failed
	else if( isset( $_GET['mp_customizer_backup_created'] ) && !$_GET['mp_customizer_backup_created'] ){
			  
		?><div class="error">
					<p>
						<strong><?php echo __( 'Oops! Something went wrong. Please try again.', 'mp_customizer_backups' ); ?></strong>
					</p>
				  </div><?php
	}
	//If a revert was just successfully executed
	else if( isset( $_GET['mp_customizer_backup_reverted'] ) && $_GET['mp_customizer_backup_reverted'] ){
			  
		?><div class="updated">
				<p>
					<strong><?php echo __( 'Customizer Backup Restored.', 'mp_customizer_backups' ); ?></strong>
				</p>
			  </div><?php
	}
	//If a revert was attempted but failed
	else if( isset( $_GET['mp_customizer_backup_reverted'] ) && !$_GET['mp_customizer_backup_reverted'] ){
			  
		?><div class="error">
				<p>
					<strong><?php echo __( 'Oops! That backup doesn\'t appear to exist.', 'mp_customizer_backups' ); ?></strong>
				</p>
			  </div><?php
	}
	//If a backup delete was just successfully executed
	else if( isset( $_GET['mp_customizer_backup_deleted'] ) && $_GET['mp_customizer_backup_deleted'] ){
			  
		?><div class="updated">
				<p>
					<strong><?php echo __( 'Customizer Backup Deleted.', 'mp_customizer_backups' ); ?></strong>
				</p>
			  </div><?php
	}
	//If a backup delete attempt failed
	else if( isset( $_GET['mp_customizer_backup_deleted'] ) && !$_GET['mp_customizer_backup_deleted'] ){
			  
		?><div class="error">
				<p>
					<strong><?php echo __( 'Oops! That backup doesn\'t appear to exist.', 'mp_customizer_backups' ); ?></strong>
				</p>
			  </div><?php
	}
	//If a backup upload attempt was successful
	else if( isset( $_GET['mp_customizer_backup_uploaded'] ) && $_GET['mp_customizer_backup_uploaded'] ){
			  
		?><div class="updated">
				<p>
					<strong><?php echo __( 'Uploaded Customizer Restored', 'mp_customizer_backups' ); ?></strong>
				</p>
			  </div><?php
	}
	//If a backup upload attempt failed
	else if( isset( $_GET['mp_customizer_backup_uploaded'] ) && !$_GET['mp_customizer_backup_uploaded'] ){
			  
		?><div class="error">
				<p>
					<strong><?php echo __( 'Uploaded Customizer Failed', 'mp_customizer_backups' ); ?></strong>
				</p>
			  </div><?php
	}
		  
}
add_action( 'admin_notices', 'mp_customizer_backups_admin_notices');

/**
 * This function is used to download the Customizer.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_download_customizer(){
	
	$nonce = isset( $_REQUEST['mp_customizer_backup_nonce'] ) ? $_REQUEST['mp_customizer_backup_nonce'] : NULL;
	
	//If we should revert to a backup of the customizer
	if( wp_verify_nonce( $nonce, 'mp_customizer_backup_download_nonce' ) && isset( $_GET['mp_download_customizer'] ) && !empty( $_GET['mp_download_customizer'] ) ){
				
		//Get the array holding all the stored backups of the customizer
		$backups = get_site_option( 'mp_customizer_backups' );
			
		//Each backup's key in the array is the timestamp from when it was created. Get the timestamp of the Backup we are downloading from the URL.
		$backup_timestamp = $_GET['mp_download_customizer'];
		
		//Convert this page to a text file
		header('Content-disposition: attachment; filename=customizer_backup_'. $backup_timestamp . '.txt');
		header('Content-type: text/plain');
		
		//Put the contents of this backup into a text file and deliver it to the user
		echo json_encode( $backups[$_GET['mp_download_customizer']]['theme_mods'] );
		
		die();
								
	}
}
add_action( 'admin_init', 'mp_download_customizer' );

/**
 * This function is used to "upload" a Customizer Backup.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_upload_customizer(){

	$nonce = isset( $_POST['mp_customizer_backup_upload_nonce'] ) ? $_POST['mp_customizer_backup_upload_nonce'] : NULL;
	
	//If we should create a backup from the submitted "upload" form
	if( wp_verify_nonce( $nonce, 'upload_customizer_backup' ) && isset( $_POST['mp_customizer_backup_upload'] ) ){
		
		$allowed_tags = wp_kses_allowed_html( 'post' );
								
		//Uploaded Theme Mods array
		$uploaded_theme_mods = json_decode( stripslashes( $_POST['mp_customizer_backup_upload'] ), true);
				
		//If this is not an array, the user didn't upload the right thing
		if ( !is_array( $uploaded_theme_mods ) ){
			//Redirect to the Customizer Backups page with an Admin Notice that the upload failed
			wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_uploaded=0' ) ); exit;
		}
		
		$sanitized_theme_mods = NULL;
		
		//Loop through each theme mod
		foreach( $uploaded_theme_mods as $name_first_level => $value_first_level ){
			
			//Sanitize this field_name
			$sanitized_name_first_level = wp_kses( sanitize_text_field( $name_first_level ), $allowed_tags );
				
			//If this theme mod is an array
			if ( is_array( $value_first_level ) ){
				
				//Loop through each option in this sub-array
				foreach ( $theme_mod as $name_second_level => $value_second_level ){
					
					//If one of these is an array, skip it - we're only going 2 levels deep here for now.
					if ( is_array( $value_second_level ) ){
						continue;	
					}
					else{
						
						//Sanitize this field_name
						$sanitized_name_second_level = wp_kses( sanitize_text_field( $name_second_level ), $allowed_tags );
						
						//Sanitize this field_value
						$sanitized_value_second_level = wp_kses( sanitize_text_field( $value_second_level ), $allowed_tags );
						
						//Add this key/value to the new sanitized array
						$sanitized_theme_mods[$sanitized_name_first_level][$sanitized_name_second_level] = $sanitized_value_second_level;
					
					}
					
				}
				
			}
			//If this theme mod is not an array
			else{
								
				//Sanitize this field_value
				$sanitized_value_first_level = wp_kses( sanitize_text_field( $value_first_level ), $allowed_tags );
				
				//Add this key/value to the new sanitized array
				$sanitized_theme_mods[$sanitized_name_first_level] = $sanitized_value_first_level;
			}
				
		}
				
		//Create a backup using the uploaded theme mods array
		$backup_theme_mods = mp_backup_customizer( __( 'This backup was created by uploading a Backup file\'s contents.', 'mp_customizer_backups'), $sanitized_theme_mods );
		
		//Revert to the uploaded Theme Mods
		mp_revert_customizer( time(), __( 'This backup was created by uploading a Backup file\'s contents.', 'mp_customizer_backups') );
		
		//Redirect to the Customizer Backups page with an Admin Notice that the upload was successful
		wp_redirect( admin_url( 'themes.php?page=mp-customizer-backups&mp_customizer_backup_uploaded=true' ) ); exit;
								
	}
}
add_action( 'admin_init', 'mp_upload_customizer' );

/**
 * Put a "View Customizer Backups" button on the Customizer at the top.
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/
 * @param    void
 * @return   void
*/
function mp_customizer_backups_button_in_customizer( $wp_customize ) {
  	
	?>
    
    <script type="text/javascript">
		jQuery(document).ready(function($){
		
			$( '#customize-info' ).append( '<div style="padding:10px;"><a href="<?php echo admin_url( 'themes.php?page=mp-customizer-backups'); ?>" class="button"><?php echo __( 'Create/View Customizer Backups', 'mp_customizer_backups' ); ?></a></div>' );
			
		});
    </script>
    
    <?php
	
}
add_action( 'customize_controls_print_footer_scripts', 'mp_customizer_backups_button_in_customizer' );