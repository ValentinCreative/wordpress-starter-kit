<?php
// ====================================================================================
// Charger les informations de base de données et les paramètres de développement local
// ====================================================================================
if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
	// ================================
	// Paramètres pour la partie locale
	// ================================
	ini_set( 'display_errors', 1 );
	define( 'WP_DEBUG_DISPLAY', true );
	define( 'WP_LOCAL_DEV', true );
	include( dirname( __FILE__ ) . '/local-config.php' );
} elseif (	file_exists( dirname( __FILE__ ) . '/dev-config.php' ) ) {
	// ==================================================
	// Paramètres pour le deploiement de la branche "dev"
	// ==================================================
	ini_set( 'display_errors', 1 );
	define( 'WP_DEBUG_DISPLAY', true );
	define( 'WP_LOCAL_DEV', false );
	include( dirname( __FILE__ ) . '/dev-config.php' );
} elseif ( file_exists( dirname( __FILE__ ) . '/prod-config.php' ) ) {
	// ===================================================
	// Paramètres pour le deploiement de la branche "prod"
	// ===================================================
	ini_set( 'display_errors', 0 );
	define( 'WP_DEBUG_DISPLAY', false );
	define( 'WP_LOCAL_DEV', false );
	include( dirname( __FILE__ ) . '/prod-config.php' );
} else {
	ini_set( 'display_errors', 0 );
	define( 'WP_DEBUG_DISPLAY', false );
	define( 'WP_LOCAL_DEV', false );
	define( 'DB_NAME', '%%DB_NAME%%' );
	define( 'DB_USER', '%%DB_USER%%' );
	define( 'DB_PASSWORD', '%%DB_PASSWORD%%' );
	define( 'DB_HOST', '%%DB_HOST%%' );
}

// ===============================
// Répertoire content personnalisé
// ===============================
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/content' );

// ================================================
// Vous n'avez normalement pas à changer ceci
// ================================================
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// ===============================================================
// Pour la sécurité
// Trouvez les ici : https://api.wordpress.org/secret-key/1.1/salt
// ===============================================================
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

// =============================================================================
// Préfix des tables
// Changer ceci si vous avez plusieurs installations sur la même base de données
// =============================================================================
$table_prefix  = 'wp_';

// ================================
// Language
// Leave blank for American English
// ================================
define('WPLANG', 'fr_FR');

// ===================
// Masquer les erreurs
// ===================
ini_set( 'display_errors', 0 );
define( 'WP_DEBUG_DISPLAY', false );

// ===========================
// Augmenter la limite mémoire
// ===========================
define('WP_MEMORY_LIMIT', '100M');

// ==============================
// Debug mode
// Debugging? Activez ces lignes.
// ==============================
// define( 'SAVEQUERIES', true );
// define( 'WP_DEBUG', true );

// ===============================================
// Charger la config Memcached si vous en avez une
// ===============================================
if ( file_exists( dirname( __FILE__ ) . '/memcached.php' ) )
	$memcached_servers = include( dirname( __FILE__ ) . '/memcached.php' );

// ==========================================================================================================
// Cela peut être utilisé pour définir par programmation la branche lors du déploiement (ex: production, dev)
// ==========================================================================================================
define( 'WP_STAGE', '%%WP_STAGE%%' );
define( 'STAGING_DOMAIN', '%%WP_STAGING_DOMAIN%%' );

// ===================
// Bootstrap WordPress
// ===================
if ( !defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/wp/' );
require_once( ABSPATH . 'wp-settings.php' );
