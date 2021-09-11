<?php
define( 'WP_CACHE', true );
/** Enable W3 Total Cache */
 // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
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
define( 'DB_NAME', 'oturthwm_wp573' );

/** MySQL database username */
define( 'DB_USER', 'oturthwm_wp573' );

/** MySQL database password */
define( 'DB_PASSWORD', '510G!38d)I.S(p' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '3ve6soi2w39edvvxuqstuvugjgd6kic84vqlalw6zqcvrd5niaduinm0wjekdm2v' );
define( 'SECURE_AUTH_KEY',  'eqam3vmw4rqlhrunhdysh699td56rrkodnht9y66df5xmgphspufmobpsa3lwcj9' );
define( 'LOGGED_IN_KEY',    'yen9z9sybp0mibnedtmvq1qfmb2zyyunohvpfenxciimuouoozty7ugmnvmsxxhi' );
define( 'NONCE_KEY',        'qjanij4vqaosrs2ip4rac6gjfbuhh9uyphezp0apsgvcdymp3mu6fu4ujheraeny' );
define( 'AUTH_SALT',        'lhjwzns2wqzhwlbioxm6om2zgxfkwa3eviimqzgcqflvzfxopt0skr2liyynrral' );
define( 'SECURE_AUTH_SALT', 'wpdbuvfvpf6tpjinl4lllmwbcqv4hsxan3rsyp4nhxbxf3fsdh8jspe9hioakvx1' );
define( 'LOGGED_IN_SALT',   '1kb2fjjmguh5icdltg1g8ofby4box7h1uzntjddni08yqfxpprt4fr870xcnxu4b' );
define( 'NONCE_SALT',       'gqd0bnmpq5rj5a0qyyl3ksfrosff6gw8wi3r5ysvfn69v8mws0gdelxkh1tpil98' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wprd_';

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
