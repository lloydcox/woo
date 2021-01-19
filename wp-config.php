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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'woo' );

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
define( 'AUTH_KEY',         'Ns.LwADTroUTd&O%$a#w=mxQY*,l~|zSD1bN1Nv,5(dXD3e5/mt>8s,yYr|k8d^Z' );
define( 'SECURE_AUTH_KEY',  'dE}_08hC31NvW=|o)N=5Eg5T*i;&7SMZ#:[0AQ+ZQtH`@8yv-cv9I4%K2Pne%(RV' );
define( 'LOGGED_IN_KEY',    'yof)j|$Qb@+<KOJdd}L)VErw9$^LDj`wL)m37bjSe5TZ#5{)_Z$[9Fa-;+ rFsLe' );
define( 'NONCE_KEY',        ',PX:F>WZo|Kd^%~2[k?pW jkCY;Em(),H/],]/sb;`o|VkHHSh[Dy6rzD$9m6T#t' );
define( 'AUTH_SALT',        'rb{bD.m_*N0qESsp3a=o2h|Q|OHy4-zdFNH)NA/ _,TCmgcB%J0wDg,EE6PBN`n(' );
define( 'SECURE_AUTH_SALT', 'eBwd|c#BY5Aa>(db&KH7Kc-c1.L&>%Bq2PtHN#yWPXe(61_[jwu3YZUjj*!vU3^)' );
define( 'LOGGED_IN_SALT',   ',0bFxFS]UQ;eS7UV(Jf+u}XzfN!SF ~I=3F72fh>ll l2|)b(?[aP=z:FNt/T]j4' );
define( 'NONCE_SALT',       'zs/@8R&1<Q#R|X.<p;bXShuDbxE):cim-GelIx]Br3zl 5yE4#zfy^VBbBZ{T0p+' );

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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
