<div class="inline" style="width: 60%">
    <span style="font-size:16px;">
        <?php _e('Use Professional version of "Dropbox backup and restore" plugin and get:','dropbox-backup') ; ?>
    </span>
    <ul class="list-dropbox-backup-pro">
        <li><img src="<?php echo plugins_url('/template/ok-icon.png', dirname(__FILE__));?>" title="" alt="" />
            <span class="text">
                <?php _e('Automated Dropbox backup (Scheduled backup tasks)','dropbox-backup') ; ?>
            </span>
        </li>
        <li>
            <img src="<?php echo plugins_url('/template/ok-icon.png', dirname(__FILE__));?>" title="" alt="" />
            <span class="text">
                <?php _e('Automated Local backup (Scheduled backup tasks)','dropbox-backup') ; ?>
            </span>
        </li>
        <li>
            <img src="<?php echo plugins_url('/template/ok-icon.png', dirname(__FILE__));?>" title="" alt="" />
            <span class="text">
                <?php _e('Backup Status E-Mail Reporting','dropbox-backup') ; ?>
            </span>
        </li>
        <li>
            <img src="<?php echo plugins_url('/template/ok-icon.png', dirname(__FILE__));?>" title="" alt="" />
            <span class="text">
                <?php _e('Online Service "Backup Website Manager" (Copy, Clone or Migrate of websites)','dropbox-backup') ; ?>
            </span>
        </li>
        <li>
            <img src="<?php echo plugins_url('/template/ok-icon.png', dirname(__FILE__));?>" title="" alt="" />
            <span class="text">
                <?php _e('One Year Free Updates for PRO version','dropbox-backup') ; ?>
            </span>
        </li>
        <li>
            <img src="<?php echo plugins_url('/template/ok-icon.png', dirname(__FILE__));?>" title="" alt="" />
            <span class="text">
                <?php _e('One Year Priority support','dropbox-backup') ; ?>
            </span>
        </li>
    </ul>
</div>
<div class="inline-right" style="margin-top: 0;">
    <div class="image-dropbox-pro" onclick="document.dropbox_pro_form.submit();">
        <img src="<?php echo plugins_url('/template/dropbox_pro_logo_box1.png', dirname(__FILE__));?>" title="<?php _e('Get PRO version','dropbox-backup');?>" alt="<?php _e('Get PRO version','dropbox-backup'); ?>">
    </div>
    <div style="margin-top:26%; float: left; margin-left: 20px; margin-right: 15px;">
        <form action="<?php echo WPADM_URL_PRO_VERSION; ?>api/" method="post" id="dropbox_pro_form" name="dropbox_pro_form" >
            <input type="hidden" value="<?php echo home_url();?>" name="site">
            <input type="hidden" value="<?php echo 'proBackupPay'?>" name="actApi">
            <input type="hidden" value="<?php echo get_option('admin_email');?>" name="email">
            <input type="hidden" value="<?php echo 'dropbox-backup';?>" name="plugin">
            <input type="hidden" value="<?php echo admin_url("admin.php?page=wpadm_wp_full_backup_dropbox&pay=success"); ?>" name="success_url">
            <input type="hidden" value="<?php echo admin_url("admin.php?page=wpadm_wp_full_backup_dropbox&pay=cancel"); ?>" name="cancel_url">
            <input type="submit" class="backup_button" value="<?php _e('Get PRO','dropbox-backup');?>">
        </form>
    </div>
</div>

<div class="clear"></div>