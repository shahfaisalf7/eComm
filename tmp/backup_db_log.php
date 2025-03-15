<div style='background-color:#AFF'><p>Encryption</p><p><strong>PHP Version</strong></p><pre>8.2.27</pre><hr /><p><strong>Cryptor</strong></p><pre>OpenSSL</pre><hr /><p><strong>Default Cipher</strong></p><pre>aes-256-cbc</pre><hr /><p><strong>mb_internal_encoding</strong></p><pre>UTF-8</pre><hr /><div style='background-color:#AAA'><p>IP Validation</p><p><strong>$headers from get_ip()</strong></p><pre>Array
(
    [Connection] => TE
    [Host] => floramom.com
    [User-Agent] => SiteLock (Module: SmartDB; Source: https://www.sitelock.com/; Version: 1.0)
    [x-forwarded-proto] => https
    [x-https] => on
    [X-Forwarded-For] => 184.154.139.56
    [Content-Length] => 0
)
</pre><hr /><p><strong>IP Check started in</strong></p><pre>/home/tncosmefloraabd/public_html/tmp/fb300a6a3cc0a7c671890b995a6bb93d.php</pre><hr /><p><strong>IP Check started at</strong></p><pre>2025-02-13T18:43:09-05:00</pre><hr /><p><strong>The following IPs will be tested</strong></p><pre>Array
(
    [0] => 184.154.139.56
)
</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => 2ea***
    [requests] => Array
        (
            [id] => 37b24850f63d8a2bd486ad8fb6e78430-17394901890265
            [action] => validate_ip
            [params] => Array
                (
                    [site_id] => 43922276
                    [ip] => 184.154.139.56
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 766
    [request_size] => 462
    [filetime] => -1
    [ssl_verify_result] => 20
    [redirect_count] => 0
    [total_time] => 0.338056
    [namelookup_time] => 0.000228
    [connect_time] => 0.040466
    [pretransfer_time] => 0.083546
    [size_upload] => 324
    [size_download] => 530
    [speed_download] => 1567
    [speed_upload] => 958
    [download_content_length] => -1
    [upload_content_length] => 324
    [starttransfer_time] => 0.337967
    [redirect_time] => 0
    [redirect_url] =>
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 198.54.114.95
    [local_port] => 43984
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => https
    [appconnect_time_us] => 83458
    [connect_time_us] => 40466
    [namelookup_time_us] => 228
    [pretransfer_time_us] => 83546
    [redirect_time_us] => 0
    [starttransfer_time_us] => 337967
    [total_time_us] => 338056
    [effective_method] => POST
)
</pre><hr /><p><strong>mapi_response</strong></p><pre><textarea style="width:99%;height:100px;">{"apiVersion":"3.0.1","status":"ok","globalResponse":null,"banner":null,"forceLogout":false,"newToken":null,"now":1739490189,"responses":[{"id":"37b24850f63d8a2bd486ad8fb6e78430-17394901890265","data":{"ip_address":"184.154.139.56","valid":true},"raw_api_url":"https:\/\/api.sitelock.com\/v1\/2ea***\/dbscan\/checkip","raw_response":{"@attributes":{"version":"1.1","encoding":"UTF-8"},"checkIP":{"status":"1"}},"raw_request":{"site_id":"43922276","ip":"184.154.139.56"},"user_agent":null,"status":"ok"}]}</textarea></pre><hr /><pre>Ifsnop\Mysqldump is loaded into memory.</pre><hr /><p><strong>Detected memory_limit</strong></p><pre>1024M</pre><hr /><p><strong>Chunk Size</strong></p><pre>10485760</pre><hr /><div style='background-color:#AAF'><p>CheckFeatures</p><p><strong>Feature Code</strong></p><pre>backup_db</pre><hr /><p><strong>Platform</strong></p><pre>other</pre><hr /><p><strong>_POST</strong></p><pre>Array
(
)
</pre><hr /><p><strong>_GET (raw)</strong></p><pre>cmd=db_creds_ready&enc_db_creds=Hc1w%2Fg6jReM7ULX7XQBarK3FRTsA1dRzhQDiRjziSu2og6noGYtVJfzMCFrTW6KX9TKbiv4PidA8IKoY6Pwe3NyJiAVBDHZQbjGjkYX1kV0Ww6%2BxzcFN0juBMO6t5eRZJDEnT4JmUSh6Ra2k0XpoXTC%2BMtpFVlkkdUYDEBxhpYQ%3D&smart_single_download_id=5268729</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => 2ea***
    [requests] => Array
        (
            [id] => 73acaf0be42a256ae41f2b1cb5b00f39-17394901893653
            [action] => s3_get_enc_info
            [params] => Array
                (
                    [site_id] => 43922276
                    [queue_id] => 5268729
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 767
    [request_size] => 466
    [filetime] => -1
    [ssl_verify_result] => 20
    [redirect_count] => 0
    [total_time] => 0.340215
    [namelookup_time] => 0.000172
    [connect_time] => 0.040404
    [pretransfer_time] => 0.083778
    [size_upload] => 328
    [size_download] => 835
    [speed_download] => 2454
    [speed_upload] => 964
    [download_content_length] => -1
    [upload_content_length] => 328
    [starttransfer_time] => 0.34017
    [redirect_time] => 0
    [redirect_url] =>
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 198.54.114.95
    [local_port] => 43998
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => https
    [appconnect_time_us] => 83677
    [connect_time_us] => 40404
    [namelookup_time_us] => 172
    [pretransfer_time_us] => 83778
    [redirect_time_us] => 0
    [starttransfer_time_us] => 340170
    [total_time_us] => 340215
    [effective_method] => POST
)
</pre><hr /><p><strong>Received encryption details</strong></p><pre>Array
(
    [cipher] => aes-256-cbc
    [key] => Q4E***
    [iv] => KIZ***
)
</pre><hr /><p><strong>Check Features - FS</strong></p><pre>true</pre><hr /><p><strong>Check Features - CRYPTO</strong></p><pre>true</pre><hr /><p><strong>Starting MySQLi constructor</strong></p><pre></pre><hr /><p><strong>Check Features - DB</strong></p><pre>true</pre><hr /><p><strong>Check Features - ZIP</strong></p><pre>2</pre><hr /><p><strong>Check Features - HTTP (always true at this point if check-ip did not fail)</strong></p><pre>1</pre><hr /><p><strong>Check Features - GZIP</strong></p><pre>1</pre><hr /><p><strong>$backup_schemas</strong></p><pre><textarea style="width:99%;height:100px;">Array
(
    [0] => tncosmefloraabd_floramom_ecommerce
)
</textarea></pre><hr /><p><strong>ini_get('disable_functions')</strong></p><pre></pre><hr /><p><strong>Attempted to setlocale() with UTF-8</strong></p><pre>Success</pre><hr /><p><strong>get_foreign_key_checks</strong></p><pre>1</pre><hr /><p><strong>GRANTS info</strong></p><pre>Array
(
    [tncosmefloraabd_floramom_ecommerce] => Array
        (
            [CREATE] => 1
            [CREATE ROUTINE] => 1
            [CREATE VIEW] => 1
            [DELETE] => 1
            [DROP] => 1
            [EVENT] => 1
            [INSERT] => 1
            [LOCK TABLES] => 1
            [SELECT] => 1
            [TRIGGER] => 1
            [UPDATE] => 1
        )

)
</pre><hr /><p><strong>Testing mysqldump with exec commands</strong></p><pre>Starting...</pre><hr /><p><strong>/usr/bin/mysql Version</strong></p><pre>/usr/bin/mysql  Ver 15.1 Distrib 10.6.20-MariaDB, for Linux (x86_64) using readline 5.1</pre><hr /><p><strong>/usr/bin/mysqldump Version</strong></p><pre>mysqldump  Ver 10.19 Distrib 10.6.20-MariaDB, for Linux (x86_64)</pre><hr /><p><strong>Testing mysqldump with exec commands</strong></p><pre>Success</pre><hr /><p><strong>Check Features - got schema?</strong></p><pre>true</pre><hr /><p><strong>$statuses - new</strong></p><pre>Array
(
    [fs] => 1
    [crypto] => 1
    [zip] => 2
    [gzip] => 1
    [http] => 1
    [db] => 1
    [json] => 1
    [mysqldump] => 1
    [errors] => Array
        (
        )

    [schemas] => 1
    [_schemas_data] => Array
        (
            [0] => tncosmefloraabd_floramom_ecommerce
        )

)
</pre><hr /><p><strong>Bullet run time, seconds.</strong></p><pre>1</pre><hr />
