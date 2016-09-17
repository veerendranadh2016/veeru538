jQuery(document).ready(function($){
	
	//When the delete Customizer Backup Button is clicked
	$( '.mp-customizer-delete-btn' ).on( 'click', function( event ){
		//Make sure the user meant to do this by popping up a dialog
		 return window.confirm( mp_customizer_backups_vars.delete_are_you_sure );
	});
	
	//When the "Upload Backup" button is clicked
	$( '.mp-customizer-backups-upload' ).on( 'click', function( event){
		
		//Show the form where the user can upload a backup
		$( '#mp-customizer-backups-upload-form').show();
			
	});
	
});