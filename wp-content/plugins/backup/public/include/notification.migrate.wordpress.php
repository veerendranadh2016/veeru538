<div which-notice="migrationError" class="update-nag notice is-dismissible">
	<p>
		<h2>Migration failed</h2><?php echo SGConfig::get('SG_BACKUP_MIGRATION_ERROR')?>
		<a href="<?php echo SG_BACKUP_SITE_URL?>"><?php echo SG_BACKUP_SITE_URL?></a>
	</p>
	<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
</div>
