<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
global $aiknowledgebase__;
// Initialize the process. Everything starts from here!
add_action( 'init', 'aiknowledgebase_activation_process_handler' );
add_action( 'rest_api_init', 'aiknowledgebase_register_routes' );
// Activate and commence plugin.
register_activation_hook( AIKNOWLEDGEBASE, 'aiknowledgebase_activation' );
// Register deactivation process.
register_deactivation_hook( AIKNOWLEDGEBASE, 'aiknowledgebase_deactivation' );
function aiknowledgebase_add_extra_plugin_links(  $links  ) {
    $action_links = array();
    $action_links['settings'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=aiknowledgebase-openai-settings' ) ), 'Settings' );
    return array_merge( $action_links, $links );
}

/**
 * Frontend script and style enqueuing
 */
function aiknowledgebase_load_scripts() {
    global $aiknowledgebase__;
    // enqueue style.
    wp_enqueue_style(
        'aiknowledgebase-frontend',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/frontend.css',
        array(),
        $aiknowledgebase__['plugin']['version'],
        'all'
    );
    // register script.
    wp_register_script(
        'aiknowledgebase-frontend',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/frontend.js',
        array('jquery'),
        $aiknowledgebase__['plugin']['version'],
        true
    );
    wp_enqueue_script(
        'aiknowledgebase-frontend',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/frontend.js',
        array('jquery'),
        $aiknowledgebase__['plugin']['version'],
        false
    );
    // handle localized variables.
    $redirect_url = get_option( 'wmc_redirect' );
    if ( '' === $redirect_url ) {
        $redirect_url = 'cart';
    }
    // add localized variables.
    $localaized_values = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'ajax-nonce' ),
    );
    // apply filter.
    $localaized_values = apply_filters( 'aiknowledgebase_front_local_vars', $localaized_values );
    // localize script.
    wp_localize_script( 'aiknowledgebase-frontend', 'aiknowledgebase', $localaized_values );
}

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function aiknowledgebase_admin_enqueue_scripts() {
    global $aiknowledgebase__;
    $screen = get_current_screen();
    if ( !in_array( $screen->id, $aiknowledgebase__['plugin']['screen'], true ) ) {
        return;
    }
    // enqueue style.
    wp_register_style(
        'aiknowledgebase_admin_style',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/admin/admin.css',
        false,
        $aiknowledgebase__['plugin']['version']
    );
    wp_enqueue_style( 'aiknowledgebase_admin_style' );
    wp_enqueue_style(
        'aiknowledgebase-frontend',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/frontend.css',
        array(),
        $aiknowledgebase__['plugin']['version'],
        'all'
    );
    wp_register_script(
        'aiknowledgebase_admin_script',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/admin/admin.js',
        array('jquery', 'jquery-ui-slider', 'jquery-ui-sortable'),
        $aiknowledgebase__['plugin']['version'],
        true
    );
    wp_enqueue_script( 'aiknowledgebase_admin_script' );
    wp_register_script(
        'aiknowledgebase-frontend',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/frontend.js',
        array('jquery'),
        $aiknowledgebase__['plugin']['version'],
        true
    );
    wp_enqueue_script(
        'aiknowledgebase-frontend',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/frontend.js',
        array('jquery'),
        $aiknowledgebase__['plugin']['version'],
        false
    );
    $var = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'ajax-nonce' ),
    );
    if ( empty( $var['image_size'] ) || '' === $var['image_size'] || 'NaN' === $var['image_size'] ) {
        $var['image_size'] = 55;
    }
    // apply hook for editing localized variables in admin script.
    $var = apply_filters( 'aiknowledgebase_local_var', $var );
    wp_localize_script( 'aiknowledgebase_admin_script', 'aiknowledgebase', $var );
}

/**
 * Add menu and submenu pages
 */
function aiknowledgebase_add_admin_menu() {
    global $aiknowledgebase__;
    // Main menu.
    add_menu_page(
        esc_html__( 'AI KnowledgeBase', 'ai-knowledgebase' ),
        esc_html__( 'AI KnowledgeBase', 'ai-knowledgebase' ),
        'manage_options',
        'ai-knowledgebase',
        'aiknowledgebase_getting_started',
        plugin_dir_url( AIKNOWLEDGEBASE ) . 'assets/images/admin-icon.svg',
        56
    );
    // main menu label change.
    add_submenu_page(
        'ai-knowledgebase',
        esc_html__( 'AI KnowledgeBase', 'ai-knowledgebase' ),
        esc_html__( 'Getting Started', 'ai-knowledgebase' ),
        'manage_options',
        'ai-knowledgebase'
    );
    add_submenu_page(
        'ai-knowledgebase',
        esc_html__( 'AI KnowledgeBase - Content Generator', 'ai-knowledgebase' ),
        esc_html__( 'Content Generator', 'ai-knowledgebase' ),
        'manage_options',
        'aiknowledgebase-content-generator',
        'aiknowledgebase_content_generator_page'
    );
    add_submenu_page(
        'ai-knowledgebase',
        esc_html__( 'AI KnowledgeBase - OpenAI Settings', 'ai-knowledgebase' ),
        esc_html__( 'OpenAI Settings', 'ai-knowledgebase' ),
        'manage_options',
        'aiknowledgebase-openai-settings',
        'aiknowledgebase_openai_settings_page'
    );
    add_submenu_page(
        'ai-knowledgebase',
        esc_html__( 'AI KnowledgeBase - Assistant Settings', 'ai-knowledgebase' ),
        esc_html__( 'Assistant Settings', 'ai-knowledgebase' ),
        'manage_options',
        'aiknowledgebase-assistant-settings',
        'aiknowledgebase_assistant_settings_page'
    );
}

/**
 * Save aiknowledgebase admin settings
 *
 * @since 6.2
 */
function aiknowledgebase_save_settings() {
    if ( !isset( $_POST['settings_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['settings_nonce'] ), 'update_settings' ) ) {
        return;
    }
    if ( !isset( $_POST['api_key'] ) ) {
        return;
    }
    global $aiknowledgebase__;
    $api_key = sanitize_text_field( wp_unslash( $_POST['api_key'] ) );
    if ( !empty( $api_key ) ) {
        update_option( 'aiknowledgebase_api_key', $api_key );
    }
    array_push( $aiknowledgebase__['notice'], '<div id="message" class="updated inline"><p><strong>' . esc_html__( "Your settings have been saved", "ai-knowledgebase" ) . '</strong></p></div>' );
}

/**
 * Save aiknowledgebase assistant settings
 *
 * @since 6.2
 */
function aiknowledgebase_save_assistant_settings() {
    if ( !isset( $_POST['assistant_settings_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['assistant_settings_nonce'] ), 'update_assistant_settings' ) ) {
        return;
    }
    global $aiknowledgebase__;
    array_push( $aiknowledgebase__['notice'], '<div id="message" class="updated inline"><p><strong>' . esc_html__( "Your assistant settings have been saved", "ai-knowledgebase" ) . '</strong></p></div>' );
}

/**
 * Start the plugin
 */
function aiknowledgebase_activation_process_handler() {
    // add extra links right under plug.
    add_filter( 'plugin_action_links_' . plugin_basename( AIKNOWLEDGEBASE ), 'aiknowledgebase_add_extra_plugin_links' );
    // Enqueue frontend scripts and styles.
    add_action( 'wp_enqueue_scripts', 'aiknowledgebase_load_scripts' );
    // Enqueue admin script and style.
    add_action( 'admin_enqueue_scripts', 'aiknowledgebase_admin_enqueue_scripts' );
    // Add admin menu page.
    add_action( 'admin_menu', 'aiknowledgebase_add_admin_menu' );
    // Save admin settings.
    aiknowledgebase_save_settings();
    // Save assistant settings.
    aiknowledgebase_save_assistant_settings();
}

/**
 * Things to do for activating the plugin.
 */
function aiknowledgebase_activation() {
    global $wpdb;
    // main plugin activation process handler.
    aiknowledgebase_activation_process_handler();
    $upload_dir = wp_upload_dir();
    mkdir( $upload_dir['basedir'] . '/ai-knowledgebase' );
    flush_rewrite_rules();
}

/**
 * Plugin deactivation handler
 */
function aiknowledgebase_deactivation() {
    flush_rewrite_rules();
}

add_shortcode( 'aiknowledgebase_chat_session', 'aiknowledgebase_chat_session_shortcode' );
function aiknowledgebase_chat_session_shortcode() {
    $api_key = aiknowledgebase_api_key();
    $assistant_details = get_option( 'aiknowledgebase_assistant_details' );
    $shortcode_customize_assistant = get_option( 'aiknowledgebase_customize_assistant' );
    if ( isset( $shortcode_customize_assistant['shortcode_input_placeholder'] ) && $shortcode_customize_assistant['shortcode_input_placeholder'] !== '' ) {
        $shortcode_input_placeholder = esc_html( $shortcode_customize_assistant['shortcode_input_placeholder'] );
    } else {
        $shortcode_input_placeholder = esc_html__( 'Type your message here ...', 'ai-knowledgebase' );
    }
    if ( isset( $shortcode_customize_assistant['shortcode_submit_button'] ) && $shortcode_customize_assistant['shortcode_submit_button'] !== '' ) {
        $shortcode_submit_button = esc_html( $shortcode_customize_assistant['shortcode_submit_button'] );
    } else {
        $shortcode_submit_button = esc_html__( 'Submit', 'ai-knowledgebase' );
    }
    $shortcode = '';
    $shortcode .= '<div id="aiknowledgebase_chat_session_main">';
    $shortcode .= '<form id="aiknowledgebase_chat_session_form" method="POST" data-nonce="' . esc_attr( wp_create_nonce( "wp_rest" ) ) . '" data-rest-location="' . esc_attr( get_rest_url() ) . '">';
    $shortcode .= wp_nonce_field( 'send_chat_message', 'chat_message_nonce' );
    $shortcode .= '<div id="aiknowledgebase_chat_session_form_inner">';
    $shortcode .= '<input id="aiknowledgebase_chat_session_content" name="aiknowledgebase_chat_session_content" ' . (( $api_key && $assistant_details ? '' : 'disabled' )) . ' placeholder="' . (( $api_key && $assistant_details ? $shortcode_input_placeholder : esc_html__( 'You need to setup the OpenAI API Key and Assistant Settings first', "ai-knowledgebase" ) )) . '" required>';
    $shortcode .= '<div id="aiknowledgebase_chat_session_submit_holder">';
    if ( $api_key && $assistant_details ) {
        $shortcode .= '<input type="submit" value="' . $shortcode_submit_button . '" id="aiknowledgebase_chat_session_submit">';
    }
    $shortcode .= '</div>';
    $shortcode .= '</div>';
    $shortcode .= '</form>';
    $shortcode .= '</div>';
    return $shortcode;
}

function aiknowledgebase_register_routes() {
    register_rest_route( 'ai-knowledgebase', '/generate-assistant', array(
        'methods'             => 'POST',
        'callback'            => 'aiknowledgebase_generate_assistant_callback',
        'permission_callback' => function ( $request ) {
            return is_user_logged_in();
        },
    ) );
    register_rest_route( 'ai-knowledgebase', '/reset-assistant', array(
        'methods'             => 'POST',
        'callback'            => 'aiknowledgebase_reset_assistant_callback',
        'permission_callback' => function ( $request ) {
            return is_user_logged_in();
        },
    ) );
    register_rest_route( 'ai-knowledgebase', '/customize-assistant', array(
        'methods'             => 'POST',
        'callback'            => 'aiknowledgebase_customize_assistant_callback',
        'permission_callback' => function ( $request ) {
            return true;
        },
    ) );
    register_rest_route( 'ai-knowledgebase', '/send-message', array(
        'methods'             => 'POST',
        'callback'            => 'aiknowledgebase_send_message_callback',
        'permission_callback' => function ( $request ) {
            return true;
        },
    ) );
}

// ADMIN ONLY - GENERATE OPENAI ASSISTANT
function aiknowledgebase_generate_assistant_callback(  $request  ) {
    check_admin_referer( 'update_assistant_settings', 'assistant_settings_nonce' );
    $model = sanitize_text_field( $_POST["assistant_model"] );
    $training_type = sanitize_text_field( $_POST['assistant_training_type'] );
    $post_types = get_post_types( array(
        'public' => true,
    ) );
    $training_types = array('file_upload');
    foreach ( $post_types as $post_type ) {
        $training_types[] = $post_type;
    }
    if ( !in_array( $model, array('gpt-3.5-turbo-0125', 'gpt-4-turbo', 'gpt-4o') ) ) {
        die;
    }
    if ( !in_array( $training_type, $training_types ) ) {
        die;
    }
    if ( $training_type === "file_upload" ) {
        $file_info = wp_check_filetype( basename( $_FILES["assistant_file"]["name"] ) );
        if ( empty( $file_info["ext"] ) ) {
            die;
        }
        $file = $_FILES["assistant_file"];
    } else {
        $file = aiknowledgebase_create_data_file( $training_type );
        if ( !is_file( $file['name'] ) ) {
            die;
        }
    }
    $response = aiknowledgebase_generate_assistant( $file, $model );
    $new_file_name = basename( pathinfo( $file['name'], PATHINFO_FILENAME ) . '_' . str_replace( 'file-', '', $response['assistant_details']['file_id'] ) . '.' . pathinfo( $file['name'], PATHINFO_EXTENSION ) );
    $upload_dir = wp_upload_dir();
    $save_path = $upload_dir['basedir'] . '/ai-knowledgebase/' . $new_file_name;
    rename( $file['tmp_name'], $save_path );
    $assistant_details = get_option( 'aiknowledgebase_assistant_details' );
    $assistant_details['file_url'] = $upload_dir['baseurl'] . '/ai-knowledgebase/' . $new_file_name;
    $assistant_details['file_name'] = $new_file_name;
    update_option( 'aiknowledgebase_assistant_details', $assistant_details );
    if ( $response['success'] ) {
        return new \WP_REST_Response(array(
            "success"  => $response['success'],
            "response" => esc_html__( "Assistant successfully generated! Page will automatically reload.", "ai-knowledgebase" ),
        ), 200);
    } else {
        return new \WP_REST_Response(array(
            "success" => $response['success'],
        ), 500);
    }
}

// ADMIN ONLY - RESET THE ASSISTANT DETAILS
function aiknowledgebase_reset_assistant_callback(  $request  ) {
    $response = aiknowledgebase_reset_assistant();
    if ( $response["delete_option"] ) {
        return new \WP_REST_Response(array(
            "success"  => $response,
            "response" => esc_html__( "Assistant successfully removed! Page will automatically reload.", "ai-knowledgebase" ),
        ), 200);
    } else {
        return new \WP_REST_Response(array(
            "success" => $response,
        ), 500);
    }
}

// ADMIN ONLY - CUSTOMIZE THE ASSISTANT TEXTS
function aiknowledgebase_customize_assistant_callback(  $request  ) {
    check_admin_referer( 'customize_assistant', 'customize_assistant_nonce' );
    $customize_assistant = get_option( "aiknowledgebase_customize_assistant" );
    $shortcode_input_placeholder = sanitize_text_field( $_POST["shortcode_input_placeholder"] );
    $shortcode_submit_button = sanitize_text_field( $_POST["shortcode_submit_button"] );
    if ( !is_string( $shortcode_input_placeholder ) || !is_string( $shortcode_submit_button ) ) {
        die;
    }
    $customize_assistant["shortcode_input_placeholder"] = $shortcode_input_placeholder;
    $customize_assistant["shortcode_submit_button"] = $shortcode_submit_button;
    update_option( "aiknowledgebase_customize_assistant", $customize_assistant );
    $customize_assistant = get_option( "aiknowledgebase_customize_assistant" );
    return new \WP_REST_Response(array(
        "success"  => true,
        "response" => esc_html__( "Assistant successfully customized! Page will automatically reload.", "ai-knowledgebase" ),
        "data"     => $customize_assistant,
    ), 200);
}

// ADMIN & USERS - SEND MESSAGE TO OPENAI ASSISTANT AND RETRIEVE RESPONSE
function aiknowledgebase_send_message_callback(  $request  ) {
    check_admin_referer( 'send_chat_message', 'chat_message_nonce' );
    global $wpdb;
    $api_key = aiknowledgebase_api_key();
    $assistant_details = get_option( 'aiknowledgebase_assistant_details' );
    if ( !($api_key && $assistant_details) ) {
        return new \WP_REST_Response(array(
            'content' => array(array(
                'text' => array(
                    'value' => esc_html__( 'Missing OpenAI API Key / Assistant Settings', "ai-knowledgebase" ),
                ),
            )),
        ), 500);
    }
    $content = sanitize_text_field( $_POST["chat_session_content"] );
    $is_first_message = sanitize_text_field( $_POST["is_first_message"] );
    if ( !is_string( $content ) ) {
        die;
    }
    if ( $is_first_message === "true" ) {
        $create_thread_response = aiknowledgebase_openai_create_thread( $api_key );
        $thread_id = $create_thread_response->id;
    } else {
        if ( $is_first_message === "false" ) {
            $thread_id = sanitize_text_field( $_POST["thread_id"] );
        }
    }
    $response = aiknowledgebase_send_message( $content, $thread_id );
    return new \WP_REST_Response(array(
        'response'  => $response['message_response']->data[0],
        'thread_id' => $thread_id,
    ), 200);
}
