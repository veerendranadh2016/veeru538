<?php
/**
* Class WPAdm_Running
*/
if (!class_exists('WPAdm_Running')) {

    if (function_exists('set_time_limit')) {
        @set_time_limit(0);
    }
    if (function_exists('ini_set')) {
        @ini_set('memory_limit', '512M');
    }


    add_action('drb_run_backup', array('wpadm_running', 'init') );

    class WPAdm_Running {

        private static $command_result_data = array();
        private static $command_result = '';
        private static $command_result_id = false;

        static function init_params_default($method = true)
        {
            update_option(PREFIX_BACKUP_ . "_commands", array());
            update_option(PREFIX_BACKUP_ . "proccess-command", array());
            set_transient('drb_running', 0, 60 * 5);
            $path = WPAdm_Core::getTmpDir();
            if (!empty($path)) {
                WPAdm_Core::rmdir($path . "/db");
                WPAdm_Core::rmdir($path . "/files");
                WPAdm_Core::rmdir($path . "/files2");
                WPAdm_Core::rmdir($path . "/archive");
                WPAdm_Core::rmdir($path . "/command_dropbox");
                WPAdm_Core::rmdir($path . "/errors_sending");
                WPAdm_Core::rmdir($path . "/tabledb");
                WPAdm_Core::rmdir($path . "/repair");
                WPAdm_Core::rmdir($path . "/restore-backup");
                WPAdm_Core::rmdir($path . "/log-restore.log");
                WPAdm_Core::rmdir($path . "/log-restore2");
                WPAdm_Core::rmdir($path . "/md5_info-restore");
                WPAdm_Core::rmdir($path . "/restore-backup");
                WPAdm_Core::rmdir($path . "/files-list-retore");
                WPAdm_Core::rmdir($path . "/extract-files-restore");
                WPAdm_Core::rmdir($path . "/cron-restore.data");
                WPAdm_Core::rmdir($path . "/stop_process");
                WPAdm_Core::rmdir($path . "/folder_create");

                if ($method) {
                    $files = glob($path ."/wpadm_method*.queue");
                    if (!empty($files)) {
                        $n = count($files);
                        for($i = 0; $i < $n; $i++) {
                            WPAdm_Core::rmdir($files[$i]);
                        }
                    }
                    $files = glob($path ."/pclzip*.tmp");
                    if (!empty($files)) {
                        $n = count($files);
                        for($i = 0; $i < $n; $i++) {
                            WPAdm_Core::rmdir($files[$i]);
                        }
                    }
                    $files = glob($path ."/wpadm_method*.done");
                    if (!empty($files)) {
                        $n = count($files);
                        for($i = 0; $i < $n; $i++) {
                            WPAdm_Core::rmdir($files[$i]);
                        }
                    }
                }
            }
        }

        static function init()
        {
            $command = self::getCommand();

            if ($command && self::is_stop() ) {
                WPAdm_Core::$cron = false;
                wpadm_class::$type = 'full';

                //$time_load = ini_get("max_execution_time");
                //WPAdm_Core::log('proccess is work ' . $time_load . 'sec');
                /*if ($time_load != 0) {
                self::run($time_load - 5);
                } else {
                self::run(90);
                } */
                self::run(60);

                if ( self::checkLock() ) {
                    $core = new WPAdm_Core($command, 'full_backup_dropbox', DRBBACKUP_BASE_DIR);
                    if (!is_bool( $core->getResult() ) && is_object( $core->getResult() ) && ( $result = $core->getResult()->toArray(true) ) ) {
                        if ($result['result'] == 'success') {
                            self::delCommand($command['method']);
                            set_transient('drb_running', 0, 60 * 5);
                            self::stop();
                            self::setCommandResultData($command['method'], $result);
                            self::init();
                        } elseif ($result['result'] == 'error') {
                            self::setCommandResultData($command['method'], $result);
                            self::stop();
                            self::init_params_default();
                        }
                    }
                }
            }
        }
        public static function checkLock()
        {
            // false - cron is running
            // true - cron not running
            $running_cron = get_transient('drb_running'); 
            if ($running_cron && $running_cron == 1) {
                $time = microtime( true );
                $locked = get_transient('doing_cron');

                if ( $locked > $time + 10 * 60 ) { // 10 minutes
                    $locked = 0;
                }
                if ((defined('WP_CRON_LOCK_TIMEOUT') && $locked + WP_CRON_LOCK_TIMEOUT > $time) || (!defined('WP_CRON_LOCK_TIMEOUT') && $locked + 60 > $time)) {
                    return false;
                }
                if (function_exists('_get_cron_array')) {
                    $crons = _get_cron_array();
                } else {
                    $crons = get_option('cron');
                }
                if (!is_array($crons)) { 
                    return false;
                }

                $values = array_values( $crons );
                if (isset($values['drb_run_backup'])) {
                    $keys = array_keys( $crons );
                    if ( isset($keys[0]) && $keys[0] > $time ) {
                        return false;
                    }
                }
            }
            set_transient('drb_running', 1, 60 * 5);
            return true;
        }

        static function is_running()
        {
            $running = get_transient('drb_running');
            if ($running && $running == 1) {
                return true;
            }
            return false;
        }

        static function is_stop() 
        {
            $stop_precess = self::getCommandResultData('stop_process');
            if ( !empty( $stop_precess ) && isset( $stop_precess['name'] ) && isset( $stop_precess['type'] ) ) {
                $_POST['backup-name'] = $stop_precess['name']; 
                if ($stop_precess['type'] == 'dropbox') {
                    $_POST['backup-type'] = $stop_precess['type']; 
                    wpadm_wp_full_backup_dropbox::delete_backup();
                    $_POST['backup-type'] = 'local';
                }
                wpadm_wp_full_backup_dropbox::delete_backup();
            }
            return empty($stop_precess);
        }

        static function getCommand($command = '')
        {
            $commands = get_option(PREFIX_BACKUP_ . "_commands");
            if ($commands !== false && is_array($commands) && isset($commands[0]) && empty($command) ) {
                return $commands[0];
            } elseif (!empty($command) && $commands !== false && is_array($commands)) {
                $id = wpadm_in_array($command, 'method', $commands, true );
                if (isset($commands[$id])) {
                    return $commands[$id];
                }
            }
            return false;
        }
        static function setCommand($method, $params = array() )
        {
            $commands = get_option(PREFIX_BACKUP_ . "_commands");
            if ( ( $commands === false ) || !wpadm_in_array($method, 'method', $commands ) ) {
                $commands[] = array('method' => $method, 'params' => $params, 'work' => 0 );
                update_option(PREFIX_BACKUP_ . "_commands", $commands);
            }
        }   
        static function delCommand($method)
        {
            $commands = get_option(PREFIX_BACKUP_ . "_commands");
            if ($commands !== false && is_array($commands)) {
                $id = wpadm_in_array($method, 'method', $commands, true);
                unset($commands[$id]);
                if (!empty($commands)) {
                    $commands = array_values($commands);
                } else {
                    $commands = array();
                }
                update_option(PREFIX_BACKUP_ . "_commands", $commands);
            }
        }

        static function run($time = false)
        {
            if ($time) {
                $time = $time + time(); 
            } else {
                $time = time();
            }
            wp_schedule_single_event($time, 'drb_run_backup', array() );
        }

        static function stop()
        {
            wp_clear_scheduled_hook( 'drb_run_backup', array() );
        }

        static function setCommandResult($command, $work = false )
        {
            $options = get_option( PREFIX_BACKUP_ . "proccess-command" );
            $id = wpadm_in_array($command, 'command', $options, true );
            if ($options === false || $id === false ) {  
                $options[] = array('command' => $command, 'work' => 0);
                self::$command_result_id = wpadm_in_array($command, 'command', $options, true );
                self::$command_result_data = array();
                update_option(PREFIX_BACKUP_ . "proccess-command", $options);
            } else {
                if ($work) {
                    $options[$id]['work'] = 1;
                    update_option(PREFIX_BACKUP_ . "proccess-command", $options);
                }
            }
        }

        static function setCommandResultData($command, $data = array())
        {
            $path = WPAdm_Core::getTmpDir();
            file_put_contents($path ."/$command", wpadm_pack( $data ) );
        }
        static function delCommandResultData($command)
        {
            $path = WPAdm_Core::getTmpDir();
            if (!empty($path)) {
                WPAdm_Core::rmdir($path . "/$command");
            }
        }

        static function getCommandResult($command)
        {                            
            $options = get_option( PREFIX_BACKUP_ . "proccess-command" );
            if ($options !== false) {
                $id = wpadm_in_array($command, 'command', $options, true );
                if ($id !== false && $options[$id]['work'] == 1) {
                    return true;
                }
            } 
            return false;
        }
        static function getCommandResultData($command) 
        {
            $path = WPAdm_Core::getTmpDir();
            if (file_exists($path . "/$command")) {
                $data = wpadm_unpack( file_get_contents( $path . "/$command" ) ); 
                return $data;
            }

            return array();
        }
    }
}