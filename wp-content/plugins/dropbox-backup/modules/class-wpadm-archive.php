<?php
if (!defined('PCLZIP_TEMPORARY_DIR')) {
    define('PCLZIP_TEMPORARY_DIR', WPAdm_Core::getTmpDir() . '/');
}
if ( !class_exists("PclZip") ) {
    require_once dirname(__FILE__) . '/pclzip.lib.php';
}
if (!class_exists('WPAdm_Archive')) {
    class WPAdm_Archive {
        private $remove_path = '';
        private $files = array();
        /**
        * @var PclZip
        */
        private $archive;
        private $md5_file = '';
        public $error = '';

        public function __construct($file, $md5_file = '') {   
            $this->archive = new PclZip($file);
            $this->files[] = $file;
            $this->md5_file = $md5_file;
        }

        public function add($file) 
        {                    
            return $this->packed($file);    
        }
        public function packed($file)
        {
            ini_set("memory_limit", "256M");
            if ( WPAdm_Running::is_stop() ) {
                if (empty($this->remove_path)) {
                    if ( WPAdm_Running::is_stop() ) {
                        $res = $this->archive->add($file);
                    }
                } else {
                    if ( WPAdm_Running::is_stop() ) {
                        $res = $this->archive->add($file, PCLZIP_OPT_REMOVE_PATH, $this->remove_path);
                    }
                }    
                if ( WPAdm_Running::is_stop() ) {
                    if ($res == 0) {
                        WPAdm_Core::log( $this->archive->errorInfo(true) ); 
                        if (file_exists($this->md5_file)) {
                            unset($this->md5_file);
                        }
                        $this->error = $this->archive->errorInfo(true);
                        return false;
                    } 
                    $this->saveMd5($file);
                }
            }
            return true;
        }

        protected function saveMd5($file) {
            if ($this->md5_file) {
                $files = explode(',', $file); {
                    foreach($files as $f) {
                        file_put_contents($this->md5_file, $f . "\t" . @md5_file($f) . "\t" . basename($this->archive->zipname) . "\n", FILE_APPEND);
                    }
                }
            }
        }

        public function setRemovePath($remove_path) {
            $this->remove_path = $remove_path;
        }
    }
}