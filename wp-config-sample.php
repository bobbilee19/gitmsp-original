<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'database_name_here');

/** MySQL database username */
define('DB_USER', 'username_here');

/** MySQL database password */
define('DB_PASSWORD', 'password_here');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '16K.4F.!@gC|(.-h&;Z{Y3tq-A)$rI@cT+ai_%m+--|`V;?+lL@u#>u^@O$v}6Ha');
define('SECURE_AUTH_KEY',  'W>y/n5EjbsS^Tjrz+(8q+>bf+?&z}u-nF!l#2xxUe-2*N9wK1dY]Yw*V~zI<8c4_');
define('LOGGED_IN_KEY',    '5YMj!?A25*EpWGoCCSFlZ1swLimGcU1_oyxa>* qCV|0+b-QWe94$x]|BNWk7:{,');
define('NONCE_KEY',        '$bb^<G*{j#Dks@~/&gK=>A3-OXM2k[-+5[-iw-GkxVbYn~UTlvoC%Q0gG6X*1mR2');
define('AUTH_SALT',        'Bg!~eg:XVbA;=I{ik&O/p~`{,3n(GM+#HR9=k>jE<v[iiADWDIWw=f|]PQaGzQ/L');
define('SECURE_AUTH_SALT', ']a&^zKE^uu=gui1Y-pq:^%W2[-snpD6AU-Zme)k`fAc},e|ens:CPJuT+Y=-wxb;');
define('LOGGED_IN_SALT',   '!pPJYt~^jym`:o%ai%N)9g)W6[.vkt5S$Lf)a1y>pIbmr>yb{@3F]d+7z.Gt+yMS');
define('NONCE_SALT',       '~+Ye7^uhkPlv8Zm63Mn`f-Hb.3;Ae`.L=G6:g6%,Bu`)wV.Hn4JSxR-:8D$tEW/j');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
