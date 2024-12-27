<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_plugin_course');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', '');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         ' Cl7m]X<M=eGpPeMobqdn91VxQDaYzshlZp{*#M+^ACgF#LJPVvg=M:>Fk?9FmnS');
define('SECURE_AUTH_KEY',  'rbVl:3Oj4GOD4HEs=tl6D5DOwXX@Gm@v9oKR#-_]tX=i2I$rMuDh#O}Uzf5oI|ns');
define('LOGGED_IN_KEY',    'gd:L}D?xbLldUbWzA5d#]x}p]#kxrpA/KI<_iqLce?|p<9kLLV4#T)[:UaWad$=:');
define('NONCE_KEY',        'm%~Fp`EskA:tV/$U$E;BKIGGr4ai>AV&a(fhd.^[UvGA&[=)T^If CssypZ4=(w}');
define('AUTH_SALT',        'J/|`6%-!]nXX4I0LsieVExjYkAZtLQ@T6@zzBS(>#9=iXRTm-%!MGw7O:NzG}G,2');
define('SECURE_AUTH_SALT', '~rsM@c[!G&i.sqS[UArA}KCu5HBZ6~4fjsi:3,oUcJBlfHjruJw;>6=IZ|F(v?~%');
define('LOGGED_IN_SALT',   'wsf-J-v/mLc`ceoN[AR8{)lvl-l1r?i8PUP{+9HV*!!28a<H96D7n6,c0{UKiG9K');
define('NONCE_SALT',       't2)5`$4PO[Bmxed=ln2F{d^_%pD913]auJ W>SFh~DdO4cu&+I9tMc_^$8N,,DJ)');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
