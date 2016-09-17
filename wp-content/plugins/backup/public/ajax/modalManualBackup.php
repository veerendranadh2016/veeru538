<?php
    require_once(dirname(__FILE__).'/../boot.php');

    $directories = SG_BACKUP_FILE_PATHS;
    $directories = explode(',', $directories);
    $dropbox = SGConfig::get('SG_DROPBOX_ACCESS_TOKEN');
    $gdrive = SGConfig::get('SG_GOOGLE_DRIVE_REFRESH_TOKEN');
    $ftp = SGConfig::get('SG_STORAGE_FTP_CONNECTED');
    $amazon = SGConfig::get('SG_AMAZON_KEY');

    $backupType = @$_GET['backupType'];
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php ($backupType == SG_BACKUP_METHOD_MIGRATE)?_t("Prepare migration package"):_t("Manual Backup") ?></h4>
        </div>
        <form class="form-horizontal" method="post" id="manualBackup">
            <div class="modal-body sg-modal-body">
                <!-- Multiple Radios -->
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="radio">
                            <label for="fullbackup-radio">
                                <input type="radio" name="backupType" id="fullbackup-radio" value="1" checked="checked">
                                <?php _t('Full backup'); ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label for="custombackup-radio">
                                <input type="radio" name="backupType" id="custombackup-radio" value="2">
                                <?php _t('Custom backup'); ?>
                            </label>
                        </div>
                        <div class="col-md-12 sg-custom-backup">
                            <div class="checkbox">
                                <label for="custombackupdb-chbx">
                                    <input type="checkbox" class="sg-custom-option" name="backupDatabase" id="custombackupdb-chbx">
                                    <?php _t('Backup database'); ?>
                                </label>
                            </div>
                            <div class="checkbox sg-no-padding-top">
                                <label for="custombackupfiles-chbx">
                                    <input type="checkbox" class="sg-custom-option" name="backupFiles" id="custombackupfiles-chbx">
                                    <?php _t('Backup files'); ?>
                                </label>
                                <!--Files-->
                                <div class="col-md-12 sg-checkbox sg-custom-backup-files">
                                    <?php foreach ($directories as $directory): ?>
                                        <div class="checkbox">
                                            <label for="<?php echo 'sg'.$directory?>">
                                                <input type="checkbox" name="directory[]" id="<?php echo 'sg'.$directory;?>" value="<?php echo $directory;?>">
                                                <?php echo basename($directory);?>
                                            </label>
                                        </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php if(SGBoot::isFeatureAvailable('STORAGE')): ?>
                            <!--Cloud-->
                            <div class="checkbox sg-no-padding-top">
                                <label for="custombackupcloud-chbx">
                                    <input type="checkbox" name="backupCloud" id="custombackupcloud-chbx">
                                    <?php _t('Upload to cloud'); ?>
                                </label>
                                <!--Storages-->
                                <div class="col-md-12 sg-checkbox sg-custom-backup-cloud">
                                    <?php if(SGBoot::isFeatureAvailable('FTP')): ?>
                                        <div class="checkbox">
                                            <label for="cloud-ftp" <?php echo empty($ftp)?'data-toggle="tooltip" data-placement="right" title="'._t('FTP is not active.',true).'"':''?>>
                                                <input type="checkbox" name="backupStorages[]" id="cloud-ftp" value="<?php echo SG_STORAGE_FTP ?>" <?php echo empty($ftp)?'disabled="disabled"':''?>>
                                                <?php _t('FTP'); ?>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(SGBoot::isFeatureAvailable('DROPBOX')): ?>
                                        <div class="checkbox">
                                            <label for="cloud-dropbox" <?php echo empty($dropbox)?'data-toggle="tooltip" data-placement="right" title="'._t('Dropbox is not active.',true).'"':''?>>
                                                <input type="checkbox" name="backupStorages[]" id="cloud-dropbox" value="<?php echo SG_STORAGE_DROPBOX ?>"
                                                    <?php echo empty($dropbox)?'disabled="disabled"':''?>>
                                                <?php _t('Dropbox'); ?>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(SGBoot::isFeatureAvailable('GOOGLE_DRIVE')): ?>
                                        <div class="checkbox">
                                            <label for="cloud-gdrive" <?php echo empty($gdrive)?'data-toggle="tooltip" data-placement="right" title="'._t('Google Drive is not active.',true).'"':''?>>
                                                <input type="checkbox" name="backupStorages[]" id="cloud-gdrive" value="<?php echo SG_STORAGE_GOOGLE_DRIVE?>"
                                                    <?php echo empty($gdrive)?'disabled="disabled"':''?>>
                                                <?php _t('Google Drive'); ?>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(SGBoot::isFeatureAvailable('AMAZON')): ?>
                                        <div class="checkbox">
                                            <label for="cloud-amazon" <?php echo empty($amazon)?'data-toggle="tooltip" data-placement="right" title="'._t('Amazon S3 is not active.',true).'"':''?>>
                                                <input type="checkbox" name="backupStorages[]" id="cloud-amazon" value="<?php echo SG_STORAGE_AMAZON?>"
                                                    <?php echo empty($amazon)?'disabled="disabled"':''?>>
                                                <?php _t('Amazon S3'); ?>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        <?php endif; ?>
                        <!-- Background mode -->
                        <?php if(SGBoot::isFeatureAvailable('BACKGROUND_MODE')): ?>
                            <div class="checkbox">
                                <label for="background-chbx">
                                    <input type="checkbox" name="backgroundMode" id="background-chbx">
                                    <?php _t('Background mode'); ?>
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="text" name="backup-type" value="<?php echo $backupType?>" hidden>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="sgBackup.manualBackup()" class="btn btn-primary"><?php echo _t('Backup')?></button>
            </div>
        </form>
    </div>
</div>
