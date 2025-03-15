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
$_TOKEN    = '299abbbd6805f8c0b9a80ef32250e823';
$_UNIQUE   = '015da542f7df3be37fcd5d71b56c1a18';

// New params used to support Generic DB
$_PLATFORM =    'other';
$_FEATURECODE = 'backup_db';

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
add_to_log( 'Ifsnop\Mysqldump is loaded into memory.' );
/**
 * PHP version of mysqldump cli that comes with MySQL.
 *
 * Tags: mysql mysqldump pdo php7 php5 database php sql hhvm mariadb mysql-backup.
 *
 * @category Library
 * @package  Ifsnop\Mysqldump
 * @author   Diego Torres <ifsnop@github.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/ifsnop/mysqldump-php
 *
 */


/**
 * Class Mysqldump.
 *
 * @category Library
 * @author   Diego Torres <ifsnop@github.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/ifsnop/mysqldump-php
 *
 */
class Mysqldump
{

    // Same as mysqldump.
    const MAXLINESIZE = 1000000;

    // List of available compression methods as constants.
    const GZIP  = 'Gzip';
    const BZIP2 = 'Bzip2';
    const NONE  = 'None';
    const GZIPSTREAM = 'Gzipstream';

    // List of available connection strings.
    const UTF8    = 'utf8';
    const UTF8MB4 = 'utf8mb4';
    const BINARY = 'binary';

    /**
     * Database username.
     * @var string
     */
    public $user;

    /**
     * Database password.
     * @var string
     */
    public $pass;

    /**
     * Connection string for PDO.
     * @var string
     */
    public $dsn;

    /**
     * Destination filename, defaults to stdout.
     * @var string
     */
    public $fileName = 'php://stdout';

    // Internal stuff.
    private $tables = array();
    private $views = array();
    private $triggers = array();
    private $procedures = array();
    private $functions = array();
    private $events = array();
    protected $dbHandler = null;
    private $dbType = "";
    private $compressManager;
    private $typeAdapter;
    protected $dumpSettings = array();
    protected $pdoSettings = array();
    private $version;
    private $tableColumnTypes = array();
    private $transformTableRowCallable;
    private $transformColumnValueCallable;
    private $infoCallable;

    /**
     * Database name, parsed from dsn.
     * @var string
     */
    private $dbName;

    /**
     * Host name, parsed from dsn.
     * @var string
     */
    private $host;

    /**
     * Dsn string parsed as an array.
     * @var array
     */
    private $dsnArray = array();

    /**
     * Keyed on table name, with the value as the conditions.
     * e.g. - 'users' => 'date_registered > NOW() - INTERVAL 6 MONTH'
     *
     * @var array
     */
    private $tableWheres = array();
    private $tableLimits = array();

    protected $dumpSettingsDefault = array(
        'include-tables' => array(),
        'exclude-tables' => array(),
        'include-views' => array(),
        'compress' => Mysqldump::NONE,
        'init_commands' => array(),
        'no-data' => array(),
        'if-not-exists' => false,
        'reset-auto-increment' => false,
        'add-drop-database' => false,
        'add-drop-table' => false,
        'add-drop-trigger' => true,
        'add-locks' => true,
        'complete-insert' => false,
        'databases' => false,
        'default-character-set' => Mysqldump::UTF8,
        'disable-keys' => true,
        'extended-insert' => true,
        'events' => false,
        'hex-blob' => true, /* faster than escaped content */
        'insert-ignore' => false,
        'net_buffer_length' => self::MAXLINESIZE,
        'no-autocommit' => true,
        'no-create-db' => false,
        'no-create-info' => false,
        'lock-tables' => true,
        'routines' => false,
        'single-transaction' => true,
        'skip-triggers' => false,
        'skip-tz-utc' => false,
        'skip-comments' => false,
        'skip-dump-date' => false,
        'skip-definer' => false,
        'where' => '',
        /* deprecated */
        'disable-foreign-keys-check' => true
    );

    protected $pdoSettingsDefault = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );

    /**
     * Constructor of Mysqldump. Note that in the case of an SQLite database
     * connection, the filename must be in the $db parameter.
     *
     * @param string $dsn        PDO DSN connection string
     * @param string $user       SQL account username
     * @param string $pass       SQL account password
     * @param array  $dumpSettings SQL database settings
     * @param array  $pdoSettings  PDO configured attributes
     */
    public function __construct(
        $dsn = '',
        $user = '',
        $pass = '',
        $dumpSettings = array(),
        $pdoSettings = array()
    ) {

        $this->user = $user;
        $this->pass = $pass;
        $this->parseDsn($dsn);

        // This drops MYSQL dependency, only use the constant if it's defined.
        if ("mysql" === $this->dbType) {
            $this->pdoSettingsDefault[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = false;
        }

        $this->pdoSettings = array_replace_recursive($this->pdoSettingsDefault, $pdoSettings);
        $this->dumpSettings = array_replace_recursive($this->dumpSettingsDefault, $dumpSettings);
        $this->dumpSettings['init_commands'][] = "SET NAMES ".$this->dumpSettings['default-character-set'];

        if (false === $this->dumpSettings['skip-tz-utc']) {
            $this->dumpSettings['init_commands'][] = "SET TIME_ZONE='+00:00'";
        }

        $diff = array_diff(array_keys($this->dumpSettings), array_keys($this->dumpSettingsDefault));
        if (count($diff) > 0) {
            throw new Exception("Unexpected value in dumpSettings: (".implode(",", $diff).")");
        }

        if (!is_array($this->dumpSettings['include-tables']) ||
            !is_array($this->dumpSettings['exclude-tables'])) {
            throw new Exception("Include-tables and exclude-tables should be arrays");
        }

        // If no include-views is passed in, dump the same views as tables, mimic mysqldump behaviour.
        if (!isset($dumpSettings['include-views'])) {
            $this->dumpSettings['include-views'] = $this->dumpSettings['include-tables'];
        }

        // Create a new compressManager to manage compressed output
        $this->compressManager = CompressManagerFactory::create($this->dumpSettings['compress']);

        add_to_log( 'Ifsnop\Mysqldump is initialized.' );
    }

    /**
     * Destructor of Mysqldump. Unsets dbHandlers and database objects.
     */
    public function __destruct()
    {
        $this->dbHandler = null;
    }

    /**
     * Keyed by table name, with the value as the conditions:
     * e.g. 'users' => 'date_registered > NOW() - INTERVAL 6 MONTH AND deleted=0'
     *
     * @param array $tableWheres
     */
    public function setTableWheres(array $tableWheres)
    {
        $this->tableWheres = $tableWheres;
    }

    /**
     * @param $tableName
     *
     * @return boolean|mixed
     */
    public function getTableWhere($tableName)
    {
        if (!empty($this->tableWheres[$tableName])) {
            return $this->tableWheres[$tableName];
        } elseif ($this->dumpSettings['where']) {
            return $this->dumpSettings['where'];
        }

        return false;
    }

    /**
     * Keyed by table name, with the value as the numeric limit:
     * e.g. 'users' => 3000
     *
     * @param array $tableLimits
     */
    public function setTableLimits(array $tableLimits)
    {
        $this->tableLimits = $tableLimits;
    }

    /**
     * Returns the LIMIT for the table.  Must be numeric to be returned.
     * @param $tableName
     * @return boolean
     */
    public function getTableLimit($tableName)
    {
        if (!isset($this->tableLimits[$tableName])) {
            return false;
        }

        $limit = $this->tableLimits[$tableName];
        if (!is_numeric($limit)) {
            return false;
        }

        return $limit;
    }

    /**
    * Import supplied SQL file
    * @param string $path Absolute path to imported *.sql file
    */
    public function restore($path)
    {
        if(!$path || !is_file($path)){
            throw new Exception("File {$path} does not exist.");
        }

        $handle = fopen($path , 'rb');

        if(!$handle){
            throw new Exception("Failed reading file {$path}. Check access permissions.");
        }

        if(!$this->dbHandler){
            $this->connect();
        }

        $buffer = '';
        while ( !feof($handle) ) {
            $line = trim(fgets($handle));

            if (substr($line, 0, 2) == '--' || !$line) {
                continue; // skip comments
            }

            $buffer .= $line;

            // if it has a semicolon at the end, it's the end of the query
            if (';' == substr(rtrim($line), -1, 1)) {
                $this->dbHandler->exec($buffer);
                $buffer = '';
            }
        }

        fclose($handle);
    }

    /**
     * Parse DSN string and extract dbname value
     * Several examples of a DSN string
     *   mysql:host=localhost;dbname=testdb
     *   mysql:host=localhost;port=3307;dbname=testdb
     *   mysql:unix_socket=/tmp/mysql.sock;dbname=testdb
     *
     * @param string $dsn dsn string to parse
     * @return boolean
     */
    private function parseDsn($dsn)
    {
        if (empty($dsn) || (false === ($pos = strpos($dsn, ":")))) {
            throw new Exception("Empty DSN string");
        }

        $this->dsn = $dsn;
        $this->dbType = strtolower(substr($dsn, 0, $pos)); // always returns a string

        if (empty($this->dbType)) {
            throw new Exception("Missing database type from DSN string");
        }

        $dsn = substr($dsn, $pos + 1);

        foreach (explode(";", $dsn) as $kvp) {
            $kvpArr = explode("=", $kvp);
            $this->dsnArray[strtolower($kvpArr[0])] = $kvpArr[1];
        }

        if (empty($this->dsnArray['host']) &&
            empty($this->dsnArray['unix_socket'])) {
            throw new Exception("Missing host from DSN string");
        }
        $this->host = (!empty($this->dsnArray['host'])) ?
            $this->dsnArray['host'] : $this->dsnArray['unix_socket'];

        if (empty($this->dsnArray['dbname'])) {
            throw new Exception("Missing database name from DSN string");
        }

        $this->dbName = $this->dsnArray['dbname'];

        return true;
    }

    /**
     * Connect with PDO.
     *
     * @return null
     */
    protected function connect()
    {
        // Connecting with PDO.
        try {
            switch ($this->dbType) {
                case 'sqlite':
                    $this->dbHandler = @new PDO("sqlite:".$this->dbName, null, null, $this->pdoSettings);
                    break;
                case 'mysql':
                case 'pgsql':
                case 'dblib':
                    $this->dbHandler = @new PDO(
                        $this->dsn,
                        $this->user,
                        $this->pass,
                        $this->pdoSettings
                    );
                    // Execute init commands once connected
                    foreach ($this->dumpSettings['init_commands'] as $stmt) {
                        $this->dbHandler->exec($stmt);
                    }
                    // Store server version
                    $this->version = $this->dbHandler->getAttribute(PDO::ATTR_SERVER_VERSION);
                    break;
                default:
                    throw new Exception("Unsupported database type (".$this->dbType.")");
            }
        } catch (PDOException $e) {
            throw new Exception(
                "Connection to ".$this->dbType." failed with message: ".
                $e->getMessage()
            );
        }

        if (is_null($this->dbHandler)) {
            throw new Exception("Connection to ".$this->dbType."failed");
        }

        $this->dbHandler->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        $this->typeAdapter = TypeAdapterFactory::create($this->dbType, $this->dbHandler, $this->dumpSettings);
    }

    /**
     * Primary function, triggers dumping.
     *
     * @param string $filename  Name of file to write sql dump to
     * @return null
     * @throws \Exception
     */
    public function start($filename = '')
    {
        // Output file can be redefined here
        if (!empty($filename)) {
            $this->fileName = $filename;
        }

        // Connect to database
        $this->connect();

        // Create output file
        $this->compressManager->open($this->fileName);

        // Write some basic info to output file
        $this->compressManager->write($this->getDumpFileHeader());

        // Store server settings and use sanner defaults to dump
        $this->compressManager->write(
            $this->typeAdapter->backup_parameters()
        );

        if ($this->dumpSettings['databases']) {
            $this->compressManager->write(
                $this->typeAdapter->getDatabaseHeader($this->dbName)
            );
            if ($this->dumpSettings['add-drop-database']) {
                $this->compressManager->write(
                    $this->typeAdapter->add_drop_database($this->dbName)
                );
            }
        }

        // Get table, view, trigger, procedures, functions and events structures from
        // database.
        $this->getDatabaseStructureTables();
        $this->getDatabaseStructureViews();
        $this->getDatabaseStructureTriggers();
        $this->getDatabaseStructureProcedures();
        $this->getDatabaseStructureFunctions();
        $this->getDatabaseStructureEvents();

        if ($this->dumpSettings['databases']) {
            $this->compressManager->write(
                $this->typeAdapter->databases($this->dbName)
            );
        }

        // If there still are some tables/views in include-tables array,
        // that means that some tables or views weren't found.
        // Give proper error and exit.
        // This check will be removed once include-tables supports regexps.
        if (0 < count($this->dumpSettings['include-tables'])) {
            $name = implode(",", $this->dumpSettings['include-tables']);
            throw new Exception("Table (".$name.") not found in database");
        }

        $this->exportTables();
        $this->exportTriggers();
        $this->exportFunctions();
        $this->exportProcedures();
        $this->exportViews();
        $this->exportEvents();

        // Restore saved parameters.
        $this->compressManager->write(
            $this->typeAdapter->restore_parameters()
        );
        // Write some stats to output file.
        $this->compressManager->write($this->getDumpFileFooter());
        // Close output file.
        $this->compressManager->close();

        return;
    }

    /**
     * Returns header for dump file.
     *
     * @return string
     */
    private function getDumpFileHeader()
    {
        $header = '';
        if (!$this->dumpSettings['skip-comments']) {
            // Some info about software, source and time
            $header = "-- mysqldump-php https://github.com/ifsnop/mysqldump-php".PHP_EOL.
                    "--".PHP_EOL.
                    "-- Host: {$this->host}\tDatabase: {$this->dbName}".PHP_EOL.
                    "-- ------------------------------------------------------".PHP_EOL;

            if (!empty($this->version)) {
                $header .= "-- Server version \t".$this->version.PHP_EOL;
            }

            if (!$this->dumpSettings['skip-dump-date']) {
                $header .= "-- Date: ".date('r').PHP_EOL.PHP_EOL;
            }
        }
        return $header;
    }

    /**
     * Returns footer for dump file.
     *
     * @return string
     */
    private function getDumpFileFooter()
    {
        $footer = '';
        if (!$this->dumpSettings['skip-comments']) {
            $footer .= '-- Dump completed';
            if (!$this->dumpSettings['skip-dump-date']) {
                $footer .= ' on: '.date('r');
            }
            $footer .= PHP_EOL;
        }

        return $footer;
    }

    /**
     * Reads table names from database.
     * Fills $this->tables array so they will be dumped later.
     *
     * @return null
     */
    private function getDatabaseStructureTables()
    {
        // Listing all tables from database
        if (empty($this->dumpSettings['include-tables'])) {
            // include all tables for now, blacklisting happens later
            foreach ($this->dbHandler->query($this->typeAdapter->show_tables($this->dbName)) as $row) {
                array_push($this->tables, current($row));
            }
        } else {
            // include only the tables mentioned in include-tables
            foreach ($this->dbHandler->query($this->typeAdapter->show_tables($this->dbName)) as $row) {
                if (in_array(current($row), $this->dumpSettings['include-tables'], true)) {
                    array_push($this->tables, current($row));
                    $elem = array_search(
                        current($row),
                        $this->dumpSettings['include-tables']
                    );
                    unset($this->dumpSettings['include-tables'][$elem]);
                }
            }
        }
        return;
    }

    /**
     * Reads view names from database.
     * Fills $this->tables array so they will be dumped later.
     *
     * @return null
     */
    private function getDatabaseStructureViews()
    {
        // Listing all views from database
        if (empty($this->dumpSettings['include-views'])) {
            // include all views for now, blacklisting happens later
            foreach ($this->dbHandler->query($this->typeAdapter->show_views($this->dbName)) as $row) {
                array_push($this->views, current($row));
            }
        } else {
            // include only the tables mentioned in include-tables
            foreach ($this->dbHandler->query($this->typeAdapter->show_views($this->dbName)) as $row) {
                if (in_array(current($row), $this->dumpSettings['include-views'], true)) {
                    array_push($this->views, current($row));
                    $elem = array_search(
                        current($row),
                        $this->dumpSettings['include-views']
                    );
                    unset($this->dumpSettings['include-views'][$elem]);
                }
            }
        }
        return;
    }

    /**
     * Reads trigger names from database.
     * Fills $this->tables array so they will be dumped later.
     *
     * @return null
     */
    private function getDatabaseStructureTriggers()
    {
        // Listing all triggers from database
        if (false === $this->dumpSettings['skip-triggers']) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_triggers($this->dbName)) as $row) {
                array_push($this->triggers, $row['Trigger']);
            }
        }
        return;
    }

    /**
     * Reads procedure names from database.
     * Fills $this->tables array so they will be dumped later.
     *
     * @return null
     */
    private function getDatabaseStructureProcedures()
    {
        // Listing all procedures from database
        if ($this->dumpSettings['routines']) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_procedures($this->dbName)) as $row) {
                array_push($this->procedures, $row['procedure_name']);
            }
        }
        return;
    }

    /**
     * Reads functions names from database.
     * Fills $this->tables array so they will be dumped later.
     *
     * @return null
     */
    private function getDatabaseStructureFunctions()
    {
        // Listing all functions from database
        if ($this->dumpSettings['routines']) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_functions($this->dbName)) as $row) {
                array_push($this->functions, $row['function_name']);
            }
        }
        return;
    }

    /**
     * Reads event names from database.
     * Fills $this->tables array so they will be dumped later.
     *
     * @return null
     */
    private function getDatabaseStructureEvents()
    {
        // Listing all events from database
        if ($this->dumpSettings['events']) {
            foreach ($this->dbHandler->query($this->typeAdapter->show_events($this->dbName)) as $row) {
                array_push($this->events, $row['event_name']);
            }
        }
        return;
    }

    /**
     * Compare if $table name matches with a definition inside $arr
     * @param $table string
     * @param $arr array with strings or patterns
     * @return boolean
     */
    private function matches($table, $arr)
    {
        $match = false;

        foreach ($arr as $pattern) {
            if ('/' != $pattern[0]) {
                continue;
            }
            if (1 == preg_match($pattern, $table)) {
                $match = true;
            }
        }

        return in_array($table, $arr) || $match;
    }

    /**
     * Exports all the tables selected from database
     *
     * @return null
     */
    private function exportTables()
    {
        // Exporting tables one by one
        foreach ($this->tables as $table) {
            if ($this->matches($table, $this->dumpSettings['exclude-tables'])) {
                continue;
            }
            $this->getTableStructure($table);
            if (false === $this->dumpSettings['no-data']) { // don't break compatibility with old trigger
                $this->listValues($table);
            } elseif (true === $this->dumpSettings['no-data']
                 || $this->matches($table, $this->dumpSettings['no-data'])) {
                continue;
            } else {
                $this->listValues($table);
            }
        }
    }

    /**
     * Exports all the views found in database
     *
     * @return null
     */
    private function exportViews()
    {
        if (false === $this->dumpSettings['no-create-info']) {
            // Exporting views one by one
            foreach ($this->views as $view) {
                if ($this->matches($view, $this->dumpSettings['exclude-tables'])) {
                    continue;
                }
                $this->tableColumnTypes[$view] = $this->getTableColumnTypes($view);
                $this->getViewStructureTable($view);
            }
            foreach ($this->views as $view) {
                if ($this->matches($view, $this->dumpSettings['exclude-tables'])) {
                    continue;
                }
                $this->getViewStructureView($view);
            }
        }
    }

    /**
     * Exports all the triggers found in database
     *
     * @return null
     */
    private function exportTriggers()
    {
        // Exporting triggers one by one
        foreach ($this->triggers as $trigger) {
            $this->getTriggerStructure($trigger);
        }

    }

    /**
     * Exports all the procedures found in database
     *
     * @return null
     */
    private function exportProcedures()
    {
        // Exporting triggers one by one
        foreach ($this->procedures as $procedure) {
            $this->getProcedureStructure($procedure);
        }
    }

    /**
     * Exports all the functions found in database
     *
     * @return null
     */
    private function exportFunctions()
    {
        // Exporting triggers one by one
        foreach ($this->functions as $function) {
            $this->getFunctionStructure($function);
        }
    }

    /**
     * Exports all the events found in database
     *
     * @return null
     */
    private function exportEvents()
    {
        // Exporting triggers one by one
        foreach ($this->events as $event) {
            $this->getEventStructure($event);
        }
    }

    /**
     * Table structure extractor
     *
     * @todo move specific mysql code to typeAdapter
     * @param string $tableName  Name of table to export
     * @return null
     */
    private function getTableStructure($tableName)
    {
        if (!$this->dumpSettings['no-create-info']) {
            $ret = '';
            if (!$this->dumpSettings['skip-comments']) {
                $ret = "--".PHP_EOL.
                    "-- Table structure for table `$tableName`".PHP_EOL.
                    "--".PHP_EOL.PHP_EOL;
            }
            $stmt = $this->typeAdapter->show_create_table($tableName);
            foreach ($this->dbHandler->query($stmt) as $r) {
                $this->compressManager->write($ret);
                if ($this->dumpSettings['add-drop-table']) {
                    $this->compressManager->write(
                        $this->typeAdapter->drop_table($tableName)
                    );
                }
                $this->compressManager->write(
                    $this->typeAdapter->create_table($r)
                );
                break;
            }
        }
        $this->tableColumnTypes[$tableName] = $this->getTableColumnTypes($tableName);
        return;
    }

    /**
     * Store column types to create data dumps and for Stand-In tables
     *
     * @param string $tableName  Name of table to export
     * @return array type column types detailed
     */

    private function getTableColumnTypes($tableName)
    {
        $columnTypes = array();
        $columns = $this->dbHandler->query(
            $this->typeAdapter->show_columns($tableName)
        );
        $columns->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($columns as $key => $col) {
            $types = $this->typeAdapter->parseColumnType($col);
            $columnTypes[$col['Field']] = array(
                'is_numeric'=> $types['is_numeric'],
                'is_blob' => $types['is_blob'],
                'type' => $types['type'],
                'type_sql' => $col['Type'],
                'is_virtual' => $types['is_virtual']
            );
        }

        return $columnTypes;
    }

    /**
     * View structure extractor, create table (avoids cyclic references)
     *
     * @todo move mysql specific code to typeAdapter
     * @param string $viewName  Name of view to export
     * @return null
     */
    private function getViewStructureTable($viewName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--".PHP_EOL.
                "-- Stand-In structure for view `{$viewName}`".PHP_EOL.
                "--".PHP_EOL.PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_view($viewName);

        // create views as tables, to resolve dependencies
        foreach ($this->dbHandler->query($stmt) as $r) {
            if ($this->dumpSettings['add-drop-table']) {
                $this->compressManager->write(
                    $this->typeAdapter->drop_view($viewName)
                );
            }

            $this->compressManager->write(
                $this->createStandInTable($viewName)
            );
            break;
        }
    }

    /**
     * Write a create table statement for the table Stand-In, show create
     * table would return a create algorithm when used on a view
     *
     * @param string $viewName  Name of view to export
     * @return string create statement
     */
    public function createStandInTable($viewName)
    {
        $ret = array();
        foreach ($this->tableColumnTypes[$viewName] as $k => $v) {
            $ret[] = "`{$k}` {$v['type_sql']}";
        }
        $ret = implode(PHP_EOL.",", $ret);

        $ret = "CREATE TABLE IF NOT EXISTS `$viewName` (".
            PHP_EOL.$ret.PHP_EOL.");".PHP_EOL;

        return $ret;
    }

    /**
     * View structure extractor, create view
     *
     * @todo move mysql specific code to typeAdapter
     * @param string $viewName  Name of view to export
     * @return null
     */
    private function getViewStructureView($viewName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--".PHP_EOL.
                "-- View structure for view `{$viewName}`".PHP_EOL.
                "--".PHP_EOL.PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_view($viewName);

        // create views, to resolve dependencies
        // replacing tables with views
        foreach ($this->dbHandler->query($stmt) as $r) {
            // because we must replace table with view, we should delete it
            $this->compressManager->write(
                $this->typeAdapter->drop_view($viewName)
            );
            $this->compressManager->write(
                $this->typeAdapter->create_view($r)
            );
            break;
        }
    }

    /**
     * Trigger structure extractor
     *
     * @param string $triggerName  Name of trigger to export
     * @return null
     */
    private function getTriggerStructure($triggerName)
    {
        $stmt = $this->typeAdapter->show_create_trigger($triggerName);
        foreach ($this->dbHandler->query($stmt) as $r) {
            if ($this->dumpSettings['add-drop-trigger']) {
                $this->compressManager->write(
                    $this->typeAdapter->add_drop_trigger($triggerName)
                );
            }
            $this->compressManager->write(
                $this->typeAdapter->create_trigger($r)
            );
            return;
        }
    }

    /**
     * Procedure structure extractor
     *
     * @param string $procedureName  Name of procedure to export
     * @return null
     */
    private function getProcedureStructure($procedureName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--".PHP_EOL.
                "-- Dumping routines for database '".$this->dbName."'".PHP_EOL.
                "--".PHP_EOL.PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_procedure($procedureName);
        foreach ($this->dbHandler->query($stmt) as $r) {
            $this->compressManager->write(
                $this->typeAdapter->create_procedure($r)
            );
            return;
        }
    }

    /**
     * Function structure extractor
     *
     * @param string $functionName  Name of function to export
     * @return null
     */
    private function getFunctionStructure($functionName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--".PHP_EOL.
                "-- Dumping routines for database '".$this->dbName."'".PHP_EOL.
                "--".PHP_EOL.PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_function($functionName);
        foreach ($this->dbHandler->query($stmt) as $r) {
            $this->compressManager->write(
                $this->typeAdapter->create_function($r)
            );
            return;
        }
    }

    /**
     * Event structure extractor
     *
     * @param string $eventName  Name of event to export
     * @return null
     */
    private function getEventStructure($eventName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $ret = "--".PHP_EOL.
                "-- Dumping events for database '".$this->dbName."'".PHP_EOL.
                "--".PHP_EOL.PHP_EOL;
            $this->compressManager->write($ret);
        }
        $stmt = $this->typeAdapter->show_create_event($eventName);
        foreach ($this->dbHandler->query($stmt) as $r) {
            $this->compressManager->write(
                $this->typeAdapter->create_event($r)
            );
            return;
        }
    }

    /**
     * Prepare values for output
     *
     * @param string $tableName Name of table which contains rows
     * @param array $row Associative array of column names and values to be
     *   quoted
     *
     * @return array
     */
    private function prepareColumnValues($tableName, array $row)
    {
        $ret = array();
        $columnTypes = $this->tableColumnTypes[$tableName];

        if ($this->transformTableRowCallable) {
            $row = call_user_func($this->transformTableRowCallable, $tableName, $row);
        }

        foreach ($row as $colName => $colValue) {
            if ($this->transformColumnValueCallable) {
                $colValue = call_user_func($this->transformColumnValueCallable, $tableName, $colName, $colValue, $row);
            }

            $ret[] = $this->escape($colValue, $columnTypes[$colName]);
        }

        return $ret;
    }

    /**
     * Escape values with quotes when needed
     *
     * @param string $tableName Name of table which contains rows
     * @param array $row Associative array of column names and values to be quoted
     *
     * @return string
     */
    private function escape($colValue, $colType)
    {
        if (is_null($colValue)) {
            return "NULL";
        } elseif ($this->dumpSettings['hex-blob'] && $colType['is_blob']) {
            if ($colType['type'] == 'bit' || !empty($colValue)) {
                return "0x{$colValue}";
            } else {
                return "''";
            }
        } elseif ($colType['is_numeric']) {
            return $colValue;
        }

        return $this->dbHandler->quote($colValue);
    }

    /**
     * Set a callable that will be used to transform table rows
     *
     * @param callable $callable
     *
     * @return void
     */
    public function setTransformTableRowHook($callable)
    {
        $this->transformTableRowCallable = $callable;
    }

    /**
     * Set a callable that will be used to transform column values
     *
     * @param callable $callable
     *
     * @return void
     *
     * @deprecated Use setTransformTableRowHook instead for better performance
     */
    public function setTransformColumnValueHook($callable)
    {
        $this->transformColumnValueCallable = $callable;
    }

    /**
     * Set a callable that will be used to report dump information
     *
     * @param callable $callable
     *
     * @return void
     */
    public function setInfoHook($callable)
    {
        $this->infoCallable = $callable;
    }

    /**
     * Table rows extractor
     *
     * @param string $tableName  Name of table to export
     *
     * @return null
     */
    private function listValues($tableName)
    {
        $this->prepareListValues($tableName);

        $onlyOnce = true;
        $lineSize = 0;

        // colStmt is used to form a query to obtain row values
        $colStmt = $this->getColumnStmt($tableName);
        // colNames is used to get the name of the columns when using complete-insert
        if ($this->dumpSettings['complete-insert']) {
            $colNames = $this->getColumnNames($tableName);
        }

        $stmt = "SELECT ".implode(",", $colStmt)." FROM `$tableName`";

        // Table specific conditions override the default 'where'
        $condition = $this->getTableWhere($tableName);

        if ($condition) {
            $stmt .= " WHERE {$condition}";
        }

        $limit = $this->getTableLimit($tableName);

        if ($limit !== false) {
            $stmt .= " LIMIT {$limit}";
        }

        $resultSet = $this->dbHandler->query($stmt);
        $resultSet->setFetchMode(PDO::FETCH_ASSOC);

        $ignore = $this->dumpSettings['insert-ignore'] ? '  IGNORE' : '';

        $count = 0;
        foreach ($resultSet as $row) {
            $count++;
            $vals = $this->prepareColumnValues($tableName, $row);
            if ($onlyOnce || !$this->dumpSettings['extended-insert']) {
                if ($this->dumpSettings['complete-insert']) {
                    $lineSize += $this->compressManager->write(
                        "INSERT$ignore INTO `$tableName` (".
                        implode(", ", $colNames).
                        ") VALUES (".implode(",", $vals).")"
                    );
                } else {
                    $lineSize += $this->compressManager->write(
                        "INSERT$ignore INTO `$tableName` VALUES (".implode(",", $vals).")"
                    );
                }
                $onlyOnce = false;
            } else {
                $lineSize += $this->compressManager->write(",(".implode(",", $vals).")");
            }
            if (($lineSize > $this->dumpSettings['net_buffer_length']) ||
                    !$this->dumpSettings['extended-insert']) {
                $onlyOnce = true;
                $lineSize = $this->compressManager->write(";".PHP_EOL);
            }
        }
        $resultSet->closeCursor();

        if (!$onlyOnce) {
            $this->compressManager->write(";".PHP_EOL);
        }

        $this->endListValues($tableName, $count);

        if ($this->infoCallable) {
            call_user_func($this->infoCallable, 'table', array('name' => $tableName, 'rowCount' => $count));
        }
    }

    /**
     * Table rows extractor, append information prior to dump
     *
     * @param string $tableName  Name of table to export
     *
     * @return null
     */
    public function prepareListValues($tableName)
    {
        if (!$this->dumpSettings['skip-comments']) {
            $this->compressManager->write(
                "--".PHP_EOL.
                "-- Dumping data for table `$tableName`".PHP_EOL.
                "--".PHP_EOL.PHP_EOL
            );
        }

        if ($this->dumpSettings['single-transaction']) {
            $this->dbHandler->exec($this->typeAdapter->setup_transaction());
            $this->dbHandler->exec($this->typeAdapter->start_transaction());
        }

        if ($this->dumpSettings['lock-tables'] && !$this->dumpSettings['single-transaction']) {
            $this->typeAdapter->lock_table($tableName);
        }

        if ($this->dumpSettings['add-locks']) {
            $this->compressManager->write(
                $this->typeAdapter->start_add_lock_table($tableName)
            );
        }

        if ($this->dumpSettings['disable-keys']) {
            $this->compressManager->write(
                $this->typeAdapter->start_add_disable_keys($tableName)
            );
        }

        // Disable autocommit for faster reload
        if ($this->dumpSettings['no-autocommit']) {
            $this->compressManager->write(
                $this->typeAdapter->start_disable_autocommit()
            );
        }

        return;
    }

    /**
     * Table rows extractor, close locks and commits after dump
     *
     * @param string $tableName Name of table to export.
     * @param integer    $count     Number of rows inserted.
     *
     * @return void
     */
    public function endListValues($tableName, $count = 0)
    {
        if ($this->dumpSettings['disable-keys']) {
            $this->compressManager->write(
                $this->typeAdapter->end_add_disable_keys($tableName)
            );
        }

        if ($this->dumpSettings['add-locks']) {
            $this->compressManager->write(
                $this->typeAdapter->end_add_lock_table($tableName)
            );
        }

        if ($this->dumpSettings['single-transaction']) {
            $this->dbHandler->exec($this->typeAdapter->commit_transaction());
        }

        if ($this->dumpSettings['lock-tables'] && !$this->dumpSettings['single-transaction']) {
            $this->typeAdapter->unlock_table($tableName);
        }

        // Commit to enable autocommit
        if ($this->dumpSettings['no-autocommit']) {
            $this->compressManager->write(
                $this->typeAdapter->end_disable_autocommit()
            );
        }

        $this->compressManager->write(PHP_EOL);

        if (!$this->dumpSettings['skip-comments']) {
            $this->compressManager->write(
                "-- Dumped table `".$tableName."` with $count row(s)".PHP_EOL.
                '--'.PHP_EOL.PHP_EOL
            );
        }

        return;
    }

    /**
     * Build SQL List of all columns on current table which will be used for selecting
     *
     * @param string $tableName  Name of table to get columns
     *
     * @return array SQL sentence with columns for select
     */
    public function getColumnStmt($tableName)
    {
        $colStmt = array();
        foreach ($this->tableColumnTypes[$tableName] as $colName => $colType) {
            if ($colType['is_virtual']) {
                $this->dumpSettings['complete-insert'] = true;
                continue;
            } elseif ($colType['type'] == 'bit' && $this->dumpSettings['hex-blob']) {
                $colStmt[] = "LPAD(HEX(`{$colName}`),2,'0') AS `{$colName}`";
            } elseif ($colType['type'] == 'double' && PHP_VERSION_ID > 80100) {
                $colStmt[] = sprintf("CONCAT(`%s`) AS `%s`", $colName, $colName);
            } elseif ($colType['is_blob'] && $this->dumpSettings['hex-blob']) {
                $colStmt[] = "HEX(`{$colName}`) AS `{$colName}`";
            } else {
                $colStmt[] = "`{$colName}`";
            }
        }

        return $colStmt;
    }

    /**
     * Build SQL List of all columns on current table which will be used for inserting
     *
     * @param string $tableName  Name of table to get columns
     *
     * @return array columns for sql sentence for insert
     */
    public function getColumnNames($tableName)
    {
        $colNames = array();
        foreach ($this->tableColumnTypes[$tableName] as $colName => $colType) {
            if ($colType['is_virtual']) {
                $this->dumpSettings['complete-insert'] = true;
                continue;
            } else {
                $colNames[] = "`{$colName}`";
            }
        }
        return $colNames;
    }
}

/**
 * Enum with all available compression methods
 *
 */
abstract class CompressMethod
{
    public static $enums = array(
        Mysqldump::NONE,
        Mysqldump::GZIP,
        Mysqldump::BZIP2,
        Mysqldump::GZIPSTREAM,
    );

    /**
     * @param string $c
     * @return boolean
     */
    public static function isValid($c)
    {
        return in_array($c, self::$enums);
    }
}

abstract class CompressManagerFactory
{
    /**
     * @param string $c
     * @return CompressBzip2|CompressGzip|CompressNone
     */
    public static function create($c)
    {
        $c = ucfirst(strtolower($c));
        if (!CompressMethod::isValid($c)) {
            throw new Exception("Compression method ($c) is not defined yet");
        }

        $method = __NAMESPACE__."\\"."Compress".$c;

        return new $method;
    }
}

class CompressBzip2 extends CompressManagerFactory
{
    private $fileHandler = null;

    public function __construct()
    {
        if (!function_exists("bzopen")) {
            throw new Exception("Compression is enabled, but bzip2 lib is not installed or configured properly");
        }
    }

    /**
     * @param string $filename
     */
    public function open($filename)
    {
        $this->fileHandler = bzopen($filename, "w");
        if (false === $this->fileHandler) {
            throw new Exception("Output file is not writable");
        }

        return true;
    }

    public function write($str)
    {
        $bytesWritten = bzwrite($this->fileHandler, $str);
        if (false === $bytesWritten) {
            throw new Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    public function close()
    {
        return bzclose($this->fileHandler);
    }
}

class CompressGzip extends CompressManagerFactory
{
    private $fileHandler = null;

    public function __construct()
    {
        if (!function_exists("gzopen")) {
            throw new Exception("Compression is enabled, but gzip lib is not installed or configured properly");
        }
    }

    /**
     * @param string $filename
     */
    public function open($filename)
    {
        $this->fileHandler = gzopen($filename, "wb");
        if (false === $this->fileHandler) {
            throw new Exception("Output file is not writable");
        }

        return true;
    }

    public function write($str)
    {
        $bytesWritten = gzwrite($this->fileHandler, $str);
        if (false === $bytesWritten) {
            throw new Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    public function close()
    {
        return gzclose($this->fileHandler);
    }
}

class CompressNone extends CompressManagerFactory
{
    private $fileHandler = null;

    /**
     * @param string $filename
     */
    public function open($filename)
    {
        $this->fileHandler = fopen($filename, "wb");
        if (false === $this->fileHandler) {
            throw new Exception("Output file is not writable");
        }

        return true;
    }

    public function write($str)
    {
        $bytesWritten = fwrite($this->fileHandler, $str);
        if (false === $bytesWritten) {
            throw new Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    public function close()
    {
        return fclose($this->fileHandler);
    }
}

class CompressGzipstream extends CompressManagerFactory
{
    private $fileHandler = null;

    private $compressContext;

    /**
     * @param string $filename
     */
    public function open($filename)
    {
    $this->fileHandler = fopen($filename, "wb");
    if (false === $this->fileHandler) {
        throw new Exception("Output file is not writable");
    }

    $this->compressContext = deflate_init(ZLIB_ENCODING_GZIP, array('level' => 9));
    return true;
    }

    public function write($str)
    {

    $bytesWritten = fwrite($this->fileHandler, deflate_add($this->compressContext, $str, ZLIB_NO_FLUSH));
    if (false === $bytesWritten) {
        throw new Exception("Writting to file failed! Probably, there is no more free space left?");
    }
    return $bytesWritten;
    }

    public function close()
    {
    fwrite($this->fileHandler, deflate_add($this->compressContext, '', ZLIB_FINISH));
    return fclose($this->fileHandler);
    }
}

/**
 * Enum with all available TypeAdapter implementations
 *
 */
abstract class TypeAdapter
{
    public static $enums = array(
        "Sqlite",
        "Mysql"
    );

    /**
     * @param string $c
     * @return boolean
     */
    public static function isValid($c)
    {
        return in_array($c, self::$enums);
    }
}

/**
 * TypeAdapter Factory
 *
 */
abstract class TypeAdapterFactory
{
    protected $dbHandler = null;
    protected $dumpSettings = array();

    /**
     * @param string $c Type of database factory to create (Mysql, Sqlite,...)
     * @param PDO $dbHandler
     */
    public static function create($c, $dbHandler = null, $dumpSettings = array())
    {
        $c = ucfirst(strtolower($c));
        if (!TypeAdapter::isValid($c)) {
            throw new Exception("Database type support for ($c) not yet available");
        }
        $method = __NAMESPACE__."\\"."TypeAdapter".$c;
        return new $method($dbHandler, $dumpSettings);
    }

    public function __construct($dbHandler = null, $dumpSettings = array())
    {
        $this->dbHandler = $dbHandler;
        $this->dumpSettings = $dumpSettings;
    }

    /**
     * function databases Add sql to create and use database
     * @todo make it do something with sqlite
     */
    public function databases()
    {
        return "";
    }

    public function show_create_table($tableName)
    {
        return "SELECT tbl_name as 'Table', sql as 'Create Table' ".
            "FROM sqlite_master ".
            "WHERE type='table' AND tbl_name='$tableName'";
    }

    /**
     * function create_table Get table creation code from database
     * @todo make it do something with sqlite
     */
    public function create_table($row)
    {
        return "";
    }

    public function show_create_view($viewName)
    {
        return "SELECT tbl_name as 'View', sql as 'Create View' ".
            "FROM sqlite_master ".
            "WHERE type='view' AND tbl_name='$viewName'";
    }

    /**
     * function create_view Get view creation code from database
     * @todo make it do something with sqlite
     */
    public function create_view($row)
    {
        return "";
    }

    /**
     * function show_create_trigger Get trigger creation code from database
     * @todo make it do something with sqlite
     */
    public function show_create_trigger($triggerName)
    {
        return "";
    }

    /**
     * function create_trigger Modify trigger code, add delimiters, etc
     * @todo make it do something with sqlite
     */
    public function create_trigger($triggerName)
    {
        return "";
    }

    /**
     * function create_procedure Modify procedure code, add delimiters, etc
     * @todo make it do something with sqlite
     */
    public function create_procedure($procedureName)
    {
        return "";
    }

    /**
     * function create_function Modify function code, add delimiters, etc
     * @todo make it do something with sqlite
     */
    public function create_function($functionName)
    {
        return "";
    }

    public function show_tables()
    {
        return "SELECT tbl_name FROM sqlite_master WHERE type='table'";
    }

    public function show_views()
    {
        return "SELECT tbl_name FROM sqlite_master WHERE type='view'";
    }

    public function show_triggers()
    {
        return "SELECT name FROM sqlite_master WHERE type='trigger'";
    }

    public function show_columns()
    {
        if (func_num_args() != 1) {
            return "";
        }

        $args = func_get_args();

        return "pragma table_info({$args[0]})";
    }

    public function show_procedures()
    {
        return "";
    }

    public function show_functions()
    {
        return "";
    }

    public function show_events()
    {
        return "";
    }

    public function setup_transaction()
    {
        return "";
    }

    public function start_transaction()
    {
        return "BEGIN EXCLUSIVE";
    }

    public function commit_transaction()
    {
        return "COMMIT";
    }

    public function lock_table()
    {
        return "";
    }

    public function unlock_table()
    {
        return "";
    }

    public function start_add_lock_table()
    {
        return PHP_EOL;
    }

    public function end_add_lock_table()
    {
        return PHP_EOL;
    }

    public function start_add_disable_keys()
    {
        return PHP_EOL;
    }

    public function end_add_disable_keys()
    {
        return PHP_EOL;
    }

    public function start_disable_foreign_keys_check()
    {
        return PHP_EOL;
    }

    public function end_disable_foreign_keys_check()
    {
        return PHP_EOL;
    }

    public function add_drop_database()
    {
        return PHP_EOL;
    }

    public function add_drop_trigger()
    {
        return PHP_EOL;
    }

    public function drop_table()
    {
        return PHP_EOL;
    }

    public function drop_view()
    {
        return PHP_EOL;
    }

    /**
     * Decode column metadata and fill info structure.
     * type, is_numeric and is_blob will always be available.
     *
     * @param array $colType Array returned from "SHOW COLUMNS FROM tableName"
     * @return array
     */
    public function parseColumnType($colType)
    {
        return array();
    }

    public function backup_parameters()
    {
        return PHP_EOL;
    }

    public function restore_parameters()
    {
        return PHP_EOL;
    }
}

class TypeAdapterPgsql extends TypeAdapterFactory
{
}

class TypeAdapterDblib extends TypeAdapterFactory
{
}

class TypeAdapterSqlite extends TypeAdapterFactory
{
}

class TypeAdapterMysql extends TypeAdapterFactory
{
    const DEFINER_RE = 'DEFINER=`(?:[^`]|``)*`@`(?:[^`]|``)*`';


    // Numerical Mysql types
    public $mysqlTypes = array(
        'numerical' => array(
            'bit',
            'tinyint',
            'smallint',
            'mediumint',
            'int',
            'integer',
            'bigint',
            'real',
            'double',
            'float',
            'decimal',
            'numeric'
        ),
        'blob' => array(
            'tinyblob',
            'blob',
            'mediumblob',
            'longblob',
            'binary',
            'varbinary',
            'bit',
            'geometry', /* http://bugs.mysql.com/bug.php?id=43544 */
            'point',
            'linestring',
            'polygon',
            'multipoint',
            'multilinestring',
            'multipolygon',
            'geometrycollection',
        )
    );

    public function databases()
    {
        if ($this->dumpSettings['no-create-db']) {
           return "";
        }

        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        $databaseName = $args[0];

        $resultSet = $this->dbHandler->query("SHOW VARIABLES LIKE 'character_set_database';");
        $characterSet = $resultSet->fetchColumn(1);
        $resultSet->closeCursor();

        $resultSet = $this->dbHandler->query("SHOW VARIABLES LIKE 'collation_database';");
        $collationDb = $resultSet->fetchColumn(1);
        $resultSet->closeCursor();
        $ret = "";

        $ret .= "CREATE DATABASE /*!32312 IF NOT EXISTS*/ `{$databaseName}`".
            " /*!40100 DEFAULT CHARACTER SET {$characterSet} ".
            " COLLATE {$collationDb} */;".PHP_EOL.PHP_EOL.
            "USE `{$databaseName}`;".PHP_EOL.PHP_EOL;

        return $ret;
    }

    public function show_create_table($tableName)
    {
        return "SHOW CREATE TABLE `$tableName`";
    }

    public function show_create_view($viewName)
    {
        return "SHOW CREATE VIEW `$viewName`";
    }

    public function show_create_trigger($triggerName)
    {
        return "SHOW CREATE TRIGGER `$triggerName`";
    }

    public function show_create_procedure($procedureName)
    {
        return "SHOW CREATE PROCEDURE `$procedureName`";
    }

    public function show_create_function($functionName)
    {
        return "SHOW CREATE FUNCTION `$functionName`";
    }

    public function show_create_event($eventName)
    {
        return "SHOW CREATE EVENT `$eventName`";
    }

    public function create_table($row)
    {
        if (!isset($row['Create Table'])) {
            throw new Exception("Error getting table code, unknown output");
        }

        $createTable = $row['Create Table'];
        if ($this->dumpSettings['reset-auto-increment']) {
            $match = "/AUTO_INCREMENT=[0-9]+/s";
            $replace = "";
            $createTable = preg_replace($match, $replace, $createTable);
        }

		if ($this->dumpSettings['if-not-exists'] ) {
			$createTable = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $createTable);
        }

        $ret = "/*!40101 SET @saved_cs_client     = @@character_set_client */;".PHP_EOL.
            "/*!40101 SET character_set_client = ".$this->dumpSettings['default-character-set']." */;".PHP_EOL.
            $createTable.";".PHP_EOL.
            "/*!40101 SET character_set_client = @saved_cs_client */;".PHP_EOL.
            PHP_EOL;
        return $ret;
    }

    public function create_view($row)
    {
        $ret = "";
        if (!isset($row['Create View'])) {
            throw new Exception("Error getting view structure, unknown output");
        }

        $viewStmt = $row['Create View'];

        $definerStr = $this->dumpSettings['skip-definer'] ? '' : '/*!50013 \2 */'.PHP_EOL;

        if ($viewStmtReplaced = preg_replace(
            '/^(CREATE(?:\s+ALGORITHM=(?:UNDEFINED|MERGE|TEMPTABLE))?)\s+('
            .self::DEFINER_RE.'(?:\s+SQL SECURITY (?:DEFINER|INVOKER))?)?\s+(VIEW .+)$/',
            '/*!50001 \1 */'.PHP_EOL.$definerStr.'/*!50001 \3 */',
            $viewStmt,
            1
        )) {
            $viewStmt = $viewStmtReplaced;
        };

        $ret .= $viewStmt.';'.PHP_EOL.PHP_EOL;
        return $ret;
    }

    public function create_trigger($row)
    {
        $ret = "";
        if (!isset($row['SQL Original Statement'])) {
            throw new Exception("Error getting trigger code, unknown output");
        }

        $triggerStmt = $row['SQL Original Statement'];
        $definerStr = $this->dumpSettings['skip-definer'] ? '' : '/*!50017 \2*/ ';
        if ($triggerStmtReplaced = preg_replace(
            '/^(CREATE)\s+('.self::DEFINER_RE.')?\s+(TRIGGER\s.*)$/s',
            '/*!50003 \1*/ '.$definerStr.'/*!50003 \3 */',
            $triggerStmt,
            1
        )) {
            $triggerStmt = $triggerStmtReplaced;
        }

        $ret .= "DELIMITER ;;".PHP_EOL.
            $triggerStmt.";;".PHP_EOL.
            "DELIMITER ;".PHP_EOL.PHP_EOL;
        return $ret;
    }

    public function create_procedure($row)
    {
        $ret = "";
        if (!isset($row['Create Procedure'])) {
            throw new Exception("Error getting procedure code, unknown output. ".
                "Please check 'https://bugs.mysql.com/bug.php?id=14564'");
        }
        $procedureStmt = $row['Create Procedure'];
        if ($this->dumpSettings['skip-definer']) {
            if ($procedureStmtReplaced = preg_replace(
                '/^(CREATE)\s+('.self::DEFINER_RE.')?\s+(PROCEDURE\s.*)$/s',
                '\1 \3',
                $procedureStmt,
                1
            )) {
                $procedureStmt = $procedureStmtReplaced;
            }
        }

        $ret .= "/*!50003 DROP PROCEDURE IF EXISTS `".
            $row['Procedure']."` */;".PHP_EOL.
            "/*!40101 SET @saved_cs_client     = @@character_set_client */;".PHP_EOL.
            "/*!40101 SET character_set_client = ".$this->dumpSettings['default-character-set']." */;".PHP_EOL.
            "DELIMITER ;;".PHP_EOL.
            $procedureStmt." ;;".PHP_EOL.
            "DELIMITER ;".PHP_EOL.
            "/*!40101 SET character_set_client = @saved_cs_client */;".PHP_EOL.PHP_EOL;

        return $ret;
    }

    public function create_function($row)
    {
        $ret = "";
        if (!isset($row['Create Function'])) {
            throw new Exception("Error getting function code, unknown output. ".
                "Please check 'https://bugs.mysql.com/bug.php?id=14564'");
        }
        $functionStmt = $row['Create Function'];
        $characterSetClient = $row['character_set_client'];
        $collationConnection = $row['collation_connection'];
        $sqlMode = $row['sql_mode'];
        if ( $this->dumpSettings['skip-definer'] ) {
            if ($functionStmtReplaced = preg_replace(
                '/^(CREATE)\s+('.self::DEFINER_RE.')?\s+(FUNCTION\s.*)$/s',
                '\1 \3',
                $functionStmt,
                1
            )) {
                $functionStmt = $functionStmtReplaced;
            }
        }

        $ret .= "/*!50003 DROP FUNCTION IF EXISTS `".
            $row['Function']."` */;".PHP_EOL.
            "/*!40101 SET @saved_cs_client     = @@character_set_client */;".PHP_EOL.
            "/*!50003 SET @saved_cs_results     = @@character_set_results */ ;".PHP_EOL.
            "/*!50003 SET @saved_col_connection = @@collation_connection */ ;".PHP_EOL.
            "/*!40101 SET character_set_client = ".$characterSetClient." */;".PHP_EOL.
            "/*!40101 SET character_set_results = ".$characterSetClient." */;".PHP_EOL.
            "/*!50003 SET collation_connection  = ".$collationConnection." */ ;".PHP_EOL.
            "/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;".PHP_EOL.
            "/*!50003 SET sql_mode              = '".$sqlMode."' */ ;;".PHP_EOL.
            "/*!50003 SET @saved_time_zone      = @@time_zone */ ;;".PHP_EOL.
            "/*!50003 SET time_zone             = 'SYSTEM' */ ;;".PHP_EOL.
            "DELIMITER ;;".PHP_EOL.
            $functionStmt." ;;".PHP_EOL.
            "DELIMITER ;".PHP_EOL.
            "/*!50003 SET sql_mode              = @saved_sql_mode */ ;".PHP_EOL.
            "/*!50003 SET character_set_client  = @saved_cs_client */ ;".PHP_EOL.
            "/*!50003 SET character_set_results = @saved_cs_results */ ;".PHP_EOL.
            "/*!50003 SET collation_connection  = @saved_col_connection */ ;".PHP_EOL.
            "/*!50106 SET TIME_ZONE= @saved_time_zone */ ;".PHP_EOL.PHP_EOL;


        return $ret;
    }

    public function create_event($row)
    {
        $ret = "";
        if (!isset($row['Create Event'])) {
            throw new Exception("Error getting event code, unknown output. ".
                "Please check 'http://stackoverflow.com/questions/10853826/mysql-5-5-create-event-gives-syntax-error'");
        }
        $eventName = $row['Event'];
        $eventStmt = $row['Create Event'];
        $sqlMode = $row['sql_mode'];
        $definerStr = $this->dumpSettings['skip-definer'] ? '' : '/*!50117 \2*/ ';

        if ($eventStmtReplaced = preg_replace(
            '/^(CREATE)\s+('.self::DEFINER_RE.')?\s+(EVENT .*)$/',
            '/*!50106 \1*/ '.$definerStr.'/*!50106 \3 */',
            $eventStmt,
            1
        )) {
            $eventStmt = $eventStmtReplaced;
        }

        $ret .= "/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;".PHP_EOL.
            "/*!50106 DROP EVENT IF EXISTS `".$eventName."` */;".PHP_EOL.
            "DELIMITER ;;".PHP_EOL.
            "/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;".PHP_EOL.
            "/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;".PHP_EOL.
            "/*!50003 SET @saved_col_connection = @@collation_connection */ ;;".PHP_EOL.
            "/*!50003 SET character_set_client  = utf8 */ ;;".PHP_EOL.
            "/*!50003 SET character_set_results = utf8 */ ;;".PHP_EOL.
            "/*!50003 SET collation_connection  = utf8_general_ci */ ;;".PHP_EOL.
            "/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;".PHP_EOL.
            "/*!50003 SET sql_mode              = '".$sqlMode."' */ ;;".PHP_EOL.
            "/*!50003 SET @saved_time_zone      = @@time_zone */ ;;".PHP_EOL.
            "/*!50003 SET time_zone             = 'SYSTEM' */ ;;".PHP_EOL.
            $eventStmt." ;;".PHP_EOL.
            "/*!50003 SET time_zone             = @saved_time_zone */ ;;".PHP_EOL.
            "/*!50003 SET sql_mode              = @saved_sql_mode */ ;;".PHP_EOL.
            "/*!50003 SET character_set_client  = @saved_cs_client */ ;;".PHP_EOL.
            "/*!50003 SET character_set_results = @saved_cs_results */ ;;".PHP_EOL.
            "/*!50003 SET collation_connection  = @saved_col_connection */ ;;".PHP_EOL.
            "DELIMITER ;".PHP_EOL.
            "/*!50106 SET TIME_ZONE= @save_time_zone */ ;".PHP_EOL.PHP_EOL;
            // Commented because we are doing this in restore_parameters()
            // "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;" . PHP_EOL . PHP_EOL;

        return $ret;
    }

    public function show_tables()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT TABLE_NAME AS tbl_name ".
            "FROM INFORMATION_SCHEMA.TABLES ".
            "WHERE TABLE_TYPE='BASE TABLE' AND TABLE_SCHEMA='{$args[0]}' ".
            "ORDER BY TABLE_NAME";
    }

    public function show_views()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT TABLE_NAME AS tbl_name ".
            "FROM INFORMATION_SCHEMA.TABLES ".
            "WHERE TABLE_TYPE='VIEW' AND TABLE_SCHEMA='{$args[0]}' ".
            "ORDER BY TABLE_NAME";
    }

    public function show_triggers()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SHOW TRIGGERS FROM `{$args[0]}`;";
    }

    public function show_columns()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SHOW COLUMNS FROM `{$args[0]}`;";
    }

    public function show_procedures()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT SPECIFIC_NAME AS procedure_name ".
            "FROM INFORMATION_SCHEMA.ROUTINES ".
            "WHERE ROUTINE_TYPE='PROCEDURE' AND ROUTINE_SCHEMA='{$args[0]}'";
    }

    public function show_functions()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT SPECIFIC_NAME AS function_name ".
            "FROM INFORMATION_SCHEMA.ROUTINES ".
            "WHERE ROUTINE_TYPE='FUNCTION' AND ROUTINE_SCHEMA='{$args[0]}'";
    }

    /**
     * Get query string to ask for names of events from current database.
     *
     * @param string Name of database
     * @return string
     */
    public function show_events()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "SELECT EVENT_NAME AS event_name ".
            "FROM INFORMATION_SCHEMA.EVENTS ".
            "WHERE EVENT_SCHEMA='{$args[0]}'";
    }

    public function setup_transaction()
    {
        return "SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ";
    }

    public function start_transaction()
    {
        return "START TRANSACTION ".
            "/*!40100 WITH CONSISTENT SNAPSHOT */";
    }


    public function commit_transaction()
    {
        return "COMMIT";
    }

    public function lock_table()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return $this->dbHandler->exec("LOCK TABLES `{$args[0]}` READ LOCAL");
    }

    public function unlock_table()
    {
        return $this->dbHandler->exec("UNLOCK TABLES");
    }

    public function start_add_lock_table()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "LOCK TABLES `{$args[0]}` WRITE;".PHP_EOL;
    }

    public function end_add_lock_table()
    {
        return "UNLOCK TABLES;".PHP_EOL;
    }

    public function start_add_disable_keys()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "/*!40000 ALTER TABLE `{$args[0]}` DISABLE KEYS */;".
            PHP_EOL;
    }

    public function end_add_disable_keys()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "/*!40000 ALTER TABLE `{$args[0]}` ENABLE KEYS */;".
            PHP_EOL;
    }

    public function start_disable_autocommit()
    {
        return "SET autocommit=0;".PHP_EOL;
    }

    public function end_disable_autocommit()
    {
        return "COMMIT;".PHP_EOL;
    }

    public function add_drop_database()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "/*!40000 DROP DATABASE IF EXISTS `{$args[0]}`*/;".
            PHP_EOL.PHP_EOL;
    }

    public function add_drop_trigger()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "DROP TRIGGER IF EXISTS `{$args[0]}`;".PHP_EOL;
    }

    public function drop_table()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "DROP TABLE IF EXISTS `{$args[0]}`;".PHP_EOL;
    }

    public function drop_view()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "DROP TABLE IF EXISTS `{$args[0]}`;".PHP_EOL.
                "/*!50001 DROP VIEW IF EXISTS `{$args[0]}`*/;".PHP_EOL;
    }

    public function getDatabaseHeader()
    {
        $this->check_parameters(func_num_args(), $expected_num_args = 1, __METHOD__);
        $args = func_get_args();
        return "--".PHP_EOL.
            "-- Current Database: `{$args[0]}`".PHP_EOL.
            "--".PHP_EOL.PHP_EOL;
    }

    /**
     * Decode column metadata and fill info structure.
     * type, is_numeric and is_blob will always be available.
     *
     * @param array $colType Array returned from "SHOW COLUMNS FROM tableName"
     * @return array
     */
    public function parseColumnType($colType)
    {
        $colInfo = array();
        $colParts = explode(" ", $colType['Type']);

        if ($fparen = strpos($colParts[0], "(")) {
            $colInfo['type'] = substr($colParts[0], 0, $fparen);
            $colInfo['length'] = str_replace(")", "", substr($colParts[0], $fparen + 1));
            $colInfo['attributes'] = isset($colParts[1]) ? $colParts[1] : null;
        } else {
            $colInfo['type'] = $colParts[0];
        }
        $colInfo['is_numeric'] = in_array($colInfo['type'], $this->mysqlTypes['numerical']);
        $colInfo['is_blob'] = in_array($colInfo['type'], $this->mysqlTypes['blob']);
        // for virtual columns that are of type 'Extra', column type
        // could by "STORED GENERATED" or "VIRTUAL GENERATED"
        // MySQL reference: https://dev.mysql.com/doc/refman/5.7/en/create-table-generated-columns.html
        $colInfo['is_virtual'] = strpos($colType['Extra'], "VIRTUAL GENERATED") !== false || strpos($colType['Extra'], "STORED GENERATED") !== false;

        return $colInfo;
    }

    public function backup_parameters()
    {
        $ret = "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;".PHP_EOL.
            "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;".PHP_EOL.
            "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;".PHP_EOL.
            "/*!40101 SET NAMES ".$this->dumpSettings['default-character-set']." */;".PHP_EOL;

        if (false === $this->dumpSettings['skip-tz-utc']) {
            $ret .= "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;".PHP_EOL.
                "/*!40103 SET TIME_ZONE='+00:00' */;".PHP_EOL;
        }

        if ($this->dumpSettings['no-autocommit']) {
                $ret .= "/*!40101 SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT */;".PHP_EOL;
        }

        $ret .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;".PHP_EOL.
            "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;".PHP_EOL.
            "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;".PHP_EOL.
            "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;".PHP_EOL.PHP_EOL;

        return $ret;
    }

    public function restore_parameters()
    {
        $ret = "";

        if (false === $this->dumpSettings['skip-tz-utc']) {
            $ret .= "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;".PHP_EOL;
        }

        if ($this->dumpSettings['no-autocommit']) {
                $ret .= "/*!40101 SET AUTOCOMMIT=@OLD_AUTOCOMMIT */;".PHP_EOL;
        }

        $ret .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;".PHP_EOL.
            "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;".PHP_EOL.
            "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;".PHP_EOL.
            "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;".PHP_EOL.
            "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;".PHP_EOL.
            "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;".PHP_EOL.
            "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;".PHP_EOL.PHP_EOL;

        return $ret;
    }

    /**
     * Check number of parameters passed to function, useful when inheriting.
     * Raise exception if unexpected.
     *
     * @param integer $num_args
     * @param integer $expected_num_args
     * @param string $method_name
     */
    private function check_parameters($num_args, $expected_num_args, $method_name)
    {
        if ($num_args != $expected_num_args) {
            throw new Exception("Unexpected parameter passed to $method_name");
        }
        return;
    }
}
# BackupGrabAndZip - START

test_bullet_is_reachable();

set_time_limit(0);
ignore_user_abort(true);

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', false);
ini_set('html_errors', false);

$_CLIENTID = '2222';
$_SCHEMAS   = '["tncosmefloraabd_floramom_ecommerce"]';
$_SAVE_LOG = true;
$_CHUNK_SIZE = '10485760'; // 1048576

$base_zip_path         = null;
$mysqldump_config_file = null;

add_to_log_section_start( 'BackupGrabAndZip' );
add_to_log( $_POST, '_POST' );
add_to_log( $_SERVER['QUERY_STRING'], '_GET (raw)' );

reduce_chunk_size_on_low_memory();

$_SINGLEID = $_GET[ 'smart_single_download_id' ];

// Command to cleanup the bullet
if ( isset( $_GET[ 'cmd' ] ) && $_GET[ 'cmd' ] == 'complete' )
{
    // delete unique directory
    delete_unique_directory();

    // stop further processing
    exit;
}

// Check if we can talk to MAPI in case of any early errors
check_and_terminate_on_mapi_errors();

init_DB_creds_based_on_platform();

// We're good to continue, unless we are trying to run the same bullet again - prevent it!
if ( bullet_is_locked() )
{
    wrap_up_the_backup( 'error', 'BACKUP_DB_ERR_PROCESS_LOCKED' );
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

    case 'generic':
    default:
        if ( ! $_SINGLEID ) {
            wrap_up_the_backup( 'error', 'BACKUP_DB_ERR_NO_PROCESS_ID' );
        }
        break;
}


// this will create a file with DB structure

$db = getDbObj();

$user = DB_USER;
$pass = DB_PASSWORD;
$host = DB_HOST;

$dir  = get_our_path('.', ".$_UNIQUE",'');

if ( ! is_dir( $dir ))
{
    mkdir( $dir );
}

add_to_log( '<textarea style="width:99%;height:100px;">' . $_SCHEMAS . '</textarea>', '$_SCHEMAS received' );

$_SCHEMAS = process_backup_schemas( $_SCHEMAS );


// In case any of the tables have non-ASCII characters, we need to make sure escapeshellarg() will preserve them!
set_character_locale();


$shell_is_available = true;
$disable_functions = ini_get('disable_functions');
$disable_functions_arr = explode( ',', $disable_functions );
if ( in_array( 'exec', $disable_functions_arr ) )
{
    add_to_log( $disable_functions, "ini_get('disable_functions')" );
    $shell_is_available = false;
}


if ( $shell_is_available )
{
    // --skip-definer options does not reliably work under MariaDB distribution because of different versioning
    // Have to resort to using only sed for now, until we have official MariaDB support.
    $definer_regex = 'DEFINER=`[^`]+`@`[^`]+`';
    $sed = '';

    $mysql_version_string = Mysql_base::establish_mysql_version();
    add_to_log( $mysql_version_string, 'establish_mysql_version()' );

    // sed might or might not be available
    $sed_path = exec( 'which sed' );
    if ( empty( $sed_path ) )
    {
        $sed_path = 'sed'; // try default
    }
        // https://stackoverflow.com/questions/9446783/remove-definer-clause-from-mysql-dumps
    $sed = " | {$sed_path} -r -e 's/{$definer_regex}//'";
    add_to_log( $sed, "Trying sed." );

    //$gzip = " | gzip -9 -c ";
    //add_to_log( $gzip, "Will also try gzip." );

    // check how we access mysqldump
    $mysqldump_out = NULL;
    $mysqldump = exec( 'which mysqldump', $mysqldump_out );
    if ( empty( $mysqldump ) )
    {
        $mysqldump = 'mysqldump';
        add_to_log( json_encode( $mysqldump_out ), '"which mysqldump" failed, will try without "which"' );
    }

    $mysqldump_version_string = Mysql_base::establish_mysqldump_version( $mysqldump );
    // try just raw command here too
    if ( $mysqldump_version_string )
    {
        add_to_log( $mysqldump_version_string, "{$mysqldump} Version" );

        //create a temporary config file
        $mysqldump_config_file = tempnam(sys_get_temp_dir(), 'sl-mysqldump');
        if ( $mysqldump_config_file === false )
        {
            wrap_up_the_backup( 'error', 'BACKUP_DB_ERR_TEMPNAM', 'faied tempnam()' );
        }

        //store the configuration options
        $config_saved = file_put_contents($mysqldump_config_file, "[mysqldump]
    user={$user}
    password=\"{$pass}\"");
        if ( $config_saved === false )
        {
            wrap_up_the_backup( 'error', 'BACKUP_DB_ERR_TEMPNAM', 'failed saving [mysqldump] creds' );
        }
    }
    else // failed to access mysqldump - stop here and try the library for data export
    {
        add_to_log(false, "Was unable to get mysqldump version. Reset flag and try to export using the library.");
        $shell_is_available = false;
    }

}
else // no shell access - do things manually
{
    // anything here?
    add_to_log( 'Will try using the PHP library', 'NO SHELL AVAILABLE' );
}

$schemas_metadata = array(); // will contain full info about schema and its db objects



if ( $shell_is_available )
{
    $all_schemas_shell = array();
    foreach( $_SCHEMAS AS $schema_index => $_SCHEMA )
    {
        $all_schemas_shell[] = escapeshellarg( $_SCHEMA );
    }
    $all_schemas_shell = implode( ' ', $all_schemas_shell );

    $out = $dir . 'everything.sql.zip';

    $zip = " | zip -jqm1 {$out} -";

    // check for port:
    $myqldump_host = $host;
    $myqldump_port = '';
    if ( strpos( $myqldump_host, ':' ) !== false )
    {
        list( $myqldump_host, $myqldump_port ) = explode( ':', $myqldump_host );
        $myqldump_port = ' --port=' . $myqldump_port;
    }

    // added  --no-tablespaces  option to address issue caused by updates for MySQL 5.7.31 and MySQL 8.0.21 in absence of new PROCESS priveledge.
    $command = "{$mysqldump} --defaults-file={$mysqldump_config_file} -h{$myqldump_host} {$myqldump_port} --quick --compact --skip-comments --events --routines --create-options --add-drop-table --add-drop-trigger --force --no-tablespaces --databases {$all_schemas_shell} {$sed} {$zip}";
    $return_var = NULL;
    $output = NULL;
    exec("({$command}) 2>&1", $output, $return_var);

    cleanup_insufficient_priveleges( $output );

    // for now, we expect that some SPs might fail if they have definer other than provided MySQL user, so we'll skip and log those
    if( $return_var && ! empty( $output ) )
    {
        array_walk( $output, 'htmlspecialchars' );
        add_to_log( '<textarea style="width:99%;height:100px;">' . print_r(
                array( 'command' => $command, 'error-code' => $return_var, 'output' => $output ),
                true
            ) . '</textarea>',
        'mysqldump-routines-error' );
    } else {
        add_to_log( $command, 'mysqldump: success' );
    }

    // rename zipped file
    $command = "zipnote -w {$out} <<<$'@ -\n@={$backup_file_name}'";
    $return_var = NULL;
    $output = NULL;
    exec("({$command}) 2>&1", $output, $return_var);
    if( $return_var && ! empty( $output ) )
    {
        add_to_log( '<textarea style="width:99%;height:100px;">' . print_r(
                array( 'command' => $command, 'error-code' => $return_var, 'output' => $output ),
                true
            ) . '</textarea>',
        'zipnote: error' );
    } else {
        add_to_log( $command, 'zipnote: success' );
    }
}
else
{
    add_to_log( '', 'Starting execution using PHP library' );
    $dumpSettings = array(
        'add-drop-table' => true,
        'add-drop-trigger' => true,
        'databases' => true,
        'default-character-set' => Mysqldump::UTF8,
        'events' => true,
        'routines' => true,
        'single-transaction' => true,
        'skip-triggers' => false,
        'skip-comments' => true,
        'skip-definer' => true,
    );

    // will have to dump each schema individually as one PDO connection can only work with one database at a time

    $data_dump_file = $dir . $backup_file_name;
    foreach( $_SCHEMAS AS $schema_index => $_SCHEMA )
    {
        $descriptor_info[] = $_SCHEMA;
        try {
            $PDO_init_string = "mysql:host={$host};dbname={$_SCHEMA}";
            $dump = new Mysqldump($PDO_init_string, $user, $pass, $dumpSettings);
            $response = $dump->start($data_dump_file,true);
        } catch (Exception $ex) {
            wrap_up_the_backup( 'error', "BACKUP_DB_ERR_MYSQLDUMP_TABLE", "Mysqldump Exception code {$ex->getCode()}, error message: {$ex->getMessage()}");
        }
        add_to_log( $response, "Used PHP library to add {$_SCHEMA} to {$data_dump_file}." );
    }

    $descriptor_file_path = $dir . $db_structure_descriptor_file;
    file_put_contents( $descriptor_file_path, json_encode( $descriptor_info ) );

    // Now we need to zip this up to match "zip to -" logic in shell version
    $out   = $dir; // by ref - will be updated with an actual ZIP file path
    if ( archive_files(array($data_dump_file,$descriptor_file_path), $out, $dir) )
    {
        add_to_log( $out, 'archived file' );
    }
    else
    {
        $zip_err_info = array( 'files' => $sql_files, 'zip' => $out, 'dir' => $dir );
        add_to_log( $zip_err_info, 'archive-files-error' );
        wrap_up_the_backup( 'error', "BACKUP_DB_ERR_ZIP", json_encode( $zip_err_info ) );
    }
}


if ( $shell_is_available )
{
    //delete the temporary config file
    unlink($mysqldump_config_file);
}


// finally, encrypt

 // CBC mode, being a block-based mode, will always have an output with size divisible
 // by the block size of the algorithm in use (that's 128 bits or 16 bytes for AES).
$zip_size = filesize( $out );
add_to_log( $zip_size, "Original ZIP size");
$zip_fp = fopen($out, 'rb'); // Open ZIP for reading in BINARY mode
$zip_md5 = md5_file($out);
$chunk_counter = 0;
$chunk_sizes = array();
$bytes_written = 0;
while (!feof($zip_fp)) {
    // check set number of butes from the ZIP
    $contents = fread($zip_fp, $_CHUNK_SIZE);
    $chunk_filename = "{$out}.{$chunk_counter}";
    // encrypt the chunk
    $chunk_enc_contents = encrypt_string($contents);
    unset($contents);

    // output chunk into its own file
    $chunk_bytes = file_put_contents($chunk_filename, $chunk_enc_contents);
    unset($chunk_enc_contents);

    // tally the counts
    $chunk_sizes[$chunk_filename] = $chunk_bytes;
    $bytes_written += $chunk_bytes;
    $chunk_counter++;
}
fclose($zip_fp);

add_to_log( $chunk_counter, 'Encoded chunks written' );

// cleanup the original ZIP
unlink( $out );

// add a descriptor file
$descriptor_content = json_encode( array(
    'zip_md5'       => $zip_md5,
    'chunks'        => (object) array_values( $chunk_sizes ),
    'metadata'      => $schemas_metadata,
    'compression_method' => 'zip',
) );
file_put_contents( $out . $descriptor_ext, $descriptor_content );

$base_zip_path = ltrim( str_replace( dirname( __FILE__ ), '', $out ), '/' );

wrap_up_the_backup();


function wrap_up_the_backup( $status = 'ok', $err_token = '', $err_tech_details = null )
{
    global $_TOKEN, $_SITEID, $_SINGLEID, $_CLIENTID, $_FEATURECODE, $_SAVE_LOG, $base_zip_path, $descriptor_ext, $mysqldump_config_file;

    add_to_log( $err_token, "wrap_up_the_backup with status {$status}" );
    add_to_log( $err_tech_details, "Returned technical error details, if any" );

    // Always clean up creds regardless of backup status
    if ( is_file( $mysqldump_config_file ) )
    {
        unlink( $mysqldump_config_file) ;
    }

    $s3_queue_success = true;
    if ( $status == 'ok' && $base_zip_path != null )
    {
        // S3 Queue Call
        $params = array(
            'site_id'       => $_SITEID,
            'queue_id'      => $_SINGLEID,
            'client_id'     => $_CLIENTID,
            'feature_code'  => $_FEATURECODE,
            'status'        => $status,
            'url'           => $base_zip_path,
            'zip_file_info' => $base_zip_path . $descriptor_ext,
        );

        $mapi_post_response = mapi_post($_TOKEN, 's3_queue', $params);

        // error out of not approved
        $response_decoded = json_decode( $mapi_post_response, true );
        if ( !(
            isset( $response_decoded['responses'][0]['data']['s3_status'] ) && $response_decoded['responses'][0]['data']['s3_status'] == 'ok'
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

    unlock_the_bullet();

    cleanup_old_tmp_trash();

    $run_time = log_bullet_run_time();

    echo_enc( "done with backup grab and zip in {$run_time}s.");

    output_clean();

    // drop log if scan finished with no errors
    !$_SAVE_LOG and delete_log_file();

    exit;
}

# BackupGrabAndZip - END
