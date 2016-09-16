<?php
/**
* General functions 
* 
*/

if ( ! function_exists( 'wpadm_run' )) {
    function  wpadm_run($pl, $dir) {

        require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-method-class.php';
        $request_name =  $pl . '_request';
        if( isset( $_POST[$request_name] ) && ! empty ( $_POST[$request_name] ) ) {
            require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-core.php';
            WPAdm_Core::$cron = false;
			$user_ip = wpadm_getIp();
			if ($_SERVER['SERVER_ADDR'] != $user_ip && $_SERVER['HTTP_USER_AGENT'] != 'dropbox-backup user-agent') {
				WPAdm_Running::init_params_default(false);
			}
			$params = wpadm_unpack($_POST[$request_name]);
			if ( isset($params['type']) ) {
				 wpadm_class::$type = $params['type']; 
			}
            $wpadm = new WPAdm_Core($params, $pl, $dir);
            echo '<wpadm>' . wpadm_pack($wpadm->getResult()->toArray()) . '</wpadm>';
            exit;
        }
    }
}


if ( ! function_exists( 'wpadm_unpack' )) {
    /**
    * @param str $str
    * @return mixed
    */
    function wpadm_unpack( $str ) {
        return unserialize( base64_decode( $str ) );
    }
}

if ( ! function_exists('wpadm_pack')) {
    /**
    * @param mixed $value
    * @return string
    */
    function wpadm_pack( $value ) {
        return base64_encode( serialize ( $value ) ) ;
    }
}

if ( ! function_exists('wpadm_admin_notice')) {
    function  wpadm_admin_notice() {

    }
}


if ( ! function_exists('wpadm_deactivation')) {
    function  wpadm_deactivation() {
        wpadm_send_blog_info('deactivation');
    }
}


if ( ! function_exists('wpadm_uninstall')) {
    function  wpadm_uninstall() {
        wpadm_send_blog_info('uninstall');
    }
}


if ( ! function_exists('wpadm_activation')) {
    function  wpadm_activation() {
        wpadm_send_blog_info('activation');
    }
}

if ( ! function_exists('wpadm_send_blog_info')) {
    function  wpadm_send_blog_info($status) {
        $info = wpadm_get_blog_info();
        $info['status'] = $status;

        $data = wpadm_pack($info);
        $host = WPADM_URL_BASE;
        $host = str_replace(array('http://','https://'), '', trim($host,'/'));
        $socket = fsockopen($host, 80, $errno, $errstr, 30);
        fwrite($socket, "GET /wpsite/pluginHook?data={$data} HTTP/1.1\r\n");
        fwrite($socket, "Host: {$host}\r\n");

        fwrite($socket,"Content-type: application/x-www-form-urlencoded\r\n");
        fwrite($socket,"Content-length:".strlen($data)."\r\n");
        fwrite($socket,"Accept:*/*\r\n");
        fwrite($socket,"User-agent:Opera 10.00\r\n");
        fwrite($socket,"Connection:Close\r\n");
        fwrite($socket,"\r\n");
        sleep(1);
        fclose($socket);
    }
}
if (!function_exists('wpadm_getIp')) {
	function wpadm_getIp()
	{
		$user_ip = '';
        if ( getenv('REMOTE_ADDR') ){
            $user_ip = getenv('REMOTE_ADDR');
        }elseif ( getenv('HTTP_FORWARDED_FOR') ){
            $user_ip = getenv('HTTP_FORWARDED_FOR');
        }elseif ( getenv('HTTP_X_FORWARDED_FOR') ){
            $user_ip = getenv('HTTP_X_FORWARDED_FOR');
        }elseif ( getenv('HTTP_X_COMING_FROM') ){
            $user_ip = getenv('HTTP_X_COMING_FROM');
        }elseif ( getenv('HTTP_VIA') ){
            $user_ip = getenv('HTTP_VIA');
        }elseif ( getenv('HTTP_XROXY_CONNECTION') ){
            $user_ip = getenv('HTTP_XROXY_CONNECTION');
        }elseif ( getenv('HTTP_CLIENT_IP') ){
            $user_ip = getenv('HTTP_CLIENT_IP');
        }

        $user_ip = trim($user_ip);
        if ( empty($user_ip) ){
            return '';
        }
        if ( !preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $user_ip) ){
            return '';
        }
        return $user_ip;
	}
}

if ( ! function_exists('wpadm_get_blog_info')) {
    function  wpadm_get_blog_info() {
        $info = array(
        'url' => get_site_url(),
        'email' => get_option('admin_email')
        );
        $debug = debug_backtrace();
        $info['debug'] = $debug;
        $file = (is_array($debug[count($debug)-1]['args'][0]))?$debug[count($debug)-1]['args'][0][0] : $debug[count($debug)-1]['args'][0];
        preg_match("|wpadm_.*wpadm_(.*)\.php|", $file, $m); 
        $info['plugin'] = '';
        if (isset($m[1])) {
            $info['plugin'] = $m[1];
        } 

        return $info;
    }
}
if ( ! function_exists( "wpadm_set_plugin" )) {
    function wpadm_set_plugin($plugin_name = '')
    {
        if (!empty($plugin_name) && function_exists("wpadm_run")) {
            $GLOBALS['wpadm_plugin'][] = $plugin_name;
        }
    }
}
if (!function_exists('wpadm_in_array')) {
    function wpadm_in_array($value, $key, $array, $ids = false) 
    {
        if (is_array($array)) {
            foreach($array as $k => $v) {
                if (!is_array($v) && $k == $key && $v == $value) {
                    if ($ids) {
                        return $k;
                    }
                    return true;
                } elseif (is_array($v) && isset($v[$key]) && $v[$key] == $value ) {
                    if ($ids) {
                        return $k;
                    }
                    return true;
                }
            }
        }
        return false;
    }
}
if (function_exists('wpadm_getKey')) { 
    function wpadm_getKey($array, $key) 
    {
        $return = array();
        if (empty($array) && ($n = count($array)) > 0) {
            for($i = 0; $i < $n; $i++) {
                if (isset($array[$i]['key']) && is_array($key) && in_array($array[$i]['key'], $key) ) {
                    $return[] = $i; 
                } elseif(isset($array[$i]['key']) && is_string($key) ) {
                    $return[] = $i;
                }
            }
        }
        return $return;
    }
} 

