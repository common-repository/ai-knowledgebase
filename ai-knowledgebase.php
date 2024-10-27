<?php

/**
 * Plugin Name: AI KnowledgeBase
 * Plugin URI: https://plugins.modeltheme.com/ai-knowledgebase
 * Description: Seamlessly integrate your knowledge base to provide instant, context-aware assistance for users. Boost support efficiency and user satisfaction with AI-driven answers.
 * Version: 1.1.2
 * Author: ModelTheme
 * Author URI: https://modeltheme.com/
 * License: GPL2
 * Text Domain: ai-knowledgebase
 * 
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Prevent direct access.
}
// plugin path
define( 'AIKNOWLEDGEBASE', __FILE__ );
define( 'AIKNOWLEDGEBASE_TEXTDOMAIN', "ai-knowledgebase" );
define( 'AIKNOWLEDGEBASE_PATH', plugin_dir_path( AIKNOWLEDGEBASE ) );
define( 'AIKNOWLEDGEBASE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AIKNOWLEDGEBASE_PLUGIN_PATH', dirname( AIKNOWLEDGEBASE ) );
define( 'AIKNOWLEDGEBASE_PLUGIN_INC', trailingslashit( path_join( AIKNOWLEDGEBASE_PLUGIN_PATH, 'includes' ) ) );
include AIKNOWLEDGEBASE_PATH . 'includes/core-data.php';
include AIKNOWLEDGEBASE_PATH . 'includes/admin-functions.php';
include AIKNOWLEDGEBASE_PATH . 'includes/loader.php';