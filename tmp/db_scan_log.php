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
</pre><hr /><p><strong>IP Check started in</strong></p><pre>/home/tncosmefloraabd/public_html/tmp/8cfc2b03f312eeb91bc33a603f6e20f3.php</pre><hr /><p><strong>IP Check started at</strong></p><pre>2025-02-13T18:42:10-05:00</pre><hr /><p><strong>The following IPs will be tested</strong></p><pre>Array
(
    [0] => 184.154.139.56
)
</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => b45***
    [requests] => Array
        (
            [id] => 1bbf202520346e64ecdce2e0c9b71a12-1739490130231
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
    [total_time] => 0.37698
    [namelookup_time] => 0.000226
    [connect_time] => 0.040306
    [pretransfer_time] => 0.083877
    [size_upload] => 324
    [size_download] => 529
    [speed_download] => 1403
    [speed_upload] => 859
    [download_content_length] => -1
    [upload_content_length] => 324
    [starttransfer_time] => 0.372097
    [redirect_time] => 0
    [redirect_url] =>
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 198.54.114.95
    [local_port] => 37350
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => https
    [appconnect_time_us] => 83790
    [connect_time_us] => 40306
    [namelookup_time_us] => 226
    [pretransfer_time_us] => 83877
    [redirect_time_us] => 0
    [starttransfer_time_us] => 372097
    [total_time_us] => 376980
    [effective_method] => POST
)
</pre><hr /><p><strong>mapi_response</strong></p><pre><textarea style="width:99%;height:100px;">{"apiVersion":"3.0.1","status":"ok","globalResponse":null,"banner":null,"forceLogout":false,"newToken":null,"now":1739490130,"responses":[{"id":"1bbf202520346e64ecdce2e0c9b71a12-1739490130231","data":{"ip_address":"184.154.139.56","valid":true},"raw_api_url":"https:\/\/api.sitelock.com\/v1\/b45***\/dbscan\/checkip","raw_response":{"@attributes":{"version":"1.1","encoding":"UTF-8"},"checkIP":{"status":"1"}},"raw_request":{"site_id":"43922276","ip":"184.154.139.56"},"user_agent":null,"status":"ok"}]}</textarea></pre><hr /><pre>Ifsnop\Mysqldump is loaded into memory.</pre><hr /><p><strong>Detected memory_limit</strong></p><pre>1024M</pre><hr /><p><strong>Chunk Size</strong></p><pre>10485760</pre><hr /><div style='background-color:#AAF'><p>CheckFeatures</p><p><strong>Feature Code</strong></p><pre>db_scan</pre><hr /><p><strong>Platform</strong></p><pre>other</pre><hr /><p><strong>_POST</strong></p><pre>Array
(
)
</pre><hr /><p><strong>_GET (raw)</strong></p><pre>cmd=db_creds_ready&enc_db_creds=FrgVOVaTnJeaM3Nf7K1zD63i84%2B2cAgoXSHZgZ5eIeIypeDHf3eBMBol1bpN0fN0whRc5M0bBawQ8peF7G5r9T%2FgiXTZsJM6hBDSX6zztMCG5gC7vX2y3XRNJe04fOymmFCAA%2FfTsFL59uPn3mL6pHWGW0l5Ft2ALbB2lbsylw5uY7fel%2BkbbHM2FmDDyuKBF8jMaLpTSmiuXXCCTsFeOxpg7taPmx8nx1yhO213l%2BE%3D&smart_single_download_id=5268728</pre><hr /><p><strong>mapi_post URL</strong></p><pre>https://mapi.sitelock.com/v3/connect/</pre><hr /><p><strong>mapi_post_request</strong></p><pre>Array
(
    [pluginVersion] => 100.0.0
    [apiTargetVersion] => 3.0.0
    [token] => b45***
    [requests] => Array
        (
            [id] => 80e469f72f8a93020956ec817f82cf85-17394901306085
            [action] => s3_get_enc_info
            [params] => Array
                (
                    [site_id] => 43922276
                    [queue_id] => 5268728
                )

        )

)
</pre><hr /><p><strong>curl_getinfo()</strong></p><pre>Array
(
    [url] => https://mapi.sitelock.com/v3/connect/
    [content_type] => text/html; charset=UTF-8
    [http_code] => 200
    [header_size] => 763
    [request_size] => 466
    [filetime] => -1
    [ssl_verify_result] => 20
    [redirect_count] => 0
    [total_time] => 0.345655
    [namelookup_time] => 0.000146
    [connect_time] => 0.03904
    [pretransfer_time] => 0.080401
    [size_upload] => 328
    [size_download] => 843
    [speed_download] => 2438
    [speed_upload] => 948
    [download_content_length] => -1
    [upload_content_length] => 328
    [starttransfer_time] => 0.345472
    [redirect_time] => 0
    [redirect_url] =>
    [primary_ip] => 45.60.12.54
    [certinfo] => Array
        (
        )

    [primary_port] => 443
    [local_ip] => 198.54.114.95
    [local_port] => 37354
    [http_version] => 2
    [protocol] => 2
    [ssl_verifyresult] => 0
    [scheme] => https
    [appconnect_time_us] => 80324
    [connect_time_us] => 39040
    [namelookup_time_us] => 146
    [pretransfer_time_us] => 80401
    [redirect_time_us] => 0
    [starttransfer_time_us] => 345472
    [total_time_us] => 345655
    [effective_method] => POST
)
</pre><hr /><p><strong>Received encryption details</strong></p><pre>Array
(
    [cipher] => aes-256-cbc
    [key] => n/P***
    [iv] => /tp***
)
</pre><hr /><p><strong>Check Features - FS</strong></p><pre>true</pre><hr /><p><strong>Check Features - CRYPTO</strong></p><pre>true</pre><hr /><p><strong>Starting MySQLi constructor</strong></p><pre></pre><hr /><p><strong>Check Features - DB</strong></p><pre>true</pre><hr /><p><strong>Check Features - ZIP</strong></p><pre>2</pre><hr /><p><strong>Check Features - HTTP (always true at this point if check-ip did not fail)</strong></p><pre>1</pre><hr /><p><strong>Check Features - GZIP</strong></p><pre>1</pre><hr /><p><strong>retrieved info about 108 "other" tables</strong></p><pre>Array
(
    [0] => activations
    [1] => addresses
    [2] => attribute_categories
    [3] => attribute_set_translations
    [4] => attribute_sets
    [5] => attribute_translations
    [6] => attribute_value_translations
    [7] => attribute_values
    [8] => attributes
    [9] => blog_categories
    [10] => blog_category_translations
    [11] => blog_post_blog_tag
    [12] => blog_post_translations
    [13] => blog_posts
    [14] => blog_tag_translations
    [15] => blog_tags
    [16] => box_weight_charges
    [17] => brand_translations
    [18] => brands
    [19] => carts
    [20] => categories
    [21] => category_translations
    [22] => cities
    [23] => coupon_categories
    [24] => coupon_products
    [25] => coupon_translations
    [26] => coupons
    [27] => cross_sell_products
    [28] => currency_rates
    [29] => default_addresses
    [30] => delivery_charges
    [31] => divisions
    [32] => entity_files
    [33] => files
    [34] => flash_sale_product_orders
    [35] => flash_sale_products
    [36] => flash_sale_translations
    [37] => flash_sales
    [38] => menu_item_translations
    [39] => menu_items
    [40] => menu_translations
    [41] => menus
    [42] => meta_data
    [43] => meta_data_translations
    [44] => migrations
    [45] => oauth_access_tokens
    [46] => oauth_auth_codes
    [47] => oauth_clients
    [48] => oauth_personal_access_clients
    [49] => oauth_refresh_tokens
    [50] => option_translations
    [51] => option_value_translations
    [52] => option_values
    [53] => options
    [54] => order_downloads
    [55] => order_product_option_values
    [56] => order_product_options
    [57] => order_product_variation_values
    [58] => order_product_variations
    [59] => order_products
    [60] => order_taxes
    [61] => orders
    [62] => page_translations
    [63] => pages
    [64] => persistences
    [65] => product_attribute_values
    [66] => product_attributes
    [67] => product_categories
    [68] => product_options
    [69] => product_tags
    [70] => product_translations
    [71] => product_variants
    [72] => product_variations
    [73] => product_weight_charges
    [74] => products
    [75] => related_products
    [76] => reminders
    [77] => reviews
    [78] => role_translations
    [79] => roles
    [80] => search_terms
    [81] => setting_translations
    [82] => settings
    [83] => slider_slide_translations
    [84] => slider_slides
    [85] => slider_translations
    [86] => sliders
    [87] => tag_translations
    [88] => tags
    [89] => tax_class_translations
    [90] => tax_classes
    [91] => tax_rate_translations
    [92] => tax_rates
    [93] => throttle
    [94] => transactions
    [95] => translation_translations
    [96] => translations
    [97] => up_sell_products
    [98] => updater_scripts
    [99] => user_activation_codes
    [100] => user_roles
    [101] => users
    [102] => variation_translations
    [103] => variation_value_translations
    [104] => variation_values
    [105] => variations
    [106] => wish_lists
    [107] => zones
)
</pre><hr /><p><strong>Check Features - got schema?</strong></p><pre>true</pre><hr /><p><strong>$statuses - new</strong></p><pre>Array
(
    [fs] => 1
    [crypto] => 1
    [zip] => 2
    [gzip] => 1
    [http] => 1
    [db] => 1
    [json] => 1
    [singlesite] => 1
    [errors] => Array
        (
        )

    [schemas] => 1
    [_schemas_data] => Array
        (
            [prefix] =>
            [tables] => Array
                (
                    [activations] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 11
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [user_id] => int(10) unsigned
                                    [code] => varchar(191)
                                    [completed] => tinyint(1)
                                    [completed_at] => datetime
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [addresses] => Array
                        (
                            [rows] => 4
                            [idcol] => id
                            [last_id] => 11
                            [avg_row_len] => 8192
                            [all_data_len] => 32768
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [customer_id] => int(10) unsigned
                                    [name] => varchar(191)
                                    [first_name] => varchar(191)
                                    [last_name] => varchar(191)
                                    [address_1] => varchar(191)
                                    [address_2] => varchar(191)
                                    [city] => varchar(191)
                                    [state] => varchar(191)
                                    [zip] => varchar(191)
                                    [country] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                    [zone_id] => int(10) unsigned
                                    [zone] => varchar(191)
                                )

                        )

                    [attribute_categories] => Array
                        (
                            [rows] => 0
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [attribute_id] => int(10) unsigned
                                    [category_id] => int(10) unsigned
                                )

                        )

                    [attribute_set_translations] => Array
                        (
                            [rows] => 4
                            [idcol] => id
                            [last_id] => 4
                            [avg_row_len] => 4096
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [attribute_set_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [attribute_sets] => Array
                        (
                            [rows] => 4
                            [idcol] => id
                            [last_id] => 4
                            [avg_row_len] => 4096
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [attribute_translations] => Array
                        (
                            [rows] => 32
                            [idcol] => id
                            [last_id] => 33
                            [avg_row_len] => 512
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [attribute_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [attribute_value_translations] => Array
                        (
                            [rows] => 150
                            [idcol] => id
                            [last_id] => 163
                            [avg_row_len] => 109
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [attribute_value_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [value] => varchar(191)
                                )

                        )

                    [attribute_values] => Array
                        (
                            [rows] => 150
                            [idcol] => id
                            [last_id] => 163
                            [avg_row_len] => 109
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [attribute_id] => int(10) unsigned
                                    [position] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [attributes] => Array
                        (
                            [rows] => 32
                            [idcol] => id
                            [last_id] => 33
                            [avg_row_len] => 512
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [attribute_set_id] => int(10) unsigned
                                    [is_filterable] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                    [slug] => varchar(191)
                                )

                        )

                    [blog_categories] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [slug] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [blog_category_translations] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [blog_category_id] => bigint(20) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [blog_post_blog_tag] => Array
                        (
                            [rows] => 0
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [blog_post_id] => bigint(20) unsigned
                                    [blog_tag_id] => bigint(20) unsigned
                                )

                        )

                    [blog_post_translations] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [blog_post_id] => bigint(20) unsigned
                                    [locale] => varchar(191)
                                    [title] => varchar(191)
                                    [description] => longtext
                                )

                        )

                    [blog_posts] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [user_id] => int(10) unsigned
                                    [blog_category_id] => bigint(20) unsigned
                                    [slug] => varchar(191)
                                    [publish_status] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [blog_tag_translations] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [blog_tag_id] => bigint(20) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [blog_tags] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [slug] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [box_weight_charges] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [weight] => decimal(8,2)
                                    [charge] => decimal(10,2)
                                    [status] => tinyint(1)
                                    [created_by] => bigint(20) unsigned
                                    [updated_by] => bigint(20) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [brand_translations] => Array
                        (
                            [rows] => 49
                            [idcol] => id
                            [last_id] => 69
                            [avg_row_len] => 334
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [brand_id] => int(11)
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [brands] => Array
                        (
                            [rows] => 31
                            [idcol] => id
                            [last_id] => 69
                            [avg_row_len] => 528
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [slug] => varchar(191)
                                    [is_active] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [carts] => Array
                        (
                            [rows] => 71
                            [idcol] => id
                            [last_id] => zfntnZI3fIXgKNUL479969rjYgnmBKSsSlK6dDjp_cart_items
                            [avg_row_len] => 36460
                            [all_data_len] => 2588672
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => varchar(191)
                                    [data] => longtext
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [categories] => Array
                        (
                            [rows] => 74
                            [idcol] => id
                            [last_id] => 308
                            [avg_row_len] => 221
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [parent_id] => int(10) unsigned
                                    [slug] => varchar(191)
                                    [position] => int(10) unsigned
                                    [is_searchable] => tinyint(1)
                                    [is_active] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [category_translations] => Array
                        (
                            [rows] => 70
                            [idcol] => id
                            [last_id] => 308
                            [avg_row_len] => 234
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [category_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [cities] => Array
                        (
                            [rows] => 17
                            [idcol] => id
                            [last_id] => 64
                            [avg_row_len] => 963
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [name] => varchar(191)
                                    [division_id] => bigint(20) unsigned
                                    [status] => enum('1','0')
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [coupon_categories] => Array
                        (
                            [rows] => 0
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [coupon_id] => int(10) unsigned
                                    [category_id] => int(10) unsigned
                                    [exclude] => tinyint(1)
                                )

                        )

                    [coupon_products] => Array
                        (
                            [rows] => 0
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [coupon_id] => int(10) unsigned
                                    [product_id] => int(10) unsigned
                                    [exclude] => tinyint(1)
                                )

                        )

                    [coupon_translations] => Array
                        (
                            [rows] => 2
                            [idcol] => id
                            [last_id] => 3
                            [avg_row_len] => 8192
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [coupon_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [coupons] => Array
                        (
                            [rows] => 2
                            [idcol] => id
                            [last_id] => 3
                            [avg_row_len] => 8192
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [code] => varchar(191)
                                    [value] => decimal(18,4) unsigned
                                    [is_percent] => tinyint(1)
                                    [free_shipping] => tinyint(1)
                                    [minimum_spend] => decimal(18,4) unsigned
                                    [maximum_spend] => decimal(18,4) unsigned
                                    [usage_limit_per_coupon] => int(10) unsigned
                                    [usage_limit_per_customer] => int(10) unsigned
                                    [used] => int(11)
                                    [is_active] => tinyint(1)
                                    [start_date] => date
                                    [end_date] => date
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [cross_sell_products] => Array
                        (
                            [rows] => 1203
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 54
                            [all_data_len] => 65536
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_id] => int(10) unsigned
                                    [cross_sell_product_id] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [currency_rates] => Array
                        (
                            [rows] => 6
                            [idcol] => id
                            [last_id] => 6
                            [avg_row_len] => 2730
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [currency] => varchar(191)
                                    [rate] => decimal(12,4)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [default_addresses] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 4
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [customer_id] => int(10) unsigned
                                    [address_id] => int(10) unsigned
                                )

                        )

                    [delivery_charges] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 2
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [city_id] => bigint(20) unsigned
                                    [charge] => decimal(8,2)
                                    [status] => enum('1','0')
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [divisions] => Array
                        (
                            [rows] => 8
                            [idcol] => id
                            [last_id] => 8
                            [avg_row_len] => 2048
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [name] => varchar(191)
                                    [description] => text
                                    [status] => enum('1','0')
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [entity_files] => Array
                        (
                            [rows] => 79
                            [idcol] => id
                            [last_id] => 4499
                            [avg_row_len] => 207
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [file_id] => int(10) unsigned
                                    [entity_type] => varchar(191)
                                    [entity_id] => bigint(20) unsigned
                                    [zone] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [files] => Array
                        (
                            [rows] => 83
                            [idcol] => id
                            [last_id] => 1571
                            [avg_row_len] => 197
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [user_id] => int(10) unsigned
                                    [filename] => varchar(191)
                                    [disk] => varchar(191)
                                    [path] => varchar(191)
                                    [extension] => varchar(191)
                                    [mime] => varchar(191)
                                    [size] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [flash_sale_product_orders] => Array
                        (
                            [rows] => 4
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 4096
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [flash_sale_product_id] => int(10) unsigned
                                    [order_id] => int(10) unsigned
                                    [qty] => int(11)
                                )

                        )

                    [flash_sale_products] => Array
                        (
                            [rows] => 9
                            [idcol] => id
                            [last_id] => 12
                            [avg_row_len] => 1820
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [flash_sale_id] => int(10) unsigned
                                    [product_id] => int(10) unsigned
                                    [end_date] => date
                                    [price] => decimal(18,4) unsigned
                                    [qty] => int(11)
                                    [position] => int(11)
                                )

                        )

                    [flash_sale_translations] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 4
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [flash_sale_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [campaign_name] => varchar(191)
                                )

                        )

                    [flash_sales] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 4
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [menu_item_translations] => Array
                        (
                            [rows] => 31
                            [idcol] => id
                            [last_id] => 51
                            [avg_row_len] => 528
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [menu_item_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [menu_items] => Array
                        (
                            [rows] => 30
                            [idcol] => id
                            [last_id] => 49
                            [avg_row_len] => 546
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [menu_id] => int(10) unsigned
                                    [parent_id] => int(10) unsigned
                                    [category_id] => int(10) unsigned
                                    [page_id] => int(10) unsigned
                                    [type] => varchar(191)
                                    [url] => varchar(191)
                                    [icon] => varchar(191)
                                    [target] => varchar(191)
                                    [position] => int(10) unsigned
                                    [is_root] => tinyint(1)
                                    [is_fluid] => tinyint(1)
                                    [is_active] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [menu_translations] => Array
                        (
                            [rows] => 4
                            [idcol] => id
                            [last_id] => 4
                            [avg_row_len] => 4096
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [menu_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [menus] => Array
                        (
                            [rows] => 4
                            [idcol] => id
                            [last_id] => 4
                            [avg_row_len] => 4096
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [is_active] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [meta_data] => Array
                        (
                            [rows] => 187
                            [idcol] => id
                            [last_id] => 256
                            [avg_row_len] => 87
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [entity_type] => varchar(191)
                                    [entity_id] => bigint(20) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [meta_data_translations] => Array
                        (
                            [rows] => 186
                            [idcol] => id
                            [last_id] => 250
                            [avg_row_len] => 88
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [meta_data_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [meta_title] => varchar(191)
                                    [meta_description] => text
                                )

                        )

                    [migrations] => Array
                        (
                            [rows] => 117
                            [idcol] => id
                            [last_id] => 135
                            [avg_row_len] => 140
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [migration] => varchar(191)
                                    [batch] => int(11)
                                )

                        )

                    [oauth_access_tokens] => Array
                        (
                            [rows] => 18
                            [idcol] => id
                            [last_id] => fe3ac1dc3dfa3b5657aee9ce6ddeaad945d63db8b9ce952541a2d38da07d9ebb55954ede406f5ec9
                            [avg_row_len] => 910
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => varchar(100)
                                    [user_id] => bigint(20) unsigned
                                    [client_id] => bigint(20) unsigned
                                    [name] => varchar(191)
                                    [scopes] => text
                                    [revoked] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                    [expires_at] => datetime
                                )

                        )

                    [oauth_auth_codes] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => varchar(100)
                                    [user_id] => bigint(20) unsigned
                                    [client_id] => bigint(20) unsigned
                                    [scopes] => text
                                    [revoked] => tinyint(1)
                                    [expires_at] => datetime
                                )

                        )

                    [oauth_clients] => Array
                        (
                            [rows] => 6
                            [idcol] => id
                            [last_id] => 6
                            [avg_row_len] => 2730
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [user_id] => bigint(20) unsigned
                                    [name] => varchar(191)
                                    [secret] => varchar(100)
                                    [provider] => varchar(191)
                                    [redirect] => text
                                    [personal_access_client] => tinyint(1)
                                    [password_client] => tinyint(1)
                                    [revoked] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [oauth_personal_access_clients] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 3
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [client_id] => bigint(20) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [oauth_refresh_tokens] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => varchar(100)
                                    [access_token_id] => varchar(100)
                                    [revoked] => tinyint(1)
                                    [expires_at] => datetime
                                )

                        )

                    [option_translations] => Array
                        (
                            [rows] => 132
                            [idcol] => id
                            [last_id] => 132
                            [avg_row_len] => 124
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [option_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [option_value_translations] => Array
                        (
                            [rows] => 448
                            [idcol] => id
                            [last_id] => 448
                            [avg_row_len] => 109
                            [all_data_len] => 49152
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [option_value_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [label] => varchar(191)
                                )

                        )

                    [option_values] => Array
                        (
                            [rows] => 448
                            [idcol] => id
                            [last_id] => 448
                            [avg_row_len] => 109
                            [all_data_len] => 49152
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [option_id] => int(10) unsigned
                                    [price] => decimal(18,4) unsigned
                                    [price_type] => varchar(10)
                                    [position] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [options] => Array
                        (
                            [rows] => 132
                            [idcol] => id
                            [last_id] => 132
                            [avg_row_len] => 124
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [type] => varchar(191)
                                    [is_required] => tinyint(1)
                                    [is_global] => tinyint(1)
                                    [position] => int(10) unsigned
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [order_downloads] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [order_id] => int(10) unsigned
                                    [file_id] => int(10) unsigned
                                )

                        )

                    [order_product_option_values] => Array
                        (
                            [rows] => 3
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [order_product_option_id] => int(10) unsigned
                                    [option_value_id] => int(10) unsigned
                                    [price] => decimal(18,4) unsigned
                                )

                        )

                    [order_product_options] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 5
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [order_product_id] => int(10) unsigned
                                    [option_id] => int(10) unsigned
                                    [value] => text
                                )

                        )

                    [order_product_variation_values] => Array
                        (
                            [rows] => 0
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [order_product_variation_id] => int(10) unsigned
                                    [variation_value_id] => int(10) unsigned
                                )

                        )

                    [order_product_variations] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [order_product_id] => int(10) unsigned
                                    [variation_id] => int(10) unsigned
                                    [type] => varchar(191)
                                    [value] => varchar(191)
                                )

                        )

                    [order_products] => Array
                        (
                            [rows] => 17
                            [idcol] => id
                            [last_id] => 53
                            [avg_row_len] => 963
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [order_id] => int(10) unsigned
                                    [product_id] => int(10) unsigned
                                    [product_variant_id] => bigint(20) unsigned
                                    [unit_price] => decimal(18,4) unsigned
                                    [qty] => int(11)
                                    [line_total] => decimal(18,4) unsigned
                                )

                        )

                    [order_taxes] => Array
                        (
                            [rows] => 0
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [order_id] => int(10) unsigned
                                    [tax_rate_id] => int(10) unsigned
                                    [amount] => decimal(15,4) unsigned
                                )

                        )

                    [orders] => Array
                        (
                            [rows] => 19
                            [idcol] => id
                            [last_id] => 39
                            [avg_row_len] => 862
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [customer_id] => int(11)
                                    [customer_email] => varchar(191)
                                    [customer_phone] => varchar(191)
                                    [customer_full_name] => varchar(191)
                                    [billing_full_name] => varchar(191)
                                    [shipping_full_name] => varchar(191)
                                    [customer_first_name] => varchar(191)
                                    [customer_last_name] => varchar(191)
                                    [billing_first_name] => varchar(191)
                                    [billing_last_name] => varchar(191)
                                    [billing_address_1] => varchar(191)
                                    [billing_address_2] => varchar(191)
                                    [billing_city] => varchar(191)
                                    [billing_state] => varchar(191)
                                    [billing_zip] => varchar(191)
                                    [billing_zone] => varchar(191)
                                    [billing_country] => varchar(191)
                                    [shipping_first_name] => varchar(191)
                                    [shipping_last_name] => varchar(191)
                                    [shipping_address_1] => varchar(191)
                                    [shipping_address_2] => varchar(191)
                                    [shipping_city] => varchar(191)
                                    [shipping_state] => varchar(191)
                                    [shipping_zip] => varchar(191)
                                    [shipping_zone] => varchar(191)
                                    [shipping_country] => varchar(191)
                                    [sub_total] => decimal(18,4) unsigned
                                    [shipping_method] => varchar(191)
                                    [shipping_cost] => decimal(18,4) unsigned
                                    [coupon_id] => int(11)
                                    [discount] => decimal(18,4) unsigned
                                    [total] => decimal(18,4) unsigned
                                    [payment_method] => varchar(191)
                                    [currency] => varchar(191)
                                    [currency_rate] => decimal(18,4)
                                    [locale] => varchar(191)
                                    [status] => varchar(191)
                                    [note] => text
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [page_translations] => Array
                        (
                            [rows] => 9
                            [idcol] => id
                            [last_id] => 12
                            [avg_row_len] => 5461
                            [all_data_len] => 49152
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [page_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                    [body] => longtext
                                )

                        )

                    [pages] => Array
                        (
                            [rows] => 9
                            [idcol] => id
                            [last_id] => 12
                            [avg_row_len] => 1820
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [slug] => varchar(191)
                                    [is_active] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [persistences] => Array
                        (
                            [rows] => 169
                            [idcol] => id
                            [last_id] => 328
                            [avg_row_len] => 96
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [user_id] => int(10) unsigned
                                    [code] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [product_attribute_values] => Array
                        (
                            [rows] => 1194
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 54
                            [all_data_len] => 65536
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_attribute_id] => int(10) unsigned
                                    [attribute_value_id] => int(10) unsigned
                                )

                        )

                    [product_attributes] => Array
                        (
                            [rows] => 781
                            [idcol] => id
                            [last_id] => 2484
                            [avg_row_len] => 83
                            [all_data_len] => 65536
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [product_id] => int(10) unsigned
                                    [attribute_id] => int(10) unsigned
                                )

                        )

                    [product_categories] => Array
                        (
                            [rows] => 82
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 199
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_id] => int(10) unsigned
                                    [category_id] => int(10) unsigned
                                )

                        )

                    [product_options] => Array
                        (
                            [rows] => 118
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 138
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_id] => int(10) unsigned
                                    [option_id] => int(10) unsigned
                                )

                        )

                    [product_tags] => Array
                        (
                            [rows] => 121
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 135
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_id] => int(10) unsigned
                                    [tag_id] => int(10) unsigned
                                )

                        )

                    [product_translations] => Array
                        (
                            [rows] => 92
                            [idcol] => id
                            [last_id] => 175
                            [avg_row_len] => 17274
                            [all_data_len] => 1589248
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [product_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                    [description] => longtext
                                    [short_description] => text
                                )

                        )

                    [product_variants] => Array
                        (
                            [rows] => 9
                            [idcol] => id
                            [last_id] => 9
                            [avg_row_len] => 1820
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [uid] => varchar(191)
                                    [uids] => text
                                    [product_id] => int(10) unsigned
                                    [name] => varchar(191)
                                    [price] => decimal(18,4) unsigned
                                    [special_price] => decimal(18,4) unsigned
                                    [special_price_type] => varchar(191)
                                    [special_price_start] => date
                                    [special_price_end] => date
                                    [selling_price] => decimal(18,4) unsigned
                                    [sku] => varchar(191)
                                    [manage_stock] => tinyint(1)
                                    [qty] => int(11)
                                    [in_stock] => tinyint(1)
                                    [is_default] => tinyint(1)
                                    [is_active] => tinyint(1)
                                    [position] => int(10) unsigned
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [product_variations] => Array
                        (
                            [rows] => 0
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_id] => int(10) unsigned
                                    [variation_id] => int(10) unsigned
                                )

                        )

                    [product_weight_charges] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [weight] => decimal(8,2)
                                    [charge] => decimal(10,2)
                                    [status] => tinyint(1)
                                    [created_by] => bigint(20) unsigned
                                    [updated_by] => bigint(20) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [products] => Array
                        (
                            [rows] => 141
                            [idcol] => id
                            [last_id] => 175
                            [avg_row_len] => 348
                            [all_data_len] => 49152
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [brand_id] => int(10) unsigned
                                    [tax_class_id] => int(10) unsigned
                                    [slug] => varchar(191)
                                    [weight] => decimal(8,2)
                                    [price] => decimal(18,4)
                                    [special_price] => decimal(18,4) unsigned
                                    [special_price_type] => varchar(191)
                                    [special_price_start] => date
                                    [special_price_end] => date
                                    [selling_price] => decimal(18,4) unsigned
                                    [sku] => varchar(191)
                                    [manage_stock] => tinyint(1)
                                    [qty] => int(11)
                                    [in_stock] => tinyint(1)
                                    [viewed] => int(10) unsigned
                                    [is_active] => tinyint(1)
                                    [new_from] => datetime
                                    [new_to] => datetime
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                    [is_virtual] => tinyint(1)
                                )

                        )

                    [related_products] => Array
                        (
                            [rows] => 1383
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 59
                            [all_data_len] => 81920
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_id] => int(10) unsigned
                                    [related_product_id] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [reminders] => Array
                        (
                            [rows] => 0
                            [idcol] => id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [user_id] => int(10) unsigned
                                    [code] => varchar(191)
                                    [completed] => tinyint(1)
                                    [completed_at] => datetime
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [reviews] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 3
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [reviewer_id] => int(10) unsigned
                                    [product_id] => int(10) unsigned
                                    [rating] => int(11)
                                    [reviewer_name] => varchar(191)
                                    [comment] => text
                                    [is_approved] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [role_translations] => Array
                        (
                            [rows] => 2
                            [idcol] => id
                            [last_id] => 2
                            [avg_row_len] => 8192
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [role_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [roles] => Array
                        (
                            [rows] => 2
                            [idcol] => id
                            [last_id] => 2
                            [avg_row_len] => 8192
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [permissions] => text
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [search_terms] => Array
                        (
                            [rows] => 27
                            [idcol] => id
                            [last_id] => 27
                            [avg_row_len] => 606
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [term] => varchar(191)
                                    [results] => int(10) unsigned
                                    [hits] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [setting_translations] => Array
                        (
                            [rows] => 90
                            [idcol] => id
                            [last_id] => 90
                            [avg_row_len] => 182
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [setting_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [value] => longtext
                                )

                        )

                    [settings] => Array
                        (
                            [rows] => 402
                            [idcol] => id
                            [last_id] => 403
                            [avg_row_len] => 163
                            [all_data_len] => 65536
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [key] => varchar(191)
                                    [is_translatable] => tinyint(1)
                                    [plain_value] => text
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [slider_slide_translations] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [slider_slide_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [file_id] => int(10) unsigned
                                    [caption_1] => varchar(191)
                                    [caption_2] => varchar(191)
                                    [call_to_action_text] => varchar(191)
                                    [direction] => varchar(191)
                                )

                        )

                    [slider_slides] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [slider_id] => int(10) unsigned
                                    [options] => text
                                    [call_to_action_url] => varchar(191)
                                    [open_in_new_window] => tinyint(1)
                                    [position] => int(11)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [slider_translations] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [slider_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [sliders] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [speed] => int(11)
                                    [autoplay] => tinyint(1)
                                    [autoplay_speed] => int(11)
                                    [fade] => tinyint(1)
                                    [dots] => tinyint(1)
                                    [arrows] => tinyint(1)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [tag_translations] => Array
                        (
                            [rows] => 42
                            [idcol] => id
                            [last_id] => 97
                            [avg_row_len] => 390
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [tag_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [tags] => Array
                        (
                            [rows] => 42
                            [idcol] => id
                            [last_id] => 97
                            [avg_row_len] => 390
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [slug] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [tax_class_translations] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [tax_class_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [label] => varchar(191)
                                )

                        )

                    [tax_classes] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [based_on] => varchar(191)
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [tax_rate_translations] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [tax_rate_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [tax_rates] => Array
                        (
                            [rows] => 1
                            [idcol] => id
                            [last_id] => 1
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [tax_class_id] => int(10) unsigned
                                    [country] => varchar(191)
                                    [state] => varchar(191)
                                    [city] => varchar(191)
                                    [zip] => varchar(191)
                                    [rate] => decimal(8,4) unsigned
                                    [position] => int(10) unsigned
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [throttle] => Array
                        (
                            [rows] => 47
                            [idcol] => id
                            [last_id] => 50
                            [avg_row_len] => 348
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [user_id] => int(10) unsigned
                                    [type] => varchar(191)
                                    [ip] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [transactions] => Array
                        (
                            [rows] => 0
                            [idcol] => order_id
                            [last_id] => 0
                            [avg_row_len] => 0
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [order_id] => int(10) unsigned
                                    [transaction_id] => varchar(191)
                                    [payment_method] => varchar(191)
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [translation_translations] => Array
                        (
                            [rows] => 1358
                            [idcol] => id
                            [last_id] => 1358
                            [avg_row_len] => 84
                            [all_data_len] => 114688
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [translation_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [value] => text
                                )

                        )

                    [translations] => Array
                        (
                            [rows] => 1358
                            [idcol] => id
                            [last_id] => 1358
                            [avg_row_len] => 96
                            [all_data_len] => 131072
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [key] => varchar(191)
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [up_sell_products] => Array
                        (
                            [rows] => 1186
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 55
                            [all_data_len] => 65536
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [product_id] => int(10) unsigned
                                    [up_sell_product_id] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [updater_scripts] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 3
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [script] => varchar(191)
                                )

                        )

                    [user_activation_codes] => Array
                        (
                            [rows] => 10
                            [idcol] => id
                            [last_id] => 11
                            [avg_row_len] => 1638
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [user_id] => bigint(20) unsigned
                                    [phone] => varchar(191)
                                    [otp_media] => enum('sms')
                                    [activation_code] => varchar(191)
                                    [expiry] => timestamp
                                    [created_by] => bigint(20) unsigned
                                    [updated_by] => bigint(20) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [user_roles] => Array
                        (
                            [rows] => 1
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 16384
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [user_id] => int(10) unsigned
                                    [role_id] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [users] => Array
                        (
                            [rows] => 10
                            [idcol] => id
                            [last_id] => 11
                            [avg_row_len] => 1638
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [name] => varchar(191)
                                    [dob] => date
                                    [first_name] => varchar(191)
                                    [last_name] => varchar(191)
                                    [email] => varchar(191)
                                    [phone] => varchar(191)
                                    [password] => varchar(191)
                                    [permissions] => text
                                    [last_login] => datetime
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [variation_translations] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 5
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [variation_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [name] => varchar(191)
                                )

                        )

                    [variation_value_translations] => Array
                        (
                            [rows] => 24
                            [idcol] => id
                            [last_id] => 33
                            [avg_row_len] => 682
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [variation_value_id] => int(10) unsigned
                                    [locale] => varchar(191)
                                    [label] => varchar(191)
                                )

                        )

                    [variation_values] => Array
                        (
                            [rows] => 24
                            [idcol] => id
                            [last_id] => 33
                            [avg_row_len] => 682
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [uid] => varchar(191)
                                    [variation_id] => int(10) unsigned
                                    [value] => varchar(191)
                                    [position] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [variations] => Array
                        (
                            [rows] => 3
                            [idcol] => id
                            [last_id] => 5
                            [avg_row_len] => 5461
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => int(10) unsigned
                                    [uid] => varchar(191)
                                    [type] => varchar(191)
                                    [is_global] => tinyint(1)
                                    [position] => int(10) unsigned
                                    [deleted_at] => timestamp
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [wish_lists] => Array
                        (
                            [rows] => 12
                            [idcol] =>
                            [last_id] => 0
                            [avg_row_len] => 1365
                            [all_data_len] => 16384
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [user_id] => int(10) unsigned
                                    [product_id] => int(10) unsigned
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                    [zones] => Array
                        (
                            [rows] => 494
                            [idcol] => id
                            [last_id] => 496
                            [avg_row_len] => 132
                            [all_data_len] => 65536
                            [engine] => InnoDB
                            [columns] => Array
                                (
                                    [id] => bigint(20) unsigned
                                    [name] => varchar(191)
                                    [city_id] => bigint(20) unsigned
                                    [status] => enum('1','0')
                                    [created_at] => timestamp
                                    [updated_at] => timestamp
                                )

                        )

                )

        )

)
</pre><hr /><p><strong>Bullet run time, seconds.</strong></p><pre>0.88</pre><hr />
