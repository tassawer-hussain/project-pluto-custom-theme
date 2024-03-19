<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', '2024_pluto' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '|v-T%k`&jp:qD(@0uZY?%{wUj9IEehE3Eh4H=Q1r2UU2v#Fuj&,}Ct~9;~8G;HYb' );
define( 'SECURE_AUTH_KEY',   '8vR@e?J!&uJ[W,*lq{3|X_OH_aOM+ON`fp^M<p#(O4`#I=zHdLr@w`Rct^ *KH]0' );
define( 'LOGGED_IN_KEY',     ']-hj]m}PnmTYzBrI<TMbk8mcISC>-Lc;-,^O}]W6?R[IhN#mRDUwHFwKqk7o7w= ' );
define( 'NONCE_KEY',         'r% 3M2};ihrXULjd(pfq?{ufv<DcJX]M@zZ`pj^N0Jlpmkc6Pq2=z/1?8Rr22nS>' );
define( 'AUTH_SALT',         'Vxk}4DL0h]`/&RxIu^?7e|)QKp>~MkSZ5-D]n_4 -Rfl4Q}cX?w*r-bD8f?)<JsH' );
define( 'SECURE_AUTH_SALT',  'XQyU[w}U.7&~iq<LQ{8;Z)VDEX*dH3@h`@ZvIA* Q,<uy5~-9Pr[Yh!$L$1%iJaR' );
define( 'LOGGED_IN_SALT',    '{4CRx&haQ#jL7O~$nzOGas1~HlW Gv3L5v34-_!9$.~FHtxJi:IYi@C2ZCO/Q=XM' );
define( 'NONCE_SALT',        '%Wc)y}A1!,0g%sPeoD/eI;PT+45F>_u5IDW}>r8j4?2k 9Is5$PpBfFRhHQj(28n' );
define( 'WP_CACHE_KEY_SALT', 'CR:{HuuJC)E>ZCkE|qbq<rc~Ihx_V6yXHPF,^IX+JD>8=qsn!8mX?*T_yvac>&&H' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
