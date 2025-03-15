<?php
# OtherUtils - START

$_LOG = '';
$_START_TIME = microtime(true);
$_SAVE_LOG = false; // Don't save logs by default but each endpoint will have its own ability to override this.
$descriptor_ext = '-descriptor';

define( 'DBSCAN', 'db_scan' );
define( 'BACKUP', 'backup_db' );

function get_ip() {
    //Just get the headers if we can or else use the SERVER global
    if ( function_exists( 'apache_request_headers' ) ) {
        $headers = apache_request_headers();
    } else {
        $headers = $_SERVER;
    }

    add_to_log_section_start( 'IP Validation' );
    add_to_log( $headers, '$headers from get_ip()' );

    /**
     * From INCAP:
        There are solutions for common applications and development frameworks.
        Also, in addition to the X-Forwarded-For header, Incapsula proxies add a new header named Incap-Client-IP with the IP address
        of the client connecting to your web site. It is easier to parse the IP in this header because it is guaranteed that it contains
        only one IP while the X-Forwarded-For header might contain several IPs.
    */
    // Get the forwarded IP if it exists
    // Now, since there might be *multiple* IPs (comma-separated), need to test 'em all!
    $collected_IPs = array();

    // new header AND original header(s), now also making them case-insensitive
    $headers_with_ip = array( 'Incap-Client-IP', 'HTTP_INCAP_CLIENT_IP', 'X-Forwarded-For', 'HTTP_X_FORWARDED_FOR' );

    // some of these headers will be set by Incap
    foreach( $headers_with_ip AS $header_with_ip_key )
    {
        // since Incap headers appear to change header CaSiNg at will [29558], will need to iterate all and compare
        foreach( $headers AS $header_key => $header_value )
        {
            if ( strcasecmp( $header_key, $header_with_ip_key ) === 0 )
            {
                // if this is a comma-separated list of IPs
                if ( stripos( $header_value, ',' ) !== false )
                {
                    $ips = explode( ',', $header_value );
                    foreach( $ips AS $ip)
                    {
                        $collected_IPs[] = trim( $ip );
                    }
                }
                // original logic: single IP value
                else
                {
                    $collected_IPs[] = $header_value;
                }
            }
        }
    }

    // original logic - fallback case
    $collected_IPs[] = $_SERVER['REMOTE_ADDR'];

    $validated_IPs = array();
    foreach( $collected_IPs AS $collected_IP )
    {
      if ( version_compare( PHP_VERSION, '5.2.0', '>=' ) ) {
        if ( filter_var( $collected_IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
        {
            // store them as keys to avoid duplicates
            $validated_IPs[ $collected_IP ] = true;
        }
      }
    }

    return array_keys( $validated_IPs );
}

function add_to_log( $message = '', $title = '' ) {
    global $_LOG;

    if ( $title != '' ) {
        $_LOG .= '<p><strong>' . $title . '</strong></p>';
    }

    ob_start();
        echo '<pre>';
        print_r( $message );
        echo '</pre>';
        echo '<hr />';
        $_LOG .= ob_get_contents();
    ob_end_clean();
}

function delete_log_file()
{
    global $_FEATURECODE;

    $log_file_name = "{$_FEATURECODE}_log.php";
    if ( file_exists( $log_file_name ) ) {
        unlink( $log_file_name );
    }
}

function add_to_log_section_start( $section_name )
{
    global $_LOG;

    $color = '#FFF';
    switch( $section_name )
    {
        case 'CheckFeatures':
        $color = '#AAF';
        break;

        case 'RemoteApi':
        $color = '#FAF';
        break;

        case 'GrabAndZip':
        case 'BackupGrabAndZip':
        $color = '#AFA';
        break;

        case 'UnzipAndApply':
        case 'BackupUnzipAndApply':
        $color = '#FFA';
        break;

        case 'IP Validation':
        $color = '#AAA';
        break;

        case 'Encryption':
        $color = '#AFF';
        break;
    }

    $_LOG .= "<div style='background-color:{$color}'>";
    $_LOG .= "<p>{$section_name}</p>";
}

// saves the log... not so sure about function name
function send_email( $message = '' ) {
    global $_SAVE_LOG, $_FEATURECODE;

    $log_file_name = "{$_FEATURECODE}_log.php";

    // if URL is Staging, always log since this is where we'd test and review logs frequently
    if ( preg_match( '/mapi.[a-z]+.dev[\d]?.sitelock.com/', API_URL ) == 1 )
    {
        $_SAVE_LOG = true;
    }

    if ( $_SAVE_LOG ) {

        // first, remove previous log file
        if ( file_exists( $log_file_name ) ) {
            unlink( $log_file_name );
        }

        // next, create the log file again
        $log = fopen( $log_file_name, "w" );

        // add logging data to the log file
        fwrite( $log, $message );

        // close the log file
        fclose( $log );
    }
}


function get_our_path() {
    $parts = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $parts);
}

function delete_unique_directory( $path = false )
{
    global $_UNIQUE, $descriptor_ext;

    $deleted_items = 0;

    if ( !$path )
    {
        $path = get_our_path('.', ".$_UNIQUE" );
    }

    if ( is_dir( $path ) )
    {
        // check for files in our $_UNIQUE
        $descriptor_file = glob( $path . DIRECTORY_SEPARATOR . '*.zip' . $descriptor_ext );
        if ( isset( $descriptor_file[0] ) && is_file( $descriptor_file[0] ) && file_exists( $descriptor_file[0] ) )
        {
            unlink( $descriptor_file[0] );
            $deleted_items++;
            add_to_log( $descriptor_file[0], 'delete_unique_directory - unlink( $descriptor_file[0] );');
        }
        $zip_chunks = glob( $path . DIRECTORY_SEPARATOR . '*.zip.[0-9]*' );
        if ( is_array( $zip_chunks ) )
        {
            foreach ( $zip_chunks as $file )
            {
                // delete file
                if ( is_file( $file ) && file_exists( $file ) )
                {
                    unlink( $file );
                    $deleted_items++;
                    add_to_log( $file, 'delete_unique_directory - file chunk');
                }
            }
        }


        // @TODO remove this eventually
        // old logic - for unchunked zip file
        {
            $zip_files = glob( $path . DIRECTORY_SEPARATOR . '[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][12][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9].zip' );
            if ( is_array( $zip_files ) )
            {
                foreach ( $zip_files as $file ) {
                    // delete file
                    $deleted_items++;
                    unlink( $file );
                }
            }
        }

        // check for any csv files that were not successfully zipped - those contain raw data and gotta be clenaup up for good!
        $raw_CSVs = glob( $path . DIRECTORY_SEPARATOR . '*-[12][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9].csv' );
        if ( is_array( $raw_CSVs ) )
        {
            foreach ( $raw_CSVs as $file )
            {
                // delete file
                if ( is_file( $file ) && file_exists( $file ) )
                {
                    unlink( $file );
                    $deleted_items++;
                    add_to_log( $file, 'delete_unique_directory - raw csv');
                }
            }
        }

        // now that all files are deleted we can delete the directory
        rmdir( $path );
        $deleted_items++;
        add_to_log( $path, 'delete_unique_directory - rmdir( $path )');
    }

    return $deleted_items;
}


// Adding this function to drop files left in /tmp since upgrade to chunking, when API stopped calling 'cmd=complete'
// @TODO: remove & stop calling this some time in the future
function cleanup_old_tmp_trash()
{
    global $_UNIQUE, $_SAVE_LOG;

    $cleanups_count = 0;

    $dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

    // (1) Cleanup older bullet files
    $glob_bullet_1_char = '[0-9a-f]';
    $glob_file_name_pattern = str_repeat( $glob_bullet_1_char, 32 );
    $files = glob( $dir . $glob_file_name_pattern . '.php' );
    if ( is_array( $files ) )
    {
        foreach ( $files as $file )
        {
            // skip our own bullet
            if ( $file == __FILE__ )
            {
                continue;
            }

            // check file
            if ( is_file( $file ) && file_exists( $file ) && !file_was_recently_modified( $file ) )
            {
                // make sure file content is what we expect and not user's file that happened to have matching name
                $fh = fopen( $file, 'r' );
                $line1 = trim( fgets( $fh ) );
                $line2 = trim( fgets( $fh ) );
                fclose( $fh );

                if (
                    $line1 == '<?php' && // starts with opning php
                    (
                        $line2 == '# OtherUtils - START' || // new bullet format
                        $line2 == '# HTTP - START'          // old bullet format
                    )
                ) {
                    unlink( $file );
                    $cleanups_count++;
                    add_to_log( $file, 'cleanup_old_tmp_trash (1)');
                }

            }
        }
    }

    // (1.1) Cleanup lock files for older bullets
    $files = glob( $dir . $glob_file_name_pattern . '.php.lock' );
    if ( is_array( $files ) )
    {
        foreach ( $files as $file )
        {
            // skip our own bullet's lock file
            if ( $file == __FILE__ . '.lock' )
            {
                continue;
            }

            // check file
            if ( is_file( $file ) && file_exists( $file ) && !file_was_recently_modified( $file ) )
            {
                // make sure file content is what we expect and not user's file that happened to have matching name
                $fh = fopen( $file, 'r' );
                $line1 = trim( fgets( $fh ) );
                $line2 = fgets( $fh );
                fclose( $fh );

                if (
                    !$line2 && // file has only one line
                    is_numeric( $line1 ) && // we store numeric timestamp,
                    (int) $line1 > 1000000000 // check if it looks like a timestamp of value after 2001-ish
                ) {
                    unlink( $file );
                    $cleanups_count++;
                    add_to_log( $file, 'cleanup_old_tmp_trash (1.1)');
                }

            }
        }
    }

    // (2) Cleanup: older key filesand directories
    $paths = glob( $dir . '.' . $glob_file_name_pattern );
    if ( is_array( $paths ) )
    {
        foreach ( $paths as $path )
        {
            //skip our own bullet's temp dir
            if ( $path == realpath( get_our_path('.', ".$_UNIQUE") ) )
            {
                continue;
            }

            if ( file_exists( $path ) )
            {
                // (2.1) Cleanup: older key files (name format: .[32 hex chars])
                if ( is_file( $path ) && !file_was_recently_modified( $path ) )
                {
                    // make sure file content is what we expect and not user's file that happened to have matching name
                    $fh = fopen( $path, 'r' );
                    $line1 = trim( fgets( $fh ) );
                    $line2 = fgets( $fh );
                    fclose( $fh );

                    if (
                        !$line2 && // file has only one line
                        ( $delim_pos = strpos( $line1, ':' ) ) !== false && // parameter after ":" decodes as hex
                        preg_match( '/[0-9a-f]+/', base64_decode( substr( $line1, $delim_pos +1 ) ) ) == 1  // looks like encoded key
                    ) {
                        unlink( $path );
                        $cleanups_count++;
                        add_to_log( $path, 'cleanup_old_tmp_trash (2.1)');
                    }

                }

                // (2.2) Cleanup: older directories (name format: .[32 hex chars] as well)
                if ( is_dir( $path ) && !file_was_recently_modified( $path . DIRECTORY_SEPARATOR . '.' ) )
                {
                    $cleanups_count += delete_unique_directory( $path );
                }
            }
        }
    }

    // (3) Cleanup: zip files for restore that were never removed.
    // Safe to remove them all as long as we only cann this from Grab and Zip,
    // so valid uploaded zips for Unzip and Apply should all processed and removed by now.
    $zip_paths = glob( $dir . $glob_file_name_pattern . '.zip' );
    if ( is_array( $zip_paths ) )
    {
        foreach ( $zip_paths as $file )
        {
            // check file
            if ( is_file( $file ) && file_exists( $file ) && !file_was_recently_modified( $file ) )
            {
                unlink( $file );
                $cleanups_count++;
                add_to_log( $file, 'cleanup_old_tmp_trash (3)');
            }
        }
    }

    // if anything was cleaned up, log everything for review
    if ( $cleanups_count > 0 )
    {
        $_SAVE_LOG = true;
    }
}

// Helper function to determine if file was modified recently
// anything less than 6 hours will be considered recent/active and will not be touched
function file_was_recently_modified( $path, $how_old_is_old = 21600 )
{
    global $_START_TIME;

    // check time difference between bullet creation and file modification
    $time_diff = $_START_TIME - filemtime( $path );
    if ( $time_diff < $how_old_is_old )
    {
        return true;
    }
    return false;
}

function try_json_decode( $string )
{
    if (
        // pure number will be JSON-encoded w/o any changes, so need to check for explicit JSON delimiters:
        ( substr( $string, 0, 1 ) == '{' || substr( $string, 0, 1 ) == '[' ) &&
        // if parsing fails, error will be recorded
        ( $parsed_string = json_decode( $string, true, 2, JSON_BIGINT_AS_STRING ) ) && json_last_error() === JSON_ERROR_NONE
    ) {
        return $parsed_string;
    }
    else
    {
        return $string;
    }
}

function log_bullet_run_time()
{
    global $_START_TIME;

    $time = round( microtime(true) - $_START_TIME, 2 );

    add_to_log( $time, 'Bullet run time, seconds.' );

    return $time;
}

function obfuscate( $value, $length = 3, $replacement = '***' )
{
    return substr( $value, 0, $length ) . $replacement;
}
# OtherUtils - END
# HTTP - START
define('API_URL', 'https://mapi.sitelock.com/v3/connect/' );
define('MAPI_CURL_CONNECT_TIMEOUT', '3' );
define('MAPI_CURL_RESPONSE_TIMEOUT', '10' );



const LOG_MAPI_NONE = 0;
const LOG_MAPI_REQUEST = 1;
const LOG_MAPI_ALL = 2;

$CURL_INIT_ERR = false;
$CURL_MAPI_ERR = false;

function mapi_post( $token, $action, $params, $log_level = LOG_MAPI_ALL ) {
    global $_SAVE_LOG;

    if ( !is_array($params)) {
        die('_bad_post_params');
    }

    $request = array(
        'pluginVersion'    => '100.0.0',
        'apiTargetVersion' => '3.0.0',
        'token'            => $token,
        'requests'         => array(
            'id'     => md5(microtime()) . '-' . implode('', explode('.', microtime(true))),
            'action' => $action,
            'params' => $params,
        ),
    );

    $rjson = json_encode($request);

    // json must be base64 encoded
    $rjson = base64_encode( $rjson );

    if ( $log_level >= LOG_MAPI_REQUEST ) {
        add_to_log(API_URL, 'mapi_post URL');
        // hide token from log
        $request_cleaned = $request;
        $request_cleaned['token'] = obfuscate( $request_cleaned['token'] );
        add_to_log($request_cleaned, 'mapi_post_request');
    }

    $return = curl_post( API_URL, $rjson );
    if( !isset( $return->status ) || $return->status != 'ok' ) {
        $_SAVE_LOG = true;
    }

    if ( $log_level == LOG_MAPI_ALL ) {
        // clean up tokens from response
        $return = str_replace( $token, obfuscate($token), $return );
        add_to_log('<textarea style="width:99%;height:100px;">' . $return . '</textarea>', 'mapi_response');
    }

    return $return;
}

function curl_post( $url, $postbody, $log_level = LOG_MAPI_ALL ) {
    global $CURL_INIT_ERR, $CURL_MAPI_ERR;

    if ( ($disabled_functions=test_curl_available()) !== true )
    {
        $CURL_INIT_ERR = true;
        add_to_log( 'FALSE', 'test_curl_available() returned the following disabled cURL functions: ' . implode(', ', $disabled_functions));
        return false;
    }
    else
    {
        $CURL_INIT_ERR = false;
    }

    $ch = curl_init( $url );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postbody);

    // control timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, MAPI_CURL_CONNECT_TIMEOUT);
    curl_setopt($ch, CURLOPT_TIMEOUT, MAPI_CURL_RESPONSE_TIMEOUT);

    $ret = curl_exec($ch);

    // capture and store error globaly if return looks like a failure
    if($ret === false)
    {
        $CURL_MAPI_ERR = curl_error($ch);
    }
    // otherwise, clean up the error
    else
    {
        $CURL_MAPI_ERR = false;

        if ( $log_level >= LOG_MAPI_REQUEST ) {
            $info = curl_getinfo($ch);
            add_to_log($info, 'curl_getinfo()');
        }
    }

    curl_close($ch);

    return $ret;
}

function test_curl_available()
{
    if ( !extension_loaded('curl') )
    {
        return ['cURL Extension'];
    }

    $function_names = [ 'curl_init', 'curl_exec', 'curl_post', 'curl_setopt', 'curl_error', 'curl_getinfo', 'curl_close' ];
    $disabled_functions = [];
    foreach($function_names AS $function_name)
    {
        if ( !function_exists($function_name) )
        {
            $disabled_functions[] = $function_name;
        }
    }

    return empty($disabled_functions) ? true : $disabled_functions;
}

// Complete simple self-test initited by API.
// Get a response - bullet is reachable via HTTP
function test_bullet_is_reachable()
{
    if ( $_GET[ 'cmd' ] == 'test' )
    {
        die( json_encode( array( 'status' => 'ok' ) ) );
    }
}
# HTTP - END
# Encryption - START

add_to_log_section_start( 'Encryption' );
add_to_log( defined('PHP_VERSION') ? PHP_VERSION : phpversion(), 'PHP Version');

define( 'OPENSSL', 'OpenSSL' );
define( 'MCRYPT', 'MCrypt' );
define( 'CRYPTOR', establish_cryptor() );

// only applies to MCrypt, which is deprecated as of PHP 7.1, but still in use by some clients
define( 'ENCRYPT_DEFAULT_MODE', establish_default_mode() );

$_ENCRYPT_USE_CIPHER = establish_default_cipher();
$_ENCRYPT_USE_CIPHER_KEY = null;
$_ENCRYPT_USE_CIPHER_IV = null;

check_internal_encoding();

function establish_cryptor()
{
    if (
        function_exists('openssl_cipher_iv_length') &&
        function_exists('openssl_get_cipher_methods') &&
        function_exists('openssl_encrypt') &&
        function_exists('openssl_decrypt')
    ) {
        add_to_log( OPENSSL, 'Cryptor');
        return OPENSSL;
    } else if (
        function_exists('mcrypt_get_iv_size') &&
        function_exists('mcrypt_get_key_size') &&
        function_exists('mcrypt_list_algorithms') &&
        function_exists('mcrypt_encrypt') &&
        function_exists('mcrypt_decrypt')
    ) {
        add_to_log( MCRYPT, 'Cryptor');
        return MCRYPT;
    } else {
        update_scan_on_error( 'CHECK_FEATURE_ERR_NO_CRYPTO', array( 'encfail' => 'establish_cryptor' ) );
    }
}

function establish_default_cipher()
{
    if ( CRYPTOR === OPENSSL ) {
        $algorithms = openssl_get_cipher_methods(true);

        // In the order of preference, we want to use AES-CBC from 256 down to 128.
        // bf-cbc is here to preserve bullet's legacy logic, although PERL's original logic is to use blowfish, so including it first.
        $preferred_algos = [ 'aes-256-cbc', 'aes-192-cbc', 'aes-128-cbc', 'blowfish', 'bf-cbc' ];

        foreach( $preferred_algos AS $preferred_algo )
        {
            if ( in_array( $preferred_algo, $algorithms ) )
            {
                add_to_log( $preferred_algo, 'Default Cipher');
                return $preferred_algo;
            }
        }

        // otherwise we can't proceed: PERL can't use arbitrary cipher if it doesn't expect it
        update_scan_on_error( 'CHECK_FEATURE_ERR_NO_CRYPTO', array( 'encfail' => 'None of preferred cryptors found in establish_default_cipher' ) );

    } else if ( CRYPTOR === MCRYPT ) {

        $algorithms = mcrypt_list_algorithms();
        // see if we can use blowfish
        if ( in_array( MCRYPT_BLOWFISH, $algorithms ) )
        {
            add_to_log( MCRYPT_BLOWFISH, 'Cipher');
            return MCRYPT_BLOWFISH;
        }
        // otherwise use the first one from the list
        else
        {
            add_to_log( "Will use {$algorithms[0]} from " . json_encode($algorithms), 'MCrypt "blowfish" not found!' );
            return $algorithms[0];
        }
    } else {
        update_scan_on_error( 'CHECK_FEATURE_ERR_NO_CRYPTO', array( 'encfail' => 'unknown cryptor "' . CRYPTOR . '" in establish_default_cipher' ) );
    }
}

function establish_default_mode()
{
    if ( CRYPTOR === OPENSSL ) {
        // mode not used by OpenSSL
        return 0;
    } else if ( CRYPTOR === MCRYPT ) {
        // see if we can use mcrypt constant
        if ( defined( MCRYPT_MODE_CBC ) )
        {
            add_to_log( MCRYPT_MODE_CBC, 'Mode');
            return MCRYPT_MODE_CBC;
        }
        // otherwise try to manually use 'CBC';
        else
        {
            add_to_log( "Manually set mode to cbc", 'MCRYPT_MODE_CBC not found!' );
            return 'cbc';
        }
    } else {
        update_scan_on_error( 'CHECK_FEATURE_ERR_NO_CRYPTO', array( 'encfail' => 'establish_default_mode' ) );
    }
}

function get_encryption_info() {

    global $_TOKEN, $_SITEID, $_SINGLEID, $_ENCRYPT_USE_CIPHER, $_ENCRYPT_USE_CIPHER_KEY, $_ENCRYPT_USE_CIPHER_IV;

    if ( $_ENCRYPT_USE_CIPHER && $_ENCRYPT_USE_CIPHER_KEY && $_ENCRYPT_USE_CIPHER_IV)
    {
        return [
            0 => $_ENCRYPT_USE_CIPHER,
            1 => $_ENCRYPT_USE_CIPHER_KEY,
            2 => $_ENCRYPT_USE_CIPHER_IV,
        ];
    }

    $payload = array(
        'site_id' => $_SITEID,
        'queue_id' => $_SINGLEID
    );

    $raw_response = mapi_post(
        $_TOKEN,
        's3_get_enc_info',
        $payload,
        LOG_MAPI_REQUEST
    );

    $get_encryption_info_response = json_decode( $raw_response, true );

    // Only continue if status is successful
    if (
        isset( $get_encryption_info_response['responses'][0]['data']['s3_status'] ) &&
        $get_encryption_info_response['responses'][0]['data']['s3_status'] == 'ok'
    )
    {
        $data = $get_encryption_info_response['responses'][0]['data'];
        $cipher = $data['cipher'];
        $key    = base64_decode($data['cipher_key']);
        $iv     = base64_decode($data['cipher_iv']);

        add_to_log( [
            'cipher' => $cipher,
            'key' => obfuscate( $data['cipher_key'] ),
            'iv' => obfuscate( $data['cipher_iv'] )
        ], 'Received encryption details');

        switch( CRYPTOR ) {
            case OPENSSL :
                $iv  = str_pad('', openssl_cipher_iv_length( $cipher ), $iv );
                break;
            case MCRYPT :
                $iv  = str_pad('', mcrypt_get_iv_size( $cipher, ENCRYPT_DEFAULT_MODE), $iv );
                $key = str_pad('', mcrypt_get_key_size($cipher, ENCRYPT_DEFAULT_MODE), $key);
                break;
            default:
                update_scan_on_error( 'CHECK_FEATURE_ERR_NO_CRYPTO', array( 'encfail' => '2 (Unknown cryptor: ' . CRYPTOR . ')' ) );
        }

        // cache values
        $_ENCRYPT_USE_CIPHER = $cipher;
        $_ENCRYPT_USE_CIPHER_KEY = $key;
        $_ENCRYPT_USE_CIPHER_IV = $iv;

        // New in [SE-957]: now also returning cipher
        return array(
            0 => $cipher,
            1 => $key,
            2 => $iv,
        );
    }
    else
    {
        update_scan_on_error( 'ENCRYPTION_FAILED', array( 'encfail' => '6 (Problem with get_enc_info call)' ) );
    }

}


function encrypt_string( $string ) {
    global $_FEATURECODE;

    list($cipher, $key, $iv) = get_encryption_info();

    if ( CRYPTOR === OPENSSL ) {
        // 1) Backup:
        if ( $_FEATURECODE == BACKUP )
        {
            // Prior to PHP 5.4, $options param was boolean raw_data with "true" equivalent to the new flag
            // https://stackoverflow.com/questions/24707007/using-openssl-raw-data-param-in-openssl-decrypt-with-php-5-3
            // OPENSSL_RAW_DATA flag takes care of returning raw encoded data, so we no longer need to take 2 extra steps
            // to base64-decode string that was just being base64-encoded automatically by openssl_encrypt.
            $options = defined( OPENSSL_RAW_DATA ) ? OPENSSL_RAW_DATA : 1;
            return openssl_encrypt($string, $cipher, $key, $options, $iv);
        }
        // 2) DB Scan
        // API still sends data with padding so we'll keep the original logic
        // 2.1) NEW DB Scan:
        else if ( $_FEATURECODE == DBSCAN )
        {
            $options = defined( OPENSSL_RAW_DATA ) ? OPENSSL_RAW_DATA : 1;
            return openssl_encrypt($string, $cipher, $key, $options, $iv);
        }
    }

    if ( CRYPTOR === MCRYPT ) {
        $mode = ENCRYPT_DEFAULT_MODE;
        return mcrypt_encrypt($cipher, $key, $string, $mode, $iv);
    }

    update_scan_on_error( 'CHECK_FEATURE_ERR_NO_CRYPTO', array( 'encfail' => '4 (encrypt_string)' ) );
}

function decrypt_string( $string ) {
    global $_FEATURECODE;

    list($cipher, $key, $iv) = get_encryption_info();

    if ( CRYPTOR === OPENSSL ) {
        // 1) Backup:
        if ( $_FEATURECODE == BACKUP )
        {
            // Using same OPENSSL_RAW_DATA flag as in encrypt_string above to make encryption and decryption function symmetrically
            $options = defined( OPENSSL_RAW_DATA ) ? OPENSSL_RAW_DATA : 1;
            return openssl_decrypt($string, $cipher, $key, $options, $iv);
        }
        // 2) DB Scan
        // 2.1) NEW DB Scan
        else if ( $_FEATURECODE == DBSCAN )
        {
            // Using same OPENSSL_RAW_DATA flag as in encrypt_string above to make encryption and decryption function symmetrically
            $options = defined( OPENSSL_RAW_DATA ) ? OPENSSL_RAW_DATA : 1;
            return openssl_decrypt($string, $cipher, $key, $options, $iv);
        }
    }

    if ( CRYPTOR === MCRYPT ) {
        $mode = ENCRYPT_DEFAULT_MODE;
        return mcrypt_decrypt($cipher, $key, $string, $mode, $iv);
    }

    update_scan_on_error( 'CHECK_FEATURE_ERR_NO_CRYPTO', array( 'encfail' => '5 (decrypt_string)' ) );
}

function check_internal_encoding()
{
    if ( function_exists( 'mb_internal_encoding' ) )
    {
        add_to_log( mb_internal_encoding(), 'mb_internal_encoding' );
    }
    else
    {
        add_to_log( 'Not available, possibly no mbstring extension.', 'mb_internal_encoding' );
    }
}
# Encryption - END
/**
 * MAIN point of entry for Wordpress
 */
function import_WP_creds()
{
    $localdir = get_bullet_location();

    if ( file_exists( $localdir . 'wp-config.php' )  ) {
        $file  = file_get_contents( $localdir . 'wp-config.php' );
    }else{
        // check one level up just like in wp-load.php. If wp-settings.php exists, then that is a separate WP install not to be used
        if ( @file_exists( dirname( $localdir ) . '/wp-config.php' )   && !@file_exists( dirname( $localdir ) . '/wp-settings.php' ) ) {
            $file  = file_get_contents( dirname( $localdir . $extradir ) . '/wp-config.php' );
        }else{

            // check for hard-coded subdir paths in index.php. Only check for this if standard config fails
            $hard_path ='';
            if ( @file_exists( $localdir . 'index.php' )  ) {
                $lines = file( $localdir . 'index.php' );
                foreach ( $lines as $line ) {
                    $end_pos = strpos( $line, '/wp-blog-header');
                    if ( $end_pos ){
                        $start_pos = strpos( $line, '/' );
                        $tok_len = $end_pos - $start_pos;
                        // extract path token without slashes
                        $hard_path = substr( $line, $start_pos + 1, $tok_len - 1 );
                        break;
                    }

                }
                if ( $hard_path ){
                    if ( @file_exists( $localdir . $hard_path.'/wp-config.php' )  ) {
                        $file  = file_get_contents( $localdir . $hard_path. '/wp-config.php' );
                    }else{
                        // no config detected on any known path
                        update_scan_on_error( 'DB_SCAN_NO_CONFIG_FOUND', array( 'get-config' => array( 'localdir' => $localdir, 'hard_path' => $hard_path ) ) );
                    }
                }
            }
        }
    }


    $tokens = array();

    foreach ( token_get_all($file) as $tok ) {
        if ( is_array($tok) && in_array( $tok[0], array( T_COMMENT, T_DOC_COMMENT, T_WHITESPACE, T_OPEN_TAG ))) {
            continue;
        }
        $tokens[] = $tok;
    }


    for ($i = 0, $tc = count($tokens); $i < $tc; ++$i ) {
        if (!is_array($t = $tokens[$i])) {
            continue;
        }

        switch($t[0]) {
            case T_STRING:
                if (strtolower($t[1]) != 'define') {
                    break;
                }

                if (   !is_array($tokens[++$i])
                    && $tokens[$i] == '('
                    && is_array($tokens[++$i])
                    && $tokens[$i][0] == T_CONSTANT_ENCAPSED_STRING
                    && in_array( ( $cur = clearString( $tokens[$i][1] ) ), array( 'DB_HOST', 'DB_USER', 'DB_PASSWORD', 'DB_NAME' )  )
                    && $tokens[++$i] == ','
                    && is_array($tokens[++$i])
                    && $tokens[$i][0] == T_CONSTANT_ENCAPSED_STRING
                ) {
                        //print "Found $cur = " . clearString($tokens[$i][1]) . "\n";
                        define( $cur, clearString($tokens[$i][1]) );
                }

                // check for multisite setting with bool param, not string like above. 319 is the token type of param here.
                if ( clearString( $tokens[$i][1] ) == 'WP_ALLOW_MULTISITE'
                    &&$tokens[++$i] == ','
                    && is_array($tokens[++$i] )
                    &&  $tokens[$i][1]  == 'true'
                    ) {
                        define( 'WP_ALLOW_MULTISITE', true );
                    }

                break;
            case T_VARIABLE:
                if ( $t[1] != '$table_prefix' ) {
                    break;
                }

                if (
                    !is_array($tokens[++$i])
                    && $tokens[$i] == '='
                    && is_array($tokens[++$i])
                    && $tokens[$i][0] == T_CONSTANT_ENCAPSED_STRING
                ) {
                    //print "Found table_prefix as " . clearString($tokens[$i][1]) . "\n";
                    define( 'DB_PREFIX', clearString($tokens[$i][1]) );
                }

                break;
        }
    }


    if ( !( defined('DB_HOST') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_NAME') ) ){
        $wpt = token_get_all( $file );
        $wpn = '';
        foreach ( $wpt as $index => $t ) {
            switch (true) {
                case !is_array($t):
                    $wpn .= $t;
                case in_array($t[0], array(T_INLINE_HTML, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO)):
                    break;
                case in_array($t[0], array(T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE, T_REQUIRE_ONCE, T_RETURN, T_EXIT, T_EVAL)):
                // Commenting out boolean in the middle of define was causing 500 error - not good!!
                // Example: define( "WP_DEBUG", //false );
                case $t[0] == T_STRING && !in_array( $t[1], array( 'true','false' ) ) and ( strtolower($t[1]) == 'header' || !function_exists($t[1]) ):
                    // Also, if previous token was a silence @, we need to add comment *before* that.
                    if ( isset( $wpt[$index-1] ) && $wpt[$index-1] == '@' ) {
                        $wpn = substr( $wpn, 0, strlen()-2 ) . '//@';
                    } else {
                        $wpn .= '//';
                    }
                default:
                    $wpn .= $t[1];
            }
        }
        eval( $wpn );

        // I've seen case of host being completely commented out, which will result in a token value of "DB_HOST" which is wrong
        // Having no host value likely means use localhost
        if ( DB_HOST == 'DB_HOST' ) {
            define( 'DB_HOST', 'localhost' );
        }
    }

}


// we need this static class/function because closure syntax is unsupported until PHP 5.3
class My_callback {
  public function __construct() {
  }

  function callback( $matches ){  return stripslashes( $matches[0] );   }
}


function clearString($sample, $preserve_quotes = false) {
    if (!is_string($sample) || strlen($sample) < 1 || (!$preserve_quotes && ( $sample == "''" || $sample == '""' ) )) {
        return '';
    }

    if ( strlen($sample) > 1 && $sample[0] == "'" ) {
        if (!$preserve_quotes) {
            $sample = substr($sample, 1, -1);
        }
        return str_replace(array('\\\'', '\\\\'), array("'", '\\'), $sample);
    }

    if (!$preserve_quotes && strlen($sample) > 1 && $sample[0] == '"' && substr($sample, -1, 1) == '"') {
        $sample = substr($sample, 1, -1);
    }
    // preg_replace with \e modifier is deprecated (17945)
    //return preg_replace('/(\\\\(?:x[0-9a-f]{1,2}|[0-7]{1,3}|[nrtvef\\\\$\'"]))/e', "eval('return stripslashes(<<<CLEARSTRING\n\\1\nCLEARSTRING\n);');", $sample);
    // closure syntax is too new so using static class member instead
    $c = new My_callback();
    return preg_replace_callback('/(\\\\(?:x[0-9a-f]{1,2}|[0-7]{1,3}|[nrtvef\\\\$\'"]))/',
                                array( $c, 'callback')
                                 , $sample);

}
# WPCredentialImport - END
# JoomlaCredentialImport - START

/**
 * MAIN point of entry for Joomla!
 */
function import_Joomla_creds()
{
    $localdir = get_bullet_location();

    $config_file_name =  'configuration.php';

    if ( file_exists( $localdir . $config_file_name ) )
    {
        include_once( $localdir . $config_file_name  );
    }
    else if ( file_exists( $localdir . 'tmp/' . $config_file_name  )  )
    {
        include_once( $localdir . 'tmp/' . $config_file_name  );
    }
    else
    {
        // no config detected on any known path
        update_scan_on_error( 'DB_SCAN_NO_CONFIG_FOUND', array( 'get-config' => $localdir ) );
    }

    // Super easy, thanks Joomla!!
    $config = new JConfig();

    ! defined('DB_HOST')     && define( 'DB_HOST',      $config->host     );
    ! defined('DB_USER')     && define( 'DB_USER',      $config->user     );
    ! defined('DB_PASSWORD') && define( 'DB_PASSWORD',  $config->password );
    ! defined('DB_NAME')     && define( 'DB_NAME',      $config->db       );
    ! defined('DB_PREFIX')   && define( 'DB_PREFIX',    $config->dbprefix );
    ! defined('DB_TYPE')     && define( 'DB_TYPE',      $config->dbtype   );

}
# JoomlaCredentialImport - END
# GenericCredentialImport - START


/**
 * Import Generic Creds - wrapper function to handle the step and init DB constants once info is available
 */
function import_Generic_creds()
{
    global $_FEATURECODE;

    if ( $_GET[ 'cmd' ] == 'db_creds_ready' && ( $enc_db_creds = $_GET[ 'enc_db_creds' ] ) != '' )
    {
        // STEP 2
        $decoded_db_creds = decode_generic_DB_creds( $enc_db_creds );

        ! defined('DB_HOST')     && define( 'DB_HOST',      $decoded_db_creds[ 'db_host' ] );
        ! defined('DB_USER')     && define( 'DB_USER',      $decoded_db_creds[ 'db_user' ] );
        ! defined('DB_PASSWORD') && define( 'DB_PASSWORD',  $decoded_db_creds[ 'db_pw' ]   );
        // db name only known in DB Scan case
        if ( $_FEATURECODE == DBSCAN ) {
            ! defined('DB_NAME')     && define( 'DB_NAME',  $decoded_db_creds[ 'db_name' ] );
        } else {
            ! defined('DB_NAME')     && define( 'DB_NAME',  null );
        }
        // no prefix for generic
        ! defined('DB_PREFIX')   && define( 'DB_PREFIX',    null );
    }
    else
    {
        // STEP 1 - function will terminate execution and return JSON to the caller
        handle_s3_init( true );
    }
}


/**
 * Function will accept encoded creds string and attempt to decode it into [db_host, db_user, db_pw, db_name]
 */
function decode_generic_DB_creds( $enc_db_creds )
{
    $contents = base64_decode( $enc_db_creds );

    // some invalid unprintable character was returned at the end of the strings, breaking json_decode
    $dec_string = trim( decrypt_string($contents) );

    // If string contains any non-ASCII characters, they will be double-encoded! Decode them into valid UTF-8 charactres.
    // Before, we used to trim those characters out, altering the values!
    if ( preg_match( '/[^ -~]/', $dec_string ) !== false )
    {
        $dec_string = utf8_decode( $dec_string );
    }

    return json_decode( $dec_string, true );
}
# GenericCredentialImport - END
define('MAX_ROWS_PER_QUERY', 100);
define('MAX_ROWS_TABLE', 2500);

define('ACTION_DEL', 'delete');
define('ACTION_UPD', 'update');
define('ACTION_RES', 'restore');

# Database - START
function die_enc_db($str = '') {
    GLOBAL $statuses;
    update_scan_on_error( 'DATABASE_GENERAL_ERROR', $str, false);

    $statuses['db'] = false;
    $statuses['errors']['db'] = array( 'code' => 'DATABASE_GENERAL_ERROR', 'message' => $str );

    end_bullet_and_respond($statuses);
}

/**
 * @var Mysql_Base
 */
$db = null;
$cached_table_info = array();

/**
 * @return Mysql_Base $db
 */
function getDbObj( $exception_on_failure = false ) {
    /**
     * @var Mysql_Base
     */
    global $db;

    // we can cache DB since it's global anyway
    if ( $db === null )
    {
        // we try newer MySQLi first every time, capturing any forced init exceptions...
        try {
            $db = new Mysql_New( true );
        }
        // ...then fall back to the older MySQL
        catch ( Exception $ex ) {
            $db = new Mysql_Old( $exception_on_failure );
        }
    }

    return $db;
}


function getTableAndIdCol($table = null, $idcol = null) {
    global $db;
    if (!is_a($db, 'Dbobj_all')) {
        die_enc_db('db_not_def');
    }

    $table = is_null($table) ? getSuper('table') : $table;
    $idcol = is_null($idcol) ? getSuper('idcol') : $idcol;

    $tdat  = $db->table_info($table);
    if (!$tdat || (is_array($tdat) && count($tdat) < 1)) {
        die_enc_db('bad_table');
    }

    if (!isset($tdat['cols'][$idcol])) {
        if ( isset($tdat['idcol']) && !empty($tdat['idcol']) ) {
            $idcol = $tdat['idcol'];
        } else {
            die_enc_db('cannot_find_idcol');
        }
    }

    return array($table, $idcol, $tdat);
}

class Dbobj_all {

}

class Mysql_Base extends Dbobj_all {
    var $link;
    var $result_set = null;
    var $buffered = true;

    function list_tables( $prefix = null ) {
        global $_PLATFORM;

        if ( $prefix ) {
            // "_" used in most prefixes is a special character in SQL, so needs escaping
            $listq = $this->_query('show tables LIKE "'.str_replace('_', '\_', $prefix).'%"', $this->link);
            add_to_log( $prefix, 'listing platform-specific tables with provided prefix' );
        } else {
            $listq = $this->_query('show tables', $this->link);
        }

        if ( $this->_generic_error_check( $listq ) ) {
            return false;
        }

        // We already checked for errors, so if we have no rows, this means DB has no tables!
        if ( $listq->num_rows === 0 )
        {
            return false;
        }

        $final = array();

        while ($table = $this->_fetch_row($listq)) {
            $table = $table[0];
            $table_info = $this->table_info( $table );
            // only add table if its info pull succeeded
            if ( !empty( $table_info ) )
            {
                $final[$table] = $table_info;
            }
        }
        add_to_log( array_keys($final), "retrieved info about ".count($final)." \"{$_PLATFORM}\" tables" );

        return $final;
    }


    /**
     * Function lists all available tables to find distinct prefixes that are used with possibly multiple WP installations
     * @return array
     */
    function find_distinct_WP_prefixes() {

        $multisite_candidates = [];
        $distinct_prefixes_in_use = [];

        $testable_tables = ['comments','posts','users'];

        $listq = $this->_query('show tables', $this->link);

        if ( $this->_generic_error_check( $listq ) ) {
            return [];
        }

        // We already checked for errors, so if we have no rows, this means DB has no tables!
        if ( $listq->num_rows === 0 )
        {
            return [];
        }

        // iterate tables and extract table prefixes
        while ($table = $this->_fetch_row($listq)) {
            $table = $table[0];

            if( preg_match("/^(wp_[0-9a-zA-Z]*)(".implode('|',$testable_tables).")$/", $table, $matches) && count($matches) === 3 ){
                $prefix = $matches[1];
                $table_name = $matches[2];

                if (!isset($multisite_candidates[$prefix])) {
                    $multisite_candidates[$prefix] = [];
                }
                // save found tables under prefix name
                $multisite_candidates[$prefix][] = $table_name;
            }
        }

        // iterate our findings to check if each prefix has all the tables we care about
        // (to exclude possible partial and incomplete installations)
        if ( count($multisite_candidates) ) {
            foreach($multisite_candidates AS $prefix=>$table_names) {
                // if we don't see all the minimum tables we care about, this is possibly a broken installation and can be skipped
                // compare table lists, which can be in any order but should have the same values
                if ( $table_names == $testable_tables ) {
                    $distinct_prefixes_in_use[] = $prefix;
                }
            }
        }

        // Here's up to us to decide how many distint prefixes means multisite, logically it's 2 and more
        return $distinct_prefixes_in_use;
    }

    function table_info( $table ) {
        global $cached_table_info;

        // see if we already have info for this table
        if ( isset( $cached_table_info[ $table ] ))
        {
            return $cached_table_info[ $table ];
        }

        $res = array();

        if (empty($table)) {
            return $res;
        }

        $table = str_replace('`', '', $table); // it's something

        do {
            $dq = $this->_query("DESCRIBE `" . $table . "`", $this->link);
            if ( $this->_generic_error_check( $dq, 'add_to_log', 'table_info - DESCRIBE failed' ) ) {
                return $res;
            }

            $res['cols'] = array();
            $aut_field  = null;
            $pri_fields = array();
            $uni_field  = null;
            // can only run through once
            while ($col = $this->_fetch_assoc( $dq )) {
                $res['cols'][$col['Field']] = $col;

                // Auto-Increment Fields, likely what we need
                if ( $col['Extra'] == 'auto_increment' ) {
                    $aut_field = $col['Field'];
                }

                // In case AI field is not present, seek Primary
                // Note: tables might contain composite primary keys... o_O
                if ( $col['Key'] == 'PRI' ) {
                    $pri_fields[] = $col['Field'];
                }

                // In case no Primary is specified, try numeric Unique
                if ( !$uni_field && $col['Key'] == 'UNI' && $this->is_db_int( $col['Type'] ) ) {
                    $uni_field = $col['Field'];
                }
            }

            // figure out the best candidate for ID field:
            if ( $aut_field ) {
                $res['idcol'] = $aut_field;
            } else if ( count( $pri_fields ) === 1 ) { // single primary field
                $res['idcol'] = reset( $pri_fields );
            } if ( count( $pri_fields ) > 1 ) { // composite primary key
                # @TODO: Deal with composite PRI keys later! [requested to hold off by Erick]
                # Will treat it as NULL key for now
                //$res['idcol'] = json_encode( $pri_fields );
            } else if ( $uni_field ) {
                $res['idcol'] = $uni_field;
            }
        } while (0);

        do {
            $sq = $this->_query('show table status like "' . $this->_escape_string( $table ) . '"', $this->link);
            if ( $this->_generic_error_check( $sq, 'add_to_log', 'table_info - show table status failed' ) ) {
                echo_enc('|tl2|', $table, "\n");
                return $res;
            }

            while ($tbl = $this->_fetch_assoc( $sq )) {
                if ( $tbl['Name'] != $table ) continue;
                $res['info'] = $tbl;
                break 2;
            }
        } while (0);

        if ( !array_key_exists('idcol', $res)) {
            $res['idcol'] = null;
        }else{
          // Keep original logic applicable to single primary key ONLY.
          if ( count( $pri_fields ) === 1 ) {
            do {
                $sq = $this->_query("SELECT max(`" . $res['idcol'] . "`) as last_id FROM `$table`");
                if ( $this->_generic_error_check( $sq, 'echo_enc' ) ) {
                    echo_enc('|tl3|', $table, "\n");
                    continue;
                }

                while ($tbl = $this->_fetch_assoc( $sq )){
                  $res['info']['last_id'] = $tbl['last_id'];

                  // We need valid UTF-8 value so json_encode can successfully send it back to API
                  // If not, then we need to cleanup those characters. Will simply replace with '?' by default.
                  // We cannot just convert them, since we don't know what encoding was meant to be there.
                  // Also, check if the function itself is available (on some installations it is not)
                  if ( function_exists( 'mb_check_encoding' ) && isset($res['info']['last_id']) && !mb_check_encoding( $res['info']['last_id'], 'UTF-8' ) )
                  {
                    $res['info']['last_id'] = Mysql_Base::cleanup_non_utf8( $res['info']['last_id'] );
                  }

                  break 2;
                }
            } while (0);
          }
        }

        // cache table info
        $cached_table_info[ $table ] = $res;

        return $res;
    }

    // https://webcollab.sourceforge.io/unicode.html
    public static function cleanup_non_utf8( $string, $replacement = '?' )
    {
        //reject overly long 2 byte sequences, as well as characters above U+10000 and replace with ?
        $string = preg_replace(
            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
            '|[\x00-\x7F][\x80-\xBF]+'.
            '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
            '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
            '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            $replacement, $string
        );

        //reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
        $string = preg_replace(
            '/\xE0[\x80-\x9F][\x80-\xBF]'.
            '|\xED[\xA0-\xBF][\x80-\xBF]/S', $replacement, $string
        );

        return $string;
    }

    function recent_rows($table, $idcol = '', $last_id = 0, $timestamp = '', $limit = MAX_ROWS_PER_QUERY) {
        global $_PLATFORM;

        if ( $idcol === null )
        {
            return $this->recent_rows_no_ID( $table );
        }

        switch ($_PLATFORM)
        {
            case 'wordpress':
                return $this->recent_rows_WP( $table, $idcol, $last_id, $timestamp, $limit );

            case 'joomla':
            case 'other': // used to be called "generic"
            default:
                return $this->recent_rows_Generic( $table, $idcol, $last_id, $timestamp, $limit );
        }
    }

    function recent_rows_WP($table, $idcol, $last_id, $timestamp = '', $limit = MAX_ROWS_PER_QUERY) {
        if (!is_numeric((string)$last_id) || !is_numeric((string)$limit)) {
            return array();
            # die_enc_db('bad_numbers_in_rr');
        }

        $table   = str_replace('`', '', $table);
        $idcol   = str_replace('`', '', $idcol);
        $where   = "`$idcol` > $last_id";

        // check for timestamp (associated with *_posts)
        if ( $timestamp != '' )
        {
            global $_QUOTA;

            $where = "`$idcol` < $last_id and `post_modified` > '{$timestamp}'";
            $limit = $_QUOTA;
        }

        // check for *_posts here in case this function was sent without a timestamp
        if ( substr( $table, -6 ) == '_posts' )
        {
            $where .= " and `post_status` = 'publish'";
        }
        else if ( substr( $table, -9 ) == '_comments' )
        {
            $where .= " and `comment_approved` = '1'";
        }

        $sql_qry = "SELECT * FROM `$table` WHERE $where ORDER BY `$idcol` ASC LIMIT $limit";
        $this->result_set = $this->_query($sql_qry);

        $this->_generic_error_check( $this->result_set, 'die_enc_db', 'recent_rows_WP' );

        $return = array();
        while( ( $row = $this->_fetch_assoc( $this->result_set ) ) != false )
        {
            $return[] = $row;
        }

        //add_to_log( $sql_qry, 'SQL SELECT QUERY - WP (' . count($return) . ' rows returned)' );
        $this->_close();

        return $return;
    }

    function recent_rows_Generic($table, $idcol = '', $last_id = 0, $timestamp = '', $limit = MAX_ROWS_PER_QUERY) {
        // $last_id value can be an arbitrary string, so ctype_numeric() check no longer applies!

        $table   = str_replace('`', '', $table);

        // common case with ID column specified
        if ( $idcol )
        {
            // simple case with sino last ID
            if ( is_scalar( $idcol ) && $last_id == 0 )
            {
                $idcol   = str_replace('`', '', $idcol);

                $where = "1";
                $order = "ORDER BY `{$idcol}` ASC";
            }
            // regular case with single ID column
            if ( is_scalar( $idcol ) && is_scalar( $last_id ) )
            {
                // original logic
                $idcol   = str_replace('`', '', $idcol);
                $last_id = $this->_escape_string( $last_id );

                $where = "`{$idcol}` > \"{$last_id}\"";
                $order = "ORDER BY `{$idcol}` ASC";
            }
            // starting case of composite primary key when we don't need to offset
            else if ( is_array( $idcol ) && $last_id == 0 )
            {
                $order = array();
                foreach( $idcol AS $index => $id_column_name )
                {
                    $order[] = "`{$id_column_name}` ASC";
                }

                if ( !empty( $order ) )
                {
                    $where = '1';
                    $order = "ORDER BY " . implode( ', ', $order );
                }
                else
                {
                    add_to_log( array( '$idcol' => $idcol, '$last_id' => $last_id ), 'Empty ORDER param in recent_rows_Generic' );
                    return array();
                }
            }
            // complex case with composite PRI Key
            else if ( is_array( $idcol ) && is_array( $last_id ) && count( $idcol ) === count( $last_id ) )
            {
                $where = $order = array();
                foreach( $idcol AS $id_column_name )
                {
                    $value = $this->_escape_string( $last_id[ $id_column_name ] );
                    $where[] = "`{$id_column_name}` >= \"{$value}\"";
                    $order[] = "`{$id_column_name}` ASC";
                }

                if ( !empty( $where ) && !empty( $order ) )
                {
                    $where  = implode( ' AND ', $where );
                    $order  = "ORDER BY " . implode( ', ', $order );
                    $limit .= " OFFSET 1"; // first record in this set will be the same as the last record in the previous set
                }
                else
                {
                    add_to_log( array( '$idcol' => $idcol, '$last_id' => $last_id ), 'Empty WHERE/ORDER params in recent_rows_Generic' );
                    return array();
                }
            }
            // invalid data format
            else
            {
                add_to_log( array( '$idcol' => $idcol, '$last_id' => $last_id ), 'Invalid ID column params in recent_rows_Generic' );
                return array();
            }
        }
        // case with no ID column - just query to the limit
        else
        {
            $where = '1';
            $order = '';
        }

        $sql_qry = "SELECT * FROM `{$table}` WHERE {$where} {$order} LIMIT {$limit}";
        $this->result_set = $this->_query($sql_qry);

        if ( !$this->result_set )
        {
            add_to_log( $sql_qry, 'SQL ERROR - Generic (FAILED!)' );
            $this->_generic_error_check( $this->result_set, 'die_enc_db', 'recent_rows_Generic' );
        }

        $return = array();
        while( ( $row = $this->_fetch_assoc( $this->result_set ) ) != false )
        {
            $return[] = $row;
        }

        //add_to_log( $sql_qry, 'SQL SELECT QUERY - Generic (' . count($return) . ' rows returned)' );
        $this->_close();

        return $return;
    }

    function recent_rows_no_ID( $table ) {
        $table   = str_replace('`', '', $table);

        if ( $this->result_set === null )
        {
            // Simplified case with no ID column (not limit)
            $qry = "SELECT * FROM `{$table}`";
            // In case of no ID table, we'll pull *ALL* rows, since we can't order or pagiante effectively.
            // To make sure memory doesn't blow up on large table, we need to explicitly stop buffering for this query.
            $this->_set_buffered( false );
            $this->result_set = $this->_query( $qry, $this->link );
            $this->_set_buffered( true );

            if ( $this->_generic_error_check( $this->result_set, 'echo_enc' ) ) {
                add_to_log( $qry, 'ERROR - recent_rows_no_ID' );
                return array();
            }
        }

        if ( $row = $this->_fetch_assoc( $this->result_set ))
        {
            //add_to_log( htmlentities( json_encode( $row ) ), "1 ROW QUERY in {$table} - recent_rows_no_ID" );
            return array( $row ); // result set with just one row at a time - we don't know how many...
        }
        else
        {
            $this->_close();
            return array();
        }
    }

    function update_rows( $table, $where_column, $where_value, $update_column, $update_value ) {
    //    add_to_log('start update_rows:'.$table, "update_rows");

        $kvpairs = array();
        $kvpairs[ $update_column ] = $update_value;
        $ustr = $this->_format_pairs( $kvpairs );
        $uid  = $this->_escape_string( (string) $where_value);

        // query
        $qry = "UPDATE `$table` SET $ustr WHERE ";

        if ( is_array( $where_column ) )
        {
            foreach ( $where_column as $key => $where )
            {
                $qry .= ( $key > 0 ? ' and ' : '' );
                $qry .= "`" . $this->_escape_string( $where ) . "`='" . $this->_escape_string( $where_value[ $key ] ) . "'";
            }
        }
        else
        {
            $qry .= "`$where_column` = '$uid'";
        }

     //   add_to_log( $qry, 'BULK_UPDATE_ROWS' );

        return $this->_rows_affected( $this->_query( $qry ) );
    }

    // post_modified is only available for wp_posts and thus this function should
    // only be used to update the wp_posts everything
    function update_row( $action, $table, $idcol, $id, $column, $orig_md5, $new_value, $date, $orig_value_base64 = null ) {
        global $_ON_VERSION_CONFLICT, $_PLATFORM;

    //    add_to_log('start update_row:'.$table.' '.$action.' '.$idcol, "update_row");
        $orig_md5 = is_string( $orig_md5 ) && $orig_md5 != '' ? trim( $orig_md5 ) : '';
        $skip_md5 = $orig_md5 == '' ? true : false;

        $kvpairs = array();

        if ( $action == ACTION_DEL )
        {
            $new_value = '';
            $skip_md5 = true;
        }

        if ( $action == ACTION_RES )
        {
            $skip_md5 = true;
        }

        // Special case for Generic
        if ( is_array( $column ) && is_array( $new_value ) )
        {
            // we might have info for multiple columns/values withing the same record
            foreach( $column AS $index => $column_name )
            {
                $kvpairs[ $column_name ] = base64_decode( $new_value[ $index ] );
            }
        }
        else if ( trim( $column ) != '' )
        {
            $kvpairs[ $column ] = $new_value;
        }

        // Wrapper for original WP extra juggling
        if ( $_PLATFORM == 'wordpress' )
        {
            // check for comments
            if ( substr( $table, -8 ) == 'comments' )
            {
                $kvpairs[ 'comment_approved' ] = $action == ACTION_DEL ? '0' : '1';
            }

            // check for posts
            if ( substr( $table, -5 ) == 'posts' )
            {
                // by default, all posts should be publish
                $kvpairs[ 'post_modified' ] = $this->_escape_string( (string) $date );
                $kvpairs[ 'post_status' ] = $action == ACTION_DEL ? 'trash' : 'publish';
            }
        }

        $table   = str_replace('`', '', $table);
        $ustr    = $this->_format_pairs( $kvpairs );

        if ( empty( $ustr ) ) {
            return false;
        }


        // Generic case for table with no ID column:
        if ( !$idcol && $orig_value_base64 !== null )
        {
            // Step 1: Query table for values matching the original value
            if ( is_array( $orig_value_base64 ) )
            {
                $orig_value_comparison = array();
                foreach( $column AS $index => $column_name )
                {
                    $orig_value_comparison[ $column_name ] = " `{$column}` = '" . $this->_escape_string( base64_decode( $orig_value_base64[ $index ] ) ) . "' ";
                }
                $orig_value_comparison = implode( ' AND ', $orig_value_comparison );
            }
            else
            {
                $orig_value_comparison = " `{$column}` = '" . $this->_escape_string( base64_decode( $orig_value_base64 ) ) . "' ";
            }

            $qry = "SELECT * FROM `$table` WHERE {$orig_value_comparison}";
            add_to_log( $qry, 'Query 1 to search for matching values');

            $query1_result = $this->_query( $qry );
            add_to_log( $query1_result, 'Query 1 result');

            if ( $this->_generic_error_check( $query1_result ) ) {
                add_to_log( 'query 1', 'SQL ERROR!');
                return false;
            }


            // Step 2: Get table cols
            $tinfo = $this->table_info($table);
            $column_names = array_keys($tinfo['cols']);


            // Step 3: Calculate row hashes
            $row_hashes = array();
            $rows = array();
            // we might find multiple rows with exactly the same row contents...
            while( ($row_values = $this->_fetch_row($query1_result) ) != false )
            {
                $hash = md5( implode( '|', $row_values ) );
                $row_hashes[] = $hash;
                add_to_log( $hash . " ? " . $orig_md5, 'row hash calcualated vs received');

                // skip rows that don't fully match
                if ( $hash !== $orig_md5 )
                {
                    continue;
                }

                $rows[] = array_combine( $column_names, $row_values );
                //add_to_log( $row_values, 'matched row');
            }

            $updates_count = 0;
            if ( !empty( $rows ) )
            {
                foreach( $rows AS $row )
                {
                    $all_original_key_value_pairs = array();
                    // prepare comparisons for each column
                    foreach( $row AS $key => $value )
                    {
                        $key_value_sql = "`{$key}` = '" . $this->_escape_string( $value ) . "'";
                        // Empty value and NULL are different and we can't tell what we have, so use both in comparison.
                        if ( empty( $value ) ) {
                            $key_value_sql = " ( {$key_value_sql} OR `{$key}` IS NULL ) ";
                        }
                        $all_original_key_value_pairs[] = $key_value_sql;
                    }
                    // combine all comparisons
                    $all_original_key_value_pairs = "(" . implode( " AND ", $all_original_key_value_pairs ) . ")";
                    // put everything into a query
                    $qry = "UPDATE `{$table}` SET {$ustr} WHERE {$all_original_key_value_pairs}";
                    add_to_log( '<textarea>' . $qry . '</textarea>', 'UPDATE query - no ID case');

                    $query2_result = $this->_query( $qry );

                    if ( $this->_generic_error_check( $query2_result ) )
                    {
                        add_to_log( 'query 2', 'SQL ERROR!');
                    }
                    else
                    {
                        $updates_count += $this->_rows_affected( $query2_result );
                    }
                }
            }

            add_to_log( $updates_count, 'Total updates no ID case');

            return $updates_count;
        }
        else
        // Original logic, expecting a valid ID column:
        {
            // Check if out ID columns and value are actually multi-ID case
            $id_columns = try_json_decode( $idcol );
            $id_values  = try_json_decode( $id );
            if ( is_array( $id_columns ) && count( $id_columns ) > 1 && is_array( $id_values ) && count( $id_values ) > 1 )
            {
                $where = array();
                foreach( $id_columns AS $id_column )
                {
                    $where[] = "`" . str_replace('`', '', $id_column) . "` = '" . $this->_escape_string( $id_values[ $id_column ] ) . "'";
                }
                $where = implode( ' AND ', $where );
            }
            // original logic - single ID case
            else
            {
                $idcol   = str_replace('`', '', $idcol);
                $uid     = $this->_escape_string( (string) $id);
                $where = "`{$idcol}` = '{$uid}'";
            }

            // if set to warn then check for md5 of original
            if ( !$skip_md5 && $_ON_VERSION_CONFLICT == 'warn' && $orig_md5 != '' )
            {
                // get val
                $qry   = "SELECT `{$column}` FROM `{$table}` WHERE {$where} LIMIT 1";
                $array = $this->_fetch_array( $this->_query( $qry ) );

                if ( isset( $array[ 0 ][ $column ] ) )
                {
                    $selected_value = $array[ 0 ][ $column ];
                }
                else if ( isset( $array[ $column ] ) )
                {
                    $selected_value = $array[ $column ];
                }

                $md5 = md5( $selected_value );

                add_to_log( $md5 . ' == ' . $orig_md5, 'MD5_COMPARE' );

                // check if md5 does not match
                if ( $md5 != $orig_md5 )
                {
                    add_to_log( '<textarea>' . $selected_value . '</textarea>', '$selected_value where $md5 != $orig_md5' );
                    return 0;
                }
            }

            $qry = "UPDATE `{$table}` SET {$ustr} WHERE {$where} LIMIT 1";

            //add_to_log( $qry, 'UPDATE query in Original logic');
        }

        $updates_count = $this->_rows_affected( $this->_query( $qry ) );

        add_to_log( (int)$updates_count, 'Total updates in original logic');

        return $updates_count;
    }

    function check_row($table, $idcol, $id, $column) {
        $table   = str_replace('`', '', $table);
        $idcol   = str_replace('`', '', $idcol);
        $id      = $this->_escape_string((string)$id);
        $qry     = "SELECT `$idcol` FROM `$table` WHERE `$idcol` = '$id'";

//        add_to_log( $qry, 'CHECK_ROW' );

        $array = $this->_fetch_array( $this->_query( $qry ) );

//        add_to_log( $array, "CHECK_ROW_RESPONSE" );

        return !empty( $array );
    }

    function delete_row($table, $idcol, $id) {
        $table   = str_replace('`', '', $table);
        $idcol   = str_replace('`', '', $idcol);
        $id      = $this->_escape_string((string)$id);
        $qry     = "DELETE FROM `$table` WHERE `$idcol` = '$id'";

//        add_to_log( $qry, 'DELETE_ROW' );

        return $this->_rows_affected( $this->_query( $qry ) );
    }

    function insert_row($table, $rowdata, $default_rowdata) {
        $table = str_replace('`', '', $table);
        $istr  = $this->_format_pairs($rowdata);

        if (empty($istr)) {
            return null;
        }

        $qry = "INSERT INTO `$table` SET $istr";

        if ( !empty( $default_rowdata ) ) {
            $qry .= " ON DUPLICATE KEY UPDATE ";
            $qry .= $this->_format_pairs( $default_rowdata );
        }

//        add_to_log( $qry, 'INSERT_ROW_ON_DUPLICATE' );

        return $this->_query($qry) ? $this->_insert_id() : 0;
    }

    function _format_pairs($pairs) {
        if (!is_array($pairs) || !count($pairs)) {
            return null;
        }

        $vals = array();
        foreach ($pairs as $k => $v ) {
            $k = str_replace('`', '', $k);
            $v = $this->_escape_string( $v );

            $vals[] = "`$k` = '$v'";
        }

        $str = join(',', $vals);

        return $str;
    }

    function is_db_int( $field_type_string )
    {
        return stripos( $field_type_string, "int(" ) !== false;
    }

    function _query($sql, $link = null) {
        die_enc_db('impl_err:0');
    }

    function _generic_error_check( $result, $handle_func = '', $title = null ) {
        die_enc_db('impl_err:1');
    }

    function _clear_definer_info_from_file( $file, $definer_regex )
    {
        $pattern            = "/{$definer_regex}/";
        $replacement        = '';
        $replacements_count = 0;

        // read original file
        $fp = fopen( $file, 'r' );

        // create temp file to store cleaned version:
        $cleaned_path = $file . '.cleaned';
        $fp_cleaned = fopen( $cleaned_path, 'w' );

        while( ( $line = fgets( $fp) ) !== false )
        {
            $count = 0;
            $line_updated = preg_replace( $pattern, $replacement, $line, -1, $count );
            fwrite( $fp_cleaned, $line_updated );
            $replacements_count += $count;
        }

        fclose( $fp );
        fclose( $fp_cleaned );

        // if any replacements were made, use the cleaned file
        if ( $replacements_count > 0 )
        {
            unlink( $file );
            rename( $cleaned_path, $file );
        }
        else // nothing was updated - drop file copy
        {
            unlink( $cleaned_path );
        }

        return $replacements_count;
    }

    function _fetch_row( $res ) {
        die_enc_db('impl_err:2:a');
    }

    function _fetch_assoc( $res ) {
        die_enc_db('impl_err:2:b');
    }

    function _fetch_array( $res ) {
        die_enc_db('impl_err:2:c');
    }

    function _num_rows( $res ) {
        die_enc_db('impl_err:3');
    }

    function _escape_string( $res ) {
        die_enc_db('impl_err:4');
    }

    function _insert_id( $link = null ) {
        die_enc_db('impl_err:5');
    }

    function error_no() {
        die_enc_db('impl_err:6:a');
    }

    function error_str() {
        die_enc_db('impl_err:6:b');
    }

    // By default, all MySQL queries are buffered.
    // This means that query results are immediately transferred from the MySQL Server to PHP and then are kept in the memory of the PHP process.
    // Unbuffered MySQL queries execute the query and then return a resource while the data is still waiting on the MySQL server for being fetched.
    function _set_buffered( $buffered = true ) {
        $this->buffered = (bool) $buffered;
    }

    function _close() {
        die_enc_db('impl_err:7');
    }

    public static function determine_locking_flag( $engine )
    {
        $locking_flag = null;
        // Prepare command to get Table Structure + Data + Triggers
        switch( strtoupper( $engine ) )
        {
            case 'INNODB':
                $locking_flag = '--single-transaction=TRUE';
                break;

            case 'MYISAM':
            case 'MEMORY': // provides table-level locking
            case 'CSV': // no transactions
            case 'MERGE':
            case 'ARCHIVE': // does not support transactions
                $locking_flag = '--lock-tables=FALSE';
                break;
        }
        return $locking_flag;
    }

    public static function establish_mysql_version( $command = 'mysql' )
    {
        $version = NULL;
        $command_with_version = "{$command} -V";
        if ( function_exists( 'exec' ) )
        {
            $version = self::try_command_and_log_error( $command_with_version );
        }
        return $version;
    }

    public static function establish_mysqldump_version( $command = 'mysqldump' )
    {
        $version = NULL;
        $command_with_version = "{$command} -V";
        if ( function_exists( 'exec' ) )
        {
            $version = self::try_command_and_log_error( $command_with_version );
        }
        return $version;
    }

    private static function try_command_and_log_error( $command )
    {
        $output = NULL;
        $return_code = NULL;
        $response = exec( $command, $output, $return_code );

        // Handle case where we can exec command, but the command itself is not available
        if ( empty($output) && $return_code > 0)
        {
            add_to_log( $return_code, "Command '{$command}' failed with error code");
            return NULL;
        }

        return $response;
    }
}

class Mysql_Old extends Mysql_Base {
    var $link;

    function __construct( $exception_on_failure = false ) {
        global $_FEATURECODE;

        add_to_log( false, 'Starting MySQL constructor' );
        if ( !function_exists('mysql_connect') )
        {
            add_to_log( false, 'MySQL class NOT available!' );
            if ( $exception_on_failure )
            {
                throw new DB_Exception( 'mysql_no_fn' );
            }
            else
            {
                die_enc_db('mysql_no_fn');
            }
        }

        $this->link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
        if ( !$this->link )
        {
            add_to_log( false, 'MySQL connect to ' . DB_HOST . ' FAILED for ' . DB_USER );
            if ( $exception_on_failure )
            {
                throw new DB_Exception( 'mysql_connect_fail' );
            }
            else
            {
                die_enc_db('mysql_connect_fail');
            }
        }

        // UTF8 support
        mysql_set_charset( 'utf8mb4', $this->link );
        // Select DB only if it's DB Scan - we don't have DB name for Backup
        if ( $_FEATURECODE == DBSCAN && !mysql_select_db(DB_NAME, $this->link) )
        {
            $error = 'mysql_select_db_fail';
            add_to_log( $error, 'error in Mysql_Old constructor');

            if ( $exception_on_failure )
            {
                throw new DB_Exception( mysql_error($this->link), mysql_errno($this->link) );
            }
            else
            {
                die_enc_db($error);
            }
        }
    }

    function Mysql_Old() {
        $this->__construct();
    }

    function _generic_error_check( $result, $handle_func = '', $title = null ) {
        if ( !function_exists($handle_func) ) {
            $handle_func = 'die_enc_db';
        }

        if ( !$result ) {
            $handle_func( sprintf('mysql_query_error:%d:%s', mysql_errno($this->link), mysql_error($this->link)), $title );
            return true;
        }

        // elseif ( !$this->_num_rows($result) ) {
            // if ( $from != '' )
            // {
            //     add_to_log( $from, 'FROM' );
            // }
            // $handle_func('zero_rows');
            // return true;
        // }

        return false;
    }

    function error_no() {
        mysql_errno($this->link);
    }

    function error_str() {
        mysql_error($this->link);
    }

    function _query($sql, $link = null) {
        if (is_null($link)) {
            $link = $this->link;
        }

        if ( $this->buffered ) {
            return mysql_query($sql, $link);
        } else {
            return mysql_unbuffered_query($sql, $link);
        }
    }

    function _rows_affected( $query ) {
        return mysql_affected_rows();
    }

    function _escape_string( $string ) {
        return mysql_real_escape_string( $string, $this->link );
    }

    function _fetch_row( $res ) {
        if (!is_resource($res)) return null;
        return mysql_fetch_row( $res );
    }

    function _fetch_assoc( $res ) {
        if (!is_resource($res)) return null;
        return mysql_fetch_assoc( $res );
    }

    function _fetch_array( $res ) {
        if (!is_resource($res)) return null;
        return mysql_fetch_array( $res );
    }

    function _num_rows( $res ) {
        if (!is_resource($res)) return null;
        return mysql_num_rows( $res );
    }

    function _insert_id( $link = null ) {
        if (!is_resource($link)) $link = $this->link;
        return mysql_insert_id($link);
    }

    function _close() {
        if ( $this->result_set !== null )
        {
            $response = mysql_free_result( $this->result_set );
            $this->result_set = null;
            return $response;
        }
        return true;
    }
}

class Mysql_New extends Mysql_Base {
    var $link;

    function __construct( $exception_on_failure = false ) {
        global $_FEATURECODE;

        add_to_log( false, 'Starting MySQLi constructor' );
        if ( !class_exists('mysqli') )
        {
            add_to_log( false, 'MySQLi class NOT available!' );
            if ( $exception_on_failure )
            {
                throw new DB_Exception( 'mysqli_no_class' );
            }
            else
            {
                die_enc_db('mysqli_no_class');
            }
        }

        $port   = null;
        $socket = null;
        $host   = DB_HOST;
        $port_or_socket = strstr( $host, ':' );
        if ( ! empty( $port_or_socket ) ) {
            $host = substr( $host, 0, strpos( $host, ':' ) );
            $port_or_socket = substr( $port_or_socket, 1 );
            if ( 0 !== strpos( $port_or_socket, '/' ) ) {
                $port = intval( $port_or_socket );
                $maybe_socket = strstr( $port_or_socket, ':' );
                if ( ! empty( $maybe_socket ) ) {
                    $socket = substr( $maybe_socket, 1 );
                }
            } else {
                $socket = $port_or_socket;
            }
        }

        $db_name = NULL;
        // Select DB only if it's DB Scan - we don't have DB name for Backup
        if ( $_FEATURECODE == DBSCAN )
        {
            $db_name = DB_NAME;
        }

        $this->link = new mysqli( $host, DB_USER, DB_PASSWORD, $db_name, $port, $socket );
        // UTF8 support
        $this->link->set_charset( 'utf8mb4' );

        if ( $this->link->connect_error )
        {
            $error = sprintf('mysqli_open_fail:%d:%s:1', $this->link->connect_errno, $this->link->connect_error);
            add_to_log( $error, 'error in Mysql_New constructor' );

            if ( $exception_on_failure )
            {
                throw new DB_Exception( $this->link->connect_error, $this->link->connect_errno );
            }
            else
            {
                die_enc_db($error);
            }
        }

    }

    function Mysql_New() {
        $this->__construct();
    }

    function _generic_error_check( $result, $handle_func = '', $title = null ) {
        if ( !function_exists($handle_func) ) {
            $handle_func = 'die_enc_db';
        }

        if ( !$result ) {
            $handle_func( sprintf('mysqli_query_error:%d:%s', $this->link->errno, $this->link->error), $title );
            return true;
        }

        // elseif ( !$this->_num_rows( $result ) ) {
        //     $handle_func('zero_rows');
        //     return true;
        // }

        return false;
    }

    function error_no() {
        return $this->link->errno;
    }

    function error_str() {
        return $this->link->error;
    }

    function _query( $sql, $link = null ) {
        if ( is_null($link) ) $link = $this->link;
        if (!is_object($link)) return null;

        if ( $this->buffered ) {
            return $link->query($sql);
        } else {
            return $link->query($sql, MYSQLI_USE_RESULT);
        }
    }

    function _rows_affected( $query ) {
        #  RJN 22382 8/17/2017
        # if the update has already executed,
        # then $query gets assigned the rows_affected as output.
        # so, return that.  otherwise, poke the object for it.
        if (!is_object($query)) return $query;
        else
            return $query->rowCount();
    }

    function _escape_string( $string ) {
        return $this->link->escape_string( $string );
    }

    function _fetch_row( $res ) {
        if (!is_object($res)) return null;
        return $res->fetch_row();
    }

    function _fetch_assoc( $res ) {
        if (!is_object($res)) return null;
        return $res->fetch_assoc();
    }

    function _fetch_array( $res ) {
        if (!is_object($res)) return null;
        return $res->fetch_array();
    }

    function _num_rows( $res ) {
        if (!is_object($res)) return null;
        return $res->num_rows;
    }

    function _insert_id( $link = null ) {
        if (is_null($link)) $link = $this->link;
        if (!is_object($link)) return null;
        return $link->insert_id;
    }

    function _close() {
        if ( $this->result_set !== null )
        {
            $this->result_set->close(); // does not return anything
            $this->result_set = null;
        }
        return true;
    }

}

/**
 * @todo
 */
class wPDO {
    var $link;

    function __construct() {
        die_enc_db('pdo_not_implemented');
    }

    function wPDO() {
        $this->__construct();
    }

}

class DB_Exception extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code

        // make sure everything is assigned properly
        if (version_compare(PHP_VERSION, '5.3.0', '>='))
        {
            parent::__construct($message, $code, $previous);
        }
        else
        {
            parent::__construct($message, $code); // in PHP 5.2, Fatal Error on third param...
        }
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
# Database - END
# Utilities - START
if ( version_compare( PHP_VERSION, '5.1.0', '>=' ) ) {
    date_default_timezone_set('America/New_York');
}

define('DEBUG', false);

define('COMPACT_XFER_FMT', true);
define('VERSION', '0.5.0');
define('RELEASE', false);

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', false);
ini_set('html_errors', false);

if (!function_exists('json_encode')) {
    function json_encode($object) {
        return _json_encode_internal($object);
    }
}

header('Content-Type: text/plain');

$_SITEID   = '43922276';
$_TOKEN    = 'd6b62b8bc700dcac643399fc6677a709';
$_UNIQUE   = '949efcd1f29b5e9a0646e7ca482a052c';

// New params used to support Generic DB
$_PLATFORM =    'other';
$_FEATURECODE = 'db_scan';

$db_structure_descriptor_file = 'db-structure-descriptor.json';
$descriptor_ext               = '-descriptor';
$backup_file_name             = 'database_backup.sql';

$_UPDATE_ID = isset( $_GET[ 'update_id' ] ) ? $_GET[ 'update_id' ] : null;

/** ********************************************************************************************
 *  First thing: always need to check if we can continue with the script by validating the IP. *
 * ****************************************************************************************** **/
{{
    $IPs = get_ip();
    add_to_log( __FILE__, 'IP Check started in');
    add_to_log( date( DATE_ATOM, time()), 'IP Check started at');
    add_to_log( $IPs, 'The following IPs will be tested');

    $ip_validated = false;
    foreach( $IPs AS $IP )
    {
        $payload = array(
            'site_id' => $_SITEID,
            'ip'      => $IP
        );

        $raw_response = mapi_post(
            $_TOKEN,
            'validate_ip',
            $payload
        );


        // check for curl errors before anything else.
        // curl not available at all? Too bad...
        if ( $CURL_INIT_ERR !== false )
        {
            add_to_log( $CURL_INIT_ERR, 'CURL INIT Error in check IP' );
            break;
        }
        // if it errored out, the HTTP connection failed and we cannot proceed
        if ( $CURL_MAPI_ERR !== false )
        {
            add_to_log( $CURL_MAPI_ERR, 'CURL MAPI Error in check IP' );
            break;
        }

        $ip_check_response = json_decode( $raw_response, true );

        // Only continue if IP is validated, and stop otherwise.
        if (
            !isset( $ip_check_response['responses'][0]['data']['valid'] ) ||
            $ip_check_response['responses'][0]['data']['valid'] != 1
            )
        {
            // nothing here - will retry the next IP, if exists
        }
        // Only need one successful IP validation to continue, otherwise - try other IPs
        else
        {
            $ip_validated = true;
            break;
        }
    }

    // If IP did not validate and no other CURL errors reported
    if ( !$ip_validated && !( $CURL_INIT_ERR || $CURL_MAPI_ERR ) )
    {
        $error = array(
            "allowed_ip" => 0,
        );

        add_to_log($error, 'error in check_ip()');

        // output the log
        echo_enc();

        echo json_encode( $error );
        exit;
    }
}}


/**
 * Reusable function to init path to where to bullet will go
 */
function get_bullet_location()
{
    // [27761] From now on, change in bullet placement: it will go into ./tmp instead of just .
    // Therefore, we'll default to jumping up one level.
    $extradir = '..' . DIRECTORY_SEPARATOR;

    $localdir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

    // put them together into a real path and avoid dozen repeated concatenations
    $localdir = realpath( $localdir . $extradir ) . DIRECTORY_SEPARATOR;

    return $localdir;
}


/**
 * Function to take care of deciding where to get the DB creds from, based on the platform param
 */
function init_DB_creds_based_on_platform()
{
    global $_PLATFORM;

    // Get DB creds
    switch( $_PLATFORM )
    {
        case 'wordpress':
            import_WP_creds();
            break;

        case 'joomla':
            import_Joomla_creds();
            break;

        case 'other':
        default:
            import_Generic_creds();
            break;
    }
}


/**
 * Function will submit site and feature to s3 init and attempt to get the single use id
 */
function handle_s3_init( $die_when_complete = false )
{
    global $_TOKEN, $_SITEID, $_FEATURECODE, $_CLIENTID, $_ENCRYPT_USE_CIPHER, $_ENCRYPT_USE_CIPHER_KEY, $_ENCRYPT_USE_CIPHER_IV;

    $params = array(
        'site_id'      => $_SITEID,
        'feature_code' => $_FEATURECODE,
    );

    $_CLIENTID   and $params[ 'client_id' ] = $_CLIENTID;

    if( CRYPTOR === OPENSSL )
    {
        $params[ 'ciphers' ] = openssl_get_cipher_methods(true);
    }
    else
    {
        $params[ 'ciphers' ] = mcrypt_list_algorithms();
    }

    $init = mapi_post( $_TOKEN, 's3_init', $params, LOG_MAPI_REQUEST );

    $single_id = null;
    $error = false;
    do {
        if ( !$init ) {
            $error = 'no-response-s3_init';
            break;
        }

        $iobj = @json_decode($init);
        if ( !$iobj ) {
            $error = 'failed-json_decode-s3_init';
            break;
        }

        if ( $iobj->status != 'ok' || $iobj->forceLogout ) {
            $error = "failed-s3_init:status={$iobj->status},forceLogout=".($iobj->forceLogout?1:0);
            break;
        }

        if ( $iobj->newToken && $iobj->newToken != $_TOKEN ) {
            $_TOKEN = $iobj->newToken;
            add_to_log($_TOKEN, 'updated $_TOKEN in s3_init');
        }

        if ( !$iobj->responses || !is_array($iobj->responses) || !count($iobj->responses) ) break;

        $response  = $iobj->responses[0]->data;
        $single_id = $response->queue_id;

        if ( $response->cipher != $_ENCRYPT_USE_CIPHER ) {
            $_ENCRYPT_USE_CIPHER = $response->cipher;
            add_to_log($_ENCRYPT_USE_CIPHER, 'updated Cipher in s3_init');
        }

        // s3_init returns Key value base64-encoded
        $key = base64_decode( $response->cipher_key );
        if ( $key != $_ENCRYPT_USE_CIPHER_KEY ) {
            $_ENCRYPT_USE_CIPHER_KEY = $key;
            add_to_log( obfuscate( $response->cipher_key ), 'updated Cipher Key in s3_init');
        }

        // s3_init returns IV value url-encoded
        $iv = urldecode( $response->cipher_iv );
        if ( $iv != $_ENCRYPT_USE_CIPHER_IV ) {
            $_ENCRYPT_USE_CIPHER_IV = $iv;
            add_to_log( obfuscate( $response->cipher_iv ), 'updated Cipher IV in s3_init');
        }

    } while (0);

    if ( $die_when_complete )
    {
        $response = array( 'response' => 'smart_single_download_id', 'smart_single_download_id' => $single_id );
        if ( $error )
        {
            $response[ 'error' ] = $error;
        }

        echo_enc(); // output log
        die_enc_json( $response ); // This is the expected die() returning JSON response to API. No changes needed.
    }
    else
    {
        return $single_id;
    }
}


function lock_the_bullet()
{
    $bytes = file_put_contents( get_bullet_lock_path(), time() );
    add_to_log( $bytes, 'lock_the_bullet: bytes written');
    return $bytes;
}

function unlock_the_bullet()
{
    $status = unlink( get_bullet_lock_path() );
    add_to_log( $status ? 'success':'failure', 'unlock_the_bullet: status');
    return $status;
}

function bullet_is_locked()
{
    $MAX_LOCK_TIME_SECONDS = 60;
    $path = get_bullet_lock_path();

    // no file - no lock
    if ( !file_exists( $path ) )
    {
        add_to_log( 'not locked (no lock file)', 'bullet_is_locked check:');
        return false;
    }
    else
    {
        $lock_time = (int) file_get_contents( $path );
        $current_time = time();
        // automatically drop lock if more than $MAX_LOCK_TIME_SECONDS elapsed since it was locked
        // (in case script hungs up - we would like to have an option to restart)
        $is_still_locked = $current_time - $lock_time < $MAX_LOCK_TIME_SECONDS ? true : false;
        add_to_log( "current: {$current_time}, locked at: {$lock_time}, diff: ".($current_time - $lock_time).", still locked: ".($is_still_locked?'Yes':'No'), 'bullet_is_locked check time:');
        return $is_still_locked;
    }
}

function get_bullet_lock_path()
{
    $path = __FILE__ . '.lock';
    add_to_log( $path, 'get_bullet_lock_path:');
    return $path;
}

function process_backup_schemas( $_SCHEMAS, $exception_on_failure = false )
{
    $db = getDbObj( $exception_on_failure );

    // Schemas not specified - get all available
    if ( $_SCHEMAS === true )
    {
        $_SCHEMAS = array();

        $result_set = $db->_query( "SHOW DATABASES" );
        if ($db->_generic_error_check($result_set))
        {
            add_to_log( $result_set, 'show-databases-error' );
            update_scan_on_error( 'BACKUP_DB_ERR_SCHEMAS', array( 'SHOW DATABASES' => $result_set ) );
        }


        while( $db_row = $db->_fetch_assoc( $result_set ) )
        {
            if(!in_array( $db_row['Database'], ['information_schema','performance_schema', 'mysql' ] ) )
            {
                $_SCHEMAS[] = $db_row[ 'Database' ];
            }
        }
        $db->_close();
    }
    // single schema or JSON of muptple schemas
    else if ( is_string( $_SCHEMAS ) )
    {
        $test_json = json_decode( $_SCHEMAS, true );
        // JSON decode with no errors
        if ( json_last_error() === JSON_ERROR_NONE )
        {
            $_SCHEMAS = $test_json;
        }
        // must be a single schema name
        else
        {
            $_SCHEMAS = array( $_SCHEMAS );
        }

    }
    else if ( is_array( $_SCHEMAS ) && count( $_SCHEMAS ) )
    {
        // no changes needed here
    }
    // unexpect format
    else
    {
        add_to_log( $_SCHEMAS, 'schemas-format-error' );
        update_scan_on_error( 'BACKUP_DB_ERR_SCHEMAS', array( '$_SCHEMAS' => $_SCHEMAS ) );
    }

    return $_SCHEMAS;
}

function cleanup_insufficient_priveleges( &$errors_array )
{
    foreach( $errors_array AS $index => $issue )
    {
        // If error is about some SP created by another user - skip it?
        // App
        if ( stripos( $issue, ' privileges to SHOW CREATE ' ) !== false )
        {
            add_to_log( '<textarea style="width:99%;height:100px;">' . $issue . '</textarea>', 'Skipping DB object we have no access to.' );
            unset( $errors_array[ $index ] );
        }
    }
}

function set_character_locale( $locale_value = "en_US.UTF-8" )
{
    // test if we can set UTF-8 locale necessary for clean DB queries
    // Making it optional... Look at it this way:
    // - If intl extension is not enabled, content is likely in English, and it won't matter if we couldnt't setlocale.
    // - If intl extension is enabled, then setlocale will likely work.
    $locale_set = setlocale( LC_CTYPE, $locale_value );
    add_to_log( $locale_set ? 'Success' : 'Fail' , 'Attempted to setlocale() with UTF-8' );
    if ( $locale_set === false )
    {
        // check if intl extension cannot be loaded - that would be a good reason
        $intl_loaded = extension_loaded( 'intl' );
        add_to_log( $intl_loaded ? 'Yes' : 'No, and nothing we can do (dl() is removed as of php 5.3).  ¯\_(ツ)_/¯' , 'Is intl extension loaded?' );
    }
    return $locale_set;
}


function get_foreign_key_checks()
{
    $db = getDbObj();

    $result_set = $db->_query( 'select @@foreign_key_checks' );
    $return = null;

    if ( ($result_set_row = $db->_fetch_assoc( $result_set )) !== false )
    {
        if ( isset($result_set_row[ '@@foreign_key_checks' ]) )
        {
            $return = (int) $result_set_row[ '@@foreign_key_checks' ];
        }
    }

    add_to_log( $return, 'get_foreign_key_checks' );

    $db->_close();

    return $return;
}


function set_foreign_key_checks( $value )
{
    $db = getDbObj();

    $result_set = $db->_query( 'SET FOREIGN_KEY_CHECKS=' . (int) $value );

    add_to_log( json_encode($result_set), 'set_foreign_key_checks to ' . $value );

    $db->_close();
}


// Original flow continues...
$_buffer = '';
if (RELEASE) {
    $_SUPER =& $_POST;
} else {
    $_SUPER =& $_REQUEST;
}

function getSuper( $key, $default = null ) {
    global $_SUPER;
    if (array_key_exists($key, $_SUPER)) {
        return $_SUPER[$key];
    }

    return $default;
}

function echo_enc() {
    global $_buffer, $_LOG;

    if ( $_LOG != '' )
    {
        send_email( $_LOG );
    }

    $_buffer .= join(func_get_args());
}

function die_enc($str = '', $title = null) {
    global $_buffer, $_LOG, $_SAVE_LOG;
    $_SAVE_LOG = true;
    $_buffer .= $str;

    if ( $title !== null )
    {
        add_to_log( $str, $title );
    }

    if ( $_LOG != '' )
    {
        send_email( $_LOG );
    }

    output_clean();
    exit;
}

function die_enc_json( $array = array() )
{
    // log the final data piece before script dies
    add_to_log( $array, 'die_enc_json TERMINATION' );
    // save db scan log
    echo_enc();
    // output transmission
    echo json_encode( $array );
    exit;
}

/**
 * @todo: encryption here
 */
function output_clean() {
    global $_buffer;

    $to_output = $_buffer; // <--- right there, that's where we need some encryption

    echo $to_output;
}

function delete_all_directory_files( $fileloc, $ext = 'csv' ) {
    if ( is_array( $ext ) )
    {
        $ext = implode( ',', $ext );
        $extant = glob($fileloc . DIRECTORY_SEPARATOR . '*.{' . $ext . '}', GLOB_BRACE );
        // Curly brace is a part of GLOB_BRACE syntax and has to be preserved as is.
    }
    else
    {
        $extant = glob($fileloc . DIRECTORY_SEPARATOR . "*.{$ext}" );
        // Here, curly is just a wrapper for PHP varaible - no special glob meaning.
    }
    // count how many were deleted
    $count_found = count($extant);
    $count_deleted = 0;
    if ( $count_found > 0 ) {
        foreach ( $extant as $exf ) {
            if( @unlink($exf) )
            {
                $count_deleted++;
            }
        }
    }

    return $count_found === $count_deleted;
}


function update_scan_on_error( $error_code, $error_message, $terminate = true )
{
    global $_TOKEN, $_SITEID, $_CLIENTID, $_UPDATE_ID, $_FEATURECODE, $_SAVE_LOG;

    if ( is_array( $error_message ) )
    {
        $error_message = json_encode( $error_message );
    }

    // prepare static params for s3 call and add dynamic ones later
    $s3_params = array(
        'site_id'       => $_SITEID,
        'client_id'     => $_CLIENTID,
        'update_id'     => $_UPDATE_ID,
        'feature_code'  => $_FEATURECODE,
        'status'        => 'error',
        'error_code'    => $error_code,
        'error_message' => $error_message,
    );

    $_SAVE_LOG = true; // log failed scans for debugging

    $mapi_response = mapi_post( $_TOKEN, 's3_update', $s3_params );

    if ( $terminate )
    {
        die_enc('error'); // API will be updated and bullet will stop execution. This function is a handle for abnormal termination.
    }

    return $mapi_response; // in case we need to look into it
}


// These checks, if failed, will abnormally terminate our execution.
// Normally we would call MAPI to update API with error, but if MAPI itself is unreachable, then, welp... log and die.
function check_and_terminate_on_mapi_errors()
{
    global $CURL_INIT_ERR, $CURL_MAPI_ERR;

    // Edge case - curl not available, so can't even eval the IP
    if ( $CURL_INIT_ERR )
    {
        add_to_log( $CURL_INIT_ERR, 'cURL Init Failed' );
        $error = array( 'CURL_INIT_ERR' => 0 );
        die_enc_json( $error );
    }

    // This is in edge case in prod (Prod MAPI/API are down?) and common case in stage (new domain being tested not whitelisted)
    if ( $CURL_MAPI_ERR )
    {
        add_to_log( $CURL_MAPI_ERR, 'cURL MAPI Failed' );
        $error = array( 'CURL_MAPI_ERR' => 0 );
        die_enc_json( $error );
    }
}

/**
 * Reusable wrapper around list_tables call that does extra processing
 */
function process_list_tables()
{
    global $_PLATFORM;

    // For WP and Joomla, since we know the prefix, limit tables we want to pull
    // For generic we'll pull everything (legacy logic)
    $prefix = null;
    if ( in_array( $_PLATFORM, ['wordpress','joomla'] ) && DB_PREFIX ) {
        $prefix = DB_PREFIX;
    }

    $db = getDbObj();
    $tables = $db->list_tables($prefix);

    $t_out = array();
    $t_out['prefix'] = DB_PREFIX;

    if ( is_array( $tables ) && count( $tables ) )
    {
        foreach ( $tables as $table )
        {
            $table_name = $table['info']['Name'];

            // restore original WordPress logic - pull only 3 specific tables
            if ( $_PLATFORM == 'wordpress' && DB_PREFIX )
            {
                $tables_to_return = array( DB_PREFIX.'users', DB_PREFIX.'posts', DB_PREFIX.'comments' );
                if ( !in_array( $table_name, $tables_to_return ) )
                {
                    continue;
                }
            }

            if (isset($table['info']['last_id'])){
                $t_last_id = $table['info']['last_id'];
            }else{
                $t_last_id = 0;
            }

            // process columns and their types
            $cols = array();
            foreach( $table['cols'] AS $key => $column )
            {
                $cols[ $key ] = (string) $column[ 'Type' ];
            }

            // put together fields we're interested in
            $t_out['tables'][ $table_name ] = array(
                'rows'         => $table['info']['Rows'],
                'idcol'        => $table['idcol'],
                'last_id'      => $t_last_id,
                'avg_row_len'  => $table['info']['Avg_row_length'],
                'all_data_len' => $table['info']['Data_length'],
                'engine'       => $table['info']['Engine'],
                'columns'      => $cols,
            );
        }
        return $t_out;
    }
    else
    {
        return false;
    }

}


/**
 * Reduces chunk size for server with low memory
 */
function reduce_chunk_size_on_low_memory( $reduction_multipler = 10 )
{
    global $_CHUNK_SIZE;

    $original_size = (int) $_CHUNK_SIZE;

    // see if server memory limit is not too small for our schunk.....
    $memory_limit_str = ini_get('memory_limit');
    add_to_log( $memory_limit_str, 'Detected memory_limit');

    if ( !empty($memory_limit_str) && preg_match( '/([\d]+)([MG])/', $memory_limit_str, $matches ) )
    {
        // .... downsize chunk size if memory is 32M or lower
        if( isset($matches[1]) && is_numeric($matches[1]) && (int)$matches[1] <= 32 && isset($matches[2]) && $matches[2] == 'M' )
        {
            $_CHUNK_SIZE = (int) ($_CHUNK_SIZE / $reduction_multipler);
        }
    }

    add_to_log( $_CHUNK_SIZE . ( $original_size != $_CHUNK_SIZE ? " (reduced from {$original_size})" : "" ), "Chunk Size");
}


function _decode_compact_data_format( $pairs ) {
    if (!is_array($pairs)) {
        if (!( is_string($pairs) && strpos($pairs, '=') != false )) { // 0 is also bad so skip that too
            return array();
        }
        $pairs = array($pairs);
    }

    $trow = array();

    if (count($pairs)) {
        foreach ($pairs as $upair) {
            list($uk, $uv) = explode('=', $upair, 2);
            if (strlen($uv)) {
                if (!($uv[0] == '@' && is_numeric(substr($uv, 1)))) {
                    $uv = base64_decode($uv);
                } else {
                    $uv = substr($uv, 1);
                }
            }
            $trow[$uk] = $uv;
        }
    }

    return $trow;
}

function _json_encode_internal($object) {
    switch (true) {
        case is_string($object):
            return '"' . str_replace('"', '\\"', $object) . '"';
        case is_numeric($object):
        case is_float($object):
        case is_int($object):
            return $object;
        case is_bool($object):
            return $object ? 'true' : 'false';
        case is_null($object):
            return 'null';
        case is_array($object):
            $km     = false;
            $keys   = array();
            $values = array();
            for( $int = 0, reset($object); list($key, $value) = each($object); ++$int) {
                $keys[] = $k = _json_encode_internal((string)$key);
                if ( !( $k === $key || $key == $int ) ) $km = true;
                $values[] = _json_encode_internal($value);
            }

            if ( count($keys) != count($values) ) {
                update_scan_on_error( 'ENCODING_FAILED', 'error_bad_counts_json_int' );
            }

            $kv = $values;
            if ( $km ) {
                for ($i = 0; $i < count($values) && $kv[$i] = "{$keys[$i]}:{$values[$i]}"; ++$i);
            }
            $d = $km ? 123 : 91;
            return chr($d) . join(',', $kv) . chr($d + 2);
        case is_object($object):
            return _json_encode_internal(get_object_vars($object));
        default:
        update_scan_on_error( 'ENCODING_FAILED', 'error_bad_vtype_json_int' );
    }
}

if ( !function_exists('file_get_contents') ) {
    function file_get_contents($filename) {
        if ( !file_exists($filename) ) {
            return null;
        }

        $fp = fopen('r', $filename);

        $contents ='';
        while ( !feof($fp) ) {
            if ( ($line = fread($fp, 8192)) !== false ) {
                $contents .= $line;
            }
        }

        fclose($fp);

        return $contents;
    }
}

if ( !function_exists('file_put_contents') ) {
    define('FILE_APPEND', 8);
    function file_put_contents($filename, $contents, $flags = 0) {
        $open = 'wb';
        if ( $flags & FILE_APPEND ) {
            $open = 'ab';
        }

        $fp = fopen($filename, $open);

        $written = fwrite($fp, $conents);

        fclose($fp);

        return $written;
    }
}

function myErrorHandler($errno, $errstr, $errfile, $errline, $errcontext='') {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
# Utilities - END
# Zip - START
function archive_files( $files, &$target, $root = null, $cap = 0 ) {
    add_to_log( 'start', 'archive_files' );
    if ( !is_array($files)) {
        return false;
    }

    $root = $root ? $root : $_SERVER['DOCUMENT_ROOT'];
    $root = realpath($root) . '/'; // keep slash attached to the root path and not the file

//    add_to_log( $root, 'archive_files 1' );
    foreach ( $files as &$file ) {
        $file = realpath($file);
    }

    if (!( $target_dir = realpath(dirname($target)) )) {
        return false;
    }

    $target = $target_dir . DIRECTORY_SEPARATOR . basename($target);

//    add_to_log( $target, 'archive_files 2' );

    DEBUG && var_dump(file_exists($target), is_dir($target), $target_dir, $target);

    if ( strcasecmp(substr($target, -4), '.zip') ) {
        if ( !is_dir($target) ) {
            if ( !@mkdir($target, 0700, true) ) {
                add_to_log( 'mkdir failed!', 'archive_files 2x' );
                return false;
            }
        }

        $nt = $target . DIRECTORY_SEPARATOR . str_replace(array('.',' '), '', microtime()) . '.zip';

    } elseif ( file_exists($target) ) {
        if ( !is_dir($target) && !unlink($target) ) {
            $nt = $target . '_' . str_replace(array('.',' '), '', microtime()) . '.zip';
        } else {
            $nt = $target . DIRECTORY_SEPARATOR . str_replace(array('.',' '), '', microtime()) . '.zip';
        }
    }

//    add_to_log( $nt, 'archive_files 3' );
    if ( isset($nt) && !empty($nt) ) {
//        add_to_log( file_exists($nt), 'archive_files 3g' );
        if ( file_exists($nt) ) {

            if ($cap > 20 ) { // I mean, what are the odds?  It's microtime()!
                return false;
            }

            return archive_files($files, $target, $root, $cap + 1);
        }

        $target = $nt;
    }

//    add_to_log( 'I made it here try #'. $cap, 'archive_files 4' );


//    if (class_exists('ZipArchive', false)) {
//        add_to_log( 'Send to ZA', 'archive_files 5' );
//        return archive_files_ZA( $files, $target, $root );
//    }
    add_to_log( 'Send to CLI'. $cap, 'archive_files 5' );

    $result = archive_files_CLI( $files, $target, $root );

    return $result;
}

function archive_files_ZA( $files, $target, $root ) {
    add_to_log( 'good luck!', 'Attempting to use ZipArchive.' );
    $zip = new ZipArchive;

    $result = $zip->open($target, ZipArchive::CREATE);
    $add_file_status = array();

    foreach ( $files as $file ) {
        $file_name = str_replace($root, '', $file);
        $add_file_status[] = $zip->addFile( $file_name );
    }

    $close_archive_status = $zip->close();

    // Once done, remove original files as ZipArchive will not remove them after adding
    if ( $close_archive_status && !in_array( false, $add_file_status ) )
    {
        foreach ( $files as $file ) {
            unlink( $file );
        }
    }

    return true;
    # return $zip->numFiles > 0;
}

function archive_files_CLI( $files, $target, $root ) {
    set_error_handler( "myErrorHandler" );

    // Fail Windows right here, but certain paths like /usr/bin/zip might be blocked so let that happen further down, within the try-catch
    if ( strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ) {
        return false;
    }

    $here = getcwd();
    chdir($root);
    $zip_path = '/usr/bin/zip';

    try { // Try using shell exec first
        file_exists( $zip_path ); // failing this will throw an exception before the actual sehll command
        is_executable( $zip_path );

        $files_count = count( $files );
        $files_running_total = 0;
        $files_group = array();
        $files_group_count = 10;

        foreach ( $files as $file )
        {
            $files_running_total++;
            // Try to group multiple files into single zip command for faster processing
            $files_group[] = $file;
            if ( count( $files_group ) >= $files_group_count || $files_running_total == $files_count )
            {
                $files_group_str = '';
                foreach( $files_group AS $file_in_group )
                {
                    $files_group_str .= ' ' . escapeshellarg($file_in_group);
                }

                $cmd = sprintf("{$zip_path} -jqm1 %s %s", escapeshellarg($target), $files_group_str);

                if ( function_exists('shell_exec') ) {
                    shell_exec( $cmd );
                } else {
                    throw new ErrorException( 'shell_exec_not_available' );
                }

                // reset temp array after we zip each batch
                $files_group = array();
            }
        }

    } catch (ErrorException $e) { // if shell exec is not supported we need to use ZipArchive instead
        add_to_log( $e->getMessage(), "Failed shell_exec for {$zip_path}." );
        archive_files_ZA( $files, $target, $root );
    }

    restore_error_handler();

    chdir($here);
    return true;
}
# Zip - END
# GrabAndZip - START

test_bullet_is_reachable();

set_time_limit(0);
ignore_user_abort(true);

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', false);
ini_set('html_errors', false);

$_FIRST_ID = 0;

$_CLIENTID = '26557';
$_TABLES   = array('entity_files' => array('last_id' => '0',),'oauth_access_tokens' => array('last_id' => '0',),'files' => array('last_id' => '0',),'menu_item_translations' => array('last_id' => '0',),'blog_post_translations' => array('last_id' => '0',),'tax_rate_translations' => array('last_id' => '0',),'activations' => array('last_id' => '0',),'products' => array('last_id' => '0',),'product_translations' => array('last_id' => '0',),'blog_category_translations' => array('last_id' => '0',),'currency_rates' => array('last_id' => '0',),'reviews' => array('last_id' => '0',),'page_translations' => array('last_id' => '0',),'reminders' => array('last_id' => '0',),'coupon_translations' => array('last_id' => '0',),'attributes' => array('last_id' => '0',),'pages' => array('last_id' => '0',),'oauth_refresh_tokens' => array('last_id' => '0',),'attribute_translations' => array('last_id' => '0',),'blog_tags' => array('last_id' => '0',),'tag_translations' => array('last_id' => '0',),'orders' => array('last_id' => '0',),'options' => array('last_id' => '0',),'option_value_translations' => array('last_id' => '0',),'menu_translations' => array('last_id' => '0',),'option_translations' => array('last_id' => '0',),'variation_values' => array('last_id' => '0',),'users' => array('last_id' => '0',),'menu_items' => array('last_id' => '0',),'transactions' => array('last_id' => '0',),'variation_translations' => array('last_id' => '0',),'user_activation_codes' => array('last_id' => '0',),'attribute_value_translations' => array('last_id' => '0',),'categories' => array('last_id' => '0',),'category_translations' => array('last_id' => '0',),'tags' => array('last_id' => '0',),'slider_slide_translations' => array('last_id' => '0',),'tax_class_translations' => array('last_id' => '0',),'variations' => array('last_id' => '0',),'updater_scripts' => array('last_id' => '0',),'coupons' => array('last_id' => '0',),'divisions' => array('last_id' => '0',),'role_translations' => array('last_id' => '0',),'oauth_clients' => array('last_id' => '0',),'attribute_set_translations' => array('last_id' => '0',),'search_terms' => array('last_id' => '0',),'zones' => array('last_id' => '0',),'product_variants' => array('last_id' => '0',),'translation_translations' => array('last_id' => '0',),'meta_data' => array('last_id' => '0',),'persistences' => array('last_id' => '0',),'roles' => array('last_id' => '0',),'slider_translations' => array('last_id' => '0',),'tax_classes' => array('last_id' => '0',),'migrations' => array('last_id' => '0',),'order_product_options' => array('last_id' => '0',),'blog_posts' => array('last_id' => '0',),'brand_translations' => array('last_id' => '0',),'flash_sale_translations' => array('last_id' => '0',),'translations' => array('last_id' => '0',),'blog_tag_translations' => array('last_id' => '0',),'throttle' => array('last_id' => '0',),'brands' => array('last_id' => '0',),'carts' => array('last_id' => '0',),'blog_categories' => array('last_id' => '0',),'oauth_auth_codes' => array('last_id' => '0',),'setting_translations' => array('last_id' => '0',),'option_values' => array('last_id' => '0',),'order_product_variations' => array('last_id' => '0',),'meta_data_translations' => array('last_id' => '0',),'settings' => array('last_id' => '0',),'slider_slides' => array('last_id' => '0',),'addresses' => array('last_id' => '0',),'variation_value_translations' => array('last_id' => '0',),'cities' => array('last_id' => '0',),'tax_rates' => array('last_id' => '0',),);
// Quota appears to be not currently in use.
$_QUOTA    = 0;
$_SAVE_LOG = false;
$_SCANDATE = time();
$_CHUNK_SIZE = '10485760'; // 1048576

add_to_log_section_start( 'GrabAndZip' );
add_to_log( $_POST, '_POST' );
add_to_log( $_SERVER['QUERY_STRING'], '_GET (raw)' );

$_SINGLEID = $_GET[ 'smart_single_download_id' ];

reduce_chunk_size_on_low_memory();

// Command to cleanup the bullet
if ( isset( $_GET[ 'cmd' ] ) && $_GET[ 'cmd' ] == 'complete' )
{
    // delete unique directory
    delete_unique_directory();

    // stop further processing
    exit;
}

check_and_terminate_on_mapi_errors();

// check if we have any tables to work with
if ( empty( $_TABLES ) )
{
    add_to_log( $_TABLES, 'Bullet received no TABLES' );
    wrap_up_the_scan( 'error', 'DB_SCAN_NO_TABLES', 'Bullet received no TABLES' );
}

init_DB_creds_based_on_platform();

add_to_log( print_r( $_TABLES, true) , '_TABLES' );
add_to_log( $_QUOTA, '_QUOTA' );
add_to_log( $_SCANDATE, '_SCANDATE' );


// We're good to continue, unless we are trying to run the same bullet again - prevent it!
if ( bullet_is_locked() )
{
    wrap_up_the_scan( 'error', 'DB_SCAN_PROCESS_LOCKED', 'Bullet is locked' );
}
else
{
    lock_the_bullet();
}


// For Generic DB, we'll already have the Queue ID from the earlier s3_init call
switch( $_PLATFORM )
{
    case 'wordpress':
    case 'joomla':
        $_SINGLEID = handle_s3_init();
        break;

    case 'other':
    default:
        if ( ! $_SINGLEID ) {
            wrap_up_the_scan( 'error', 'DB_SCAN_NO_PROCESS_ID', 'Received no SMART Single Download ID' );
        }
        break;
}


$recs_pulled_total = 0;
$current     = null; // current($_TABLES);

$db = getDbObj();

$t1 = microtime(true);
add_to_log('','Tables Summary');
foreach( $_TABLES AS $table_name => $current ) {
    // original logic: if any of these are not satisfied, then loop is over
    if ( !within_quota($recs_pulled_total) )
    {
        break;
    }

    // table info
    $_FIRST_ID = $current['last_id'];
    $last_id   = try_json_decode( $current['last_id'] );
    $tinfo     = $db->table_info( $table_name );
    $idcol     = try_json_decode( $tinfo['idcol'] );

    // last scanned
    $last_scanned = isset( $current['last_scanned'] ) ? $current['last_scanned'] : '';

    // set up the loop; remember, $_QUOTA < 1 means no limit
    $recs_this_table = 0;

    // This loop will have 2 exist scenarios based on the kind of tables it processes.
    // 1: Table rows count returned in an iteraion is 0.
    //    ^ This means table has no rows, or there was a SQL error, and table processing should end.
    //      This exit also includes special tables with "No ID" which will iterate unlimited + unordered results record by record,
    //      finally reaching the empty record.
    // 2: Table rows count returned in an iteraion is less than an iteration could pull AND table has a defined ID column.
    //    ^ This means we got the last batch of records and table processing should end.
    while ( keep_pulling( $recs_this_table, $db ) ) {

        $recent_rows  = $db->recent_rows( $table_name, $idcol, $last_id, $last_scanned );
        $rr_count     = count($recent_rows);
        $recs_this_table += $rr_count;
        $recs_pulled_total += $rr_count;
        write_rows_to_disk( $table_name, $recent_rows);

        // stop processing this table if no rows found
        if ( $rr_count == 0 ) {
            break;
        }

        // stop processing this table if we got less rows than query limit (we found all there was)
        // This does not apply to table with no ID, which will be fetched one row at a time (exit condition for those is "$rr_count == 0" above)
        if ( $idcol !== null && $rr_count < MAX_ROWS_PER_QUERY ) {
            break;
        }

        // update last_id so we can fetch some more
        $last_row = end($recent_rows);
        // handle case with multi-column ID
        if ( is_array( $idcol ) )
        {
            $last_id = array();
            foreach( $idcol AS $col )
            {
                $last_id[ $col ] = $last_row[ $col ];
            }
        }
        // original logic - make sure ID col exists
        else if ( $idcol )
        {
            $last_id = $last_row[$idcol];
        }
    }

    // for wp_posts only we will also search by post_modified
    if ( $_PLATFORM == 'wordpress' && !empty( $current['timestamp'] ) && strpos( $table_name, '_posts' ) !== false )
    {
        $recent_rows = $db->recent_rows(
            $table_name,
            $idcol,
            $_FIRST_ID,
            date( 'Y-m-d H:i:s', $current['timestamp'] )
        );

        $rr_count     = count($recent_rows);
        $recs_pulled_total += $rr_count;
        write_rows_to_disk( $table_name, $recent_rows);
    }

    add_to_log( "{$table_name}: {$recs_this_table} records pulled; {$recs_pulled_total} running total.");
}

add_to_log( microtime(true) - $t1, 'Total time to query and write tables' );


// NEW logic: CSVs with data > ZIP them all > Chunk the ZIP > Encrypt chunks
$dir = get_our_path('.', ".$_UNIQUE",'');
$csv_files = glob($dir . '*.csv');
$out_zip   = $dir; // by ref - will be updated with an actual ZIP file path
if ( archive_files($csv_files, $out_zip, $dir) ) {
    add_to_log( $out_zip, 'archived file' );
}
else
{
    wrap_up_the_scan( 'error', 'DB_SCAN_ZIP_FAILED', array( 'csv_files_count' => count($csv_files), 'zip' => $out_zip, 'dir' => $dir ) );
}

$zip_size = filesize( $out_zip );
add_to_log( $zip_size, "Original ZIP size");
$zip_fp = fopen($out_zip, 'rb');
$zip_md5 = md5_file($out_zip);
$chunk_counter = 0;
$chunk_sizes = array();
$bytes_written = 0;
while (!feof($zip_fp)) {
    $contents = fread($zip_fp, $_CHUNK_SIZE);
    $chunk_filename = "{$out_zip}.{$chunk_counter}";

    $chunk_enc_contents = encrypt_string($contents);
    unset($contents);

    $chunk_bytes = file_put_contents($chunk_filename, $chunk_enc_contents);
    unset($chunk_enc_contents);
    add_to_log( $chunk_bytes, "bytes written to {$chunk_filename}");

    $chunk_sizes[$chunk_filename] = $chunk_bytes;
    $bytes_written += $chunk_bytes;
    $chunk_counter++;
}
fclose($zip_fp);

// cleanup the original ZIP
unlink( $out_zip );

// add a descriptor file
$descriptor_content = json_encode( array(
    'zip_md5'       => $zip_md5,
    'chunks'        => array_values( $chunk_sizes ),
), JSON_FORCE_OBJECT );
file_put_contents( $out_zip . $descriptor_ext, $descriptor_content );


$zip_base_url = ltrim( str_replace( dirname( __FILE__ ), '', $out_zip ), '/' );

// Successful completion
wrap_up_the_scan();

exit;

function get_csv_filename($table_name) {
    global $_SCANDATE;

    return $table_name . '-' . $_SCANDATE;
}

function keep_pulling($recs_this_round, $db, $per_table = MAX_ROWS_TABLE) {
    /*global $_QUOTA;
    if ( $_QUOTA < 1 ) {
        return true;
    }*/

    if ( $recs_this_round < $per_table )
    {
        return true;
    }
    else
    {
        $db->_close(); // make sure to terminate any unbuffered queries
        return false;
    }
}

function within_quota( $records_pulled ) {
    global $_QUOTA;
    if ( $_QUOTA < 1 ) {
        return true;
    }

    return $records_pulled < $_QUOTA;
}

function build_csv_layout($array, $idcol=null) {

    $return = array();

    // if ID col is null, then we need to hash the entire row!
    if ( $idcol === null )
    {
        $values_combined = array();
        // Turn this into 2-step process: first, store all the original values...
        foreach ( $array as $col => $val )
        {
            $return[ $col ] = base64_encode( $val );
            $values_combined[] = $val;
        }

        // ...then combined all values to get a single md5 hash...
        $values_combined_md5 = md5( implode( '|', $values_combined ) );

        // ...and assign that hash to all the fields.
        foreach ( $array as $col => $val )
        {
            $return[ $col . '_md5hash' ] = $values_combined_md5;
        }
    }
    else
    // original logic:
    {
        foreach ( $array as $col => $val )
        {

            $return[ $col ] = $val === null ? null : base64_encode( $val );
            $return[ $col.'_md5hash' ] = $val === null ? null : md5( $val );
        }
    }

    return $return;
}

function write_rows_to_disk($table_name, $rows) {

    if ( !is_array($rows) || count($rows) < 1 ) {
        return;
    }

    $db    = getDbObj();
    $tinfo = $db->table_info($table_name);
    $idcol = $tinfo['idcol'];

    // convert mysql response to csv needed layout
    $new_rows = array();
    foreach ( $rows as $key => $val ) {
        $new_rows[ $key ] = build_csv_layout( $val, $idcol );
    }

    global $_UNIQUE;
    $file_name = get_csv_filename($table_name);
    $path = get_our_path('.', ".$_UNIQUE", "$file_name.csv");
    if ( !is_dir(dirname($path)) ) {
        mkdir( dirname($path), 0755, true );
    }

    $write_cols = false;
    if ( !file_exists($path) ) {
        $write_cols = true;
    }
//    add_to_log($path, 'before open file to append');
    $fp = fopen($path, 'a');

 //   add_to_log($fp, 'open file to append');

    if ( $write_cols ) {
        fputcsv($fp, array_keys($new_rows[0]));

    }

    foreach( $new_rows as $new_row ) {
        fputcsv($fp, array_values($new_row));
    }

    fclose($fp);
}

function wrap_up_the_scan( $status = 'ok', $err_token = '', $err_tech_details = null )
{
    global $_TOKEN, $_SITEID, $_SINGLEID, $_CLIENTID, $_FEATURECODE, $_SAVE_LOG, $zip_base_url, $descriptor_ext;

    add_to_log( $err_token, "wrap_up_the_scan with status {$status}" );
    add_to_log( $err_tech_details, "Returned technical error details, if any" );

    $s3_queue_success = true;
    if ( $status == 'ok' && $zip_base_url != null )
    {
        // prepare static params for s3 call and add dynamic ones later
        $s3_params = array(
            'site_id'       => $_SITEID,
            'queue_id'      => $_SINGLEID,
            'client_id'     => $_CLIENTID,
            'feature_code'  => $_FEATURECODE,
            'status'        => $status,
            'url'           => $zip_base_url,
            'zip_file_info' => $zip_base_url . $descriptor_ext,
        );

        $mapi_post_response = mapi_post($_TOKEN, 's3_queue', $s3_params);

        // error out of not approved
        $response_decoded = json_decode( $mapi_post_response, true );
        if ( !(
            isset( $response_decoded['responses'][0]['data']['s3_status'] ) && $response_decoded['responses'][0]['data']['s3_status'] == 'approved'
            &&
            isset( $response_decoded['responses'][0]['data']['queue_id'] )  && is_numeric( $response_decoded['responses'][0]['data']['queue_id'] )
        ) ) {
            $s3_queue_success = false;
            $err_token = 'CURL_MAPI_ERR';
            if ( isset( $response_decoded['responses'][0]['data'] ) )
            {
                $err_tech_details = json_encode( $response_decoded['responses'][0]['data'] );
            }
        }
    }

    // send all errors to new endpoint
    if( !$s3_queue_success || $status != 'ok' )
    {
        update_scan_on_error( $err_token, $err_tech_details, false );
    }

    if ( bullet_is_locked() )
    {
        unlock_the_bullet();
    }

    // this will scan whatever's left from faulty scans/feature checks/restores and remove them all
    cleanup_old_tmp_trash();

    $run_time = log_bullet_run_time();

    echo_enc( "done with grab and zip in {$run_time}s.");

    output_clean();

    // drop log if scan finished with no errors
    !$_SAVE_LOG and delete_log_file();

    exit;
}

# GrabAndZip - END
