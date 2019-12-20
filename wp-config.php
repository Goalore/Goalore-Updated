<?php
# Database Configuration
define( 'DB_NAME', 'wp_staginggoalore' );
define( 'DB_USER', 'staginggoalore' );
define( 'DB_PASSWORD', '4J1wnn1WkDP0qRJZJ-iL' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         '+B~>71]-d5*KJbod+|/@i4mvC(w,Zj!W;Igmm0~`$!}2pRKzB,oXIz`)Gtx*NfIz');
define('SECURE_AUTH_KEY',  '-;78vht,5syfDv~:gX7`5 `*M{Z2yi I_,X^~m%Y/)szR4w9s[YQgORwj$X;2bz ');
define('LOGGED_IN_KEY',    'Mt|DA2gG>g.R}l|#hA<7r00Z/=~-e>Uq:-F/5S{0yN]%NXe7.|sNZ:+i@:yWRi62');
define('NONCE_KEY',        '~Zl46t>W:W0=iw*4lgSry[{:*`NQZ,9o<(n1o8rV6RMM6-}wjt-AHbvBDZ0fek5B');
define('AUTH_SALT',        'pi}T:!aI`Z<n4>jqq^H/hj-sU`_9+VC+-2C_AjW,|Q7i~I-#?Hgmqz/kMIqH;Zn4');
define('SECURE_AUTH_SALT', 'AV7-/3-m*bw2Z_*+orMw2ur33y:*qfrTb;-k_Ne7N96q&AMy7C)0jNS+D[ZU|*29');
define('LOGGED_IN_SALT',   '`Y?+s-=(b3e-:_ku|E=r9gxB!lu{[-/M`QFhNa&RN~;?.d?z=g{&ObnK@Sli$A^*');
define('NONCE_SALT',       'l$n|l_|q&s BhIfl~E+;R8@v-L&u)y=ZJ!$Q[F1+.8dvg+g8?oVNKhF|KG0e_U{o');


# Localized Language Stuff

define( 'WP_DEBUG', true );

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'staginggoalore' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'PWP_ROOT_DIR', '/nas/wp' );

define( 'WPE_APIKEY', 'b904d27f4f76774d218c966a3d73ad662515cd3c' );

define( 'WPE_CLUSTER_ID', '100962' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'staginggoalore.wpengine.com', );

$wpe_varnish_servers=array ( 0 => 'pod-100962', );

$wpe_special_ips=array ( 0 => '104.197.244.27', );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );
define('WPLANG','');

# WP Engine ID


# WP Engine Settings


/*
 *CUSTOM ROUTES SLUGS
 */
define(PROFILE, 'profile');
define(MY_CONNECTIONS, 'my-connections');
define(SETTINGS, 'settings');
define(SearchResult, 'search-result');
define(InviteFriend, 'invite-friend');
define(CONTACT, 'contact');




# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', __DIR__ . '/');
require_once(ABSPATH . 'wp-settings.php');




