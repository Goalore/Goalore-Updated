<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'goalore' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'OmFfj/$;~+j9$ nTqP`zn:uFA^Z<(w-D66H2^iBRgNxI}v=_{:,sH8d)<Uc}XCMX' );
define( 'SECURE_AUTH_KEY',  'PJ4PD)YOY3afI>zJtI8nI%H@w(Kf{!CRV;^vfkO8oHlW+<1<|C BKM& ^-||sD=i' );
define( 'LOGGED_IN_KEY',    'sX+Is!TKN0m8cOyUw%~K3Zkxyk&!3uw,kA n[Ar_FPZd6| s1b4[^d,1rOBy@$_1' );
define( 'NONCE_KEY',        'sGl]RIjC2+$qgHXL-z%FC5Cteyh(MRmT4O]^c5k)po}h-(B5d.uiXY&12 0BI??2' );
define( 'AUTH_SALT',        '&)IC}O_dv*4H+6}@3DL{SF cq^U$a_RC*Q@;@Q*u#bHv]*wcK#H1;Lx207O-j}FC' );
define( 'SECURE_AUTH_SALT', 'zi; wA}E8x)sTwC] i# H>N(00*prL70,4E!)c9|k^jkb m=v-:V3m|<1{j}E5*;' );
define( 'LOGGED_IN_SALT',   'DAFh6P:7!c-1Z|H]zZy55VAFkhyd<(8<O+V$#z-QO]$:##G-qXG,GC^+z8}*;#CE' );
define( 'NONCE_SALT',       'K>e&+/%zIlA=va)5*-{.?.=pzs8G|UkZV.W&H|z@7t+VMUT<TR]a~;)(x!JXN4KO' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
