<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
/**
 * Display pre-saved admin notices
 */
function aiknowledgebase_settings_display_saved_notices() {
    global $aiknowledgebase__;
    // Display notices.
    foreach ( $aiknowledgebase__['notice'] as $notice ) {
        echo wp_kses_post( $notice );
    }
}

/**
 * Admin settings template functions.
 *
 * Display settings title
 */
function aiknowledgebase_settings_display_title() {
    echo sprintf( '<h1 class=""><b>%s</b></h1>', esc_html( get_admin_page_title() ) );
}

/**
 * Get menu url.
 *
 * @param string $menu tab name.
 */
function aiknowledgebase_settings_menu_url(  $menu  ) {
    global $aiknowledgebase__;
    $url = '';
    foreach ( $aiknowledgebase__['menu'] as $page => $tabs ) {
        if ( !in_array( $menu, $tabs, true ) ) {
            continue;
        }
        if ( 'Getting Started' === $menu ) {
            $url = admin_url( 'admin.php?page=ai-knowledgebase' );
        } else {
            if ( 'Content Generator' === $menu ) {
                $url = admin_url( 'admin.php?page=aiknowledgebase-content-generator' );
            } else {
                if ( 'Assistant Settings' === $menu ) {
                    $url = admin_url( 'admin.php?page=aiknowledgebase-assistant-settings' );
                } else {
                    if ( 'Chat History' === $menu ) {
                        $url = admin_url( 'admin.php?page=aiknowledgebase-chat-history' );
                    } else {
                        if ( 'OpenAI Settings' === $menu ) {
                            $url = admin_url( 'admin.php?page=aiknowledgebase-openai-settings&tab=' . sanitize_title( $menu ) . '&nonce=' . wp_create_nonce( 'aiknowledgebase_tab_nonce' ) );
                        } else {
                            if ( 'License Activation' === $menu ) {
                                $url = admin_url( 'admin.php?page=aiknowledgebase-license-activation' );
                            }
                        }
                    }
                }
            }
        }
    }
    return $url;
}

/**
 * Display navigation items
 */
function aiknowledgebase_settings_menu() {
    global $aiknowledgebase__;
    $section = $aiknowledgebase__['settings_section'];
    $tab = $aiknowledgebase__['settings_tab'];
    foreach ( $aiknowledgebase__['menu'][$section] as $menu ) {
        $classes = array();
        $url = aiknowledgebase_settings_menu_url( $menu );
        // check if this menu is active?
        if ( sanitize_title( $menu ) === $tab ) {
            $classes[] = 'nav-tab-active';
        }
        ?>
		<a class="nav-tab <?php 
        echo esc_html( implode( ' ', $classes ) );
        ?>" data-target="general" href="<?php 
        echo esc_url( $url );
        ?>">
			<?php 
        echo esc_html( $menu );
        ?>
		</a>
		<?php 
    }
}

/**
 * Display tab-wise content in admin settings page.
 */
function aiknowledgebase_settings_display_section() {
    global $aiknowledgebase__;
    $tab = $aiknowledgebase__['settings_tab'];
    $path = AIKNOWLEDGEBASE_PATH . 'templates/admin/template-parts/' . $tab . '.php';
    if ( file_exists( $path ) ) {
        include $path;
    }
    // for adding extra admin settings.
    do_action( 'aiknowledgebasea_extra_section' );
}

/**
 * Which settings to load
 *
 * @param string $section | settings section.
 * @param string $tab | subsection of the given $section.
 */
function aiknowledgebase_load_settings_template(  $section, $tab  ) {
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }
    // show error/update messages.
    settings_errors( 'wporg_messages' );
    // if GET parameter given as custom tab, use that.
    if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['nonce'] ) ), 'aiknowledgebase_tab_nonce' ) ) {
        // if aiknowledgebase_tab exists | nav-tab-active.
        if ( isset( $_GET['tab'] ) && !empty( sanitize_key( wp_unslash( $_GET['tab'] ) ) ) ) {
            $tab = sanitize_key( wp_unslash( $_GET['tab'] ) );
        }
    }
    global $aiknowledgebase__;
    // set current settings section and tab.
    $aiknowledgebase__['settings_section'] = $section;
    $aiknowledgebase__['settings_tab'] = $tab;
    include AIKNOWLEDGEBASE_PATH . 'templates/admin/settings.php';
}

/**
 * Top level menu callback function
 */
function aiknowledgebase_getting_started() {
    aiknowledgebase_load_settings_template( 'ai-knowledgebase', 'getting-started' );
}

/**
 * AI KnowledgeBase Content Generator page
 */
function aiknowledgebase_content_generator_page() {
    aiknowledgebase_load_settings_template( 'content-generator', 'content-generator' );
}

/**
 * AI KnowledgeBase Admin Settings page
 */
function aiknowledgebase_openai_settings_page() {
    aiknowledgebase_load_settings_template( 'openai-settings', 'openai-settings' );
}

/**
 * AI KnowledgeBase Assistant Settings page
 */
function aiknowledgebase_assistant_settings_page() {
    aiknowledgebase_load_settings_template( 'assistant-settings', 'assistant-settings' );
}

/**
 * OpenAI API Key
 */
function aiknowledgebase_api_key() {
    // Retrieve the API key from the options table.
    return get_option( 'aiknowledgebase_api_key' );
}

/**
 * OpenAI API Key input field
 */
function aiknowledgebase_api_field() {
    $api_key = aiknowledgebase_api_key();
    ?>
	<input name="api_key" type="text" id="api_key" value="<?php 
    echo esc_html( $api_key );
    ?>" class="regular-text" required>
    <?php 
    if ( !$api_key ) {
        ?>
			<p><?php 
        echo esc_html__( 'Get your ', "ai-knowledgebase" );
        ?><a href="<?php 
        echo esc_url( 'https://platform.openai.com/account/api-keys' );
        ?>"><?php 
        echo esc_html__( 'OpenAI API Key' );
        ?></a></p>
    <?php 
    }
    ?>
	<?php 
}

function aiknowledgebase_create_data_file(  $post_type  ) {
    $posts_query = new WP_Query(array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    ));
    if ( $posts_query->have_posts() ) {
        $markdownContent = "";
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            $markdownContent .= "## " . get_the_title() . "\n\n";
            $markdownContent .= apply_filters( "the_content", get_the_content() ) . "\n\n";
        }
        wp_reset_postdata();
        $markdownFilename = 'assistant_training_file.md';
        file_put_contents( $markdownFilename, $markdownContent );
        return array(
            'tmp_name' => basename( $markdownFilename ),
            'type'     => 'text/markdown',
            'name'     => $markdownFilename,
        );
    } else {
        return 'No posts found';
    }
}

function aiknowledgebase_execute_curl(
    $url,
    $method,
    $headers,
    $post_fields = null,
    $post_fields_encode = false
) {
    $curl_request = curl_init();
    curl_setopt_array( $curl_request, array(
        CURLOPT_URL            => $url,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    ) );
    if ( $post_fields ) {
        curl_setopt_array( $curl_request, array(
            CURLOPT_POSTFIELDS => ( $post_fields_encode ? json_encode( $post_fields ) : $post_fields ),
        ) );
    }
    $curl_response = curl_exec( $curl_request );
    curl_close( $curl_request );
    return $curl_response;
}

function aiknowledgebase_generate_assistant(  $file, $model  ) {
    $api_key = aiknowledgebase_api_key();
    $file_upload_response = aiknowledgebase_openai_file_upload( $file, $api_key );
    $create_vector_store_response = aiknowledgebase_openai_create_vector_store( $file_upload_response->id, $api_key );
    $create_assistant_response = aiknowledgebase_openai_create_assistant( $create_vector_store_response->id, $model, $api_key );
    $assistant_details = array(
        'file_id'         => $file_upload_response->id,
        'file_name'       => $file_upload_response->filename,
        'vector_store_id' => $create_vector_store_response->id,
        'assistant_id'    => $create_assistant_response->id,
    );
    return array(
        'assistant_details' => $assistant_details,
        'success'           => add_option( 'aiknowledgebase_assistant_details', $assistant_details ),
    );
}

function aiknowledgebase_reset_assistant() {
    $delete_option_response = delete_option( 'aiknowledgebase_assistant_details' );
    return array(
        'delete_option' => $delete_option_response,
    );
}

function aiknowledgebase_openai_file_upload(  $file, $api_key  ) {
    $curl_file = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
    $url = 'https://api.openai.com/v1/files';
    $headers = array('Authorization: Bearer ' . $api_key);
    $post_fields = array(
        'purpose' => 'assistants',
        'file'    => $curl_file,
    );
    $response = aiknowledgebase_execute_curl(
        $url,
        'POST',
        $headers,
        $post_fields
    );
    return json_decode( $response );
}

function aiknowledgebase_openai_create_vector_store(  $file_id, $api_key  ) {
    $url = 'https://api.openai.com/v1/vector_stores';
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v2');
    $post_fields = array(
        'file_ids' => array($file_id),
        'name'     => 'AI KnowledgeBase - Vector Store',
    );
    $response = aiknowledgebase_execute_curl(
        $url,
        'POST',
        $headers,
        $post_fields,
        true
    );
    return json_decode( $response );
}

function aiknowledgebase_openai_create_assistant(  $vector_store_id, $model, $api_key  ) {
    $url = 'https://api.openai.com/v1/assistants';
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v2');
    $post_fields = array(
        'name'           => 'AI KnowledgeBase',
        'instructions'   => 'You are a helper bot that will always give an answer based on your general knowledge in case the information can\'t be found inside the document.',
        'model'          => $model,
        'tools'          => array(array(
            'type' => 'file_search',
        )),
        'tool_resources' => array(
            'file_search' => array(
                'vector_store_ids' => array($vector_store_id),
            ),
        ),
    );
    $response = aiknowledgebase_execute_curl(
        $url,
        'POST',
        $headers,
        $post_fields,
        true
    );
    return json_decode( $response );
}

function aiknowledgebase_openai_create_thread(  $api_key  ) {
    $url = 'https://api.openai.com/v1/threads';
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v2');
    $response = aiknowledgebase_execute_curl( $url, 'POST', $headers );
    return json_decode( $response );
}

function aiknowledgebase_send_message(  $content, $thread_id  ) {
    $api_key = aiknowledgebase_api_key();
    $assistant_details = get_option( 'aiknowledgebase_assistant_details' );
    aiknowledgebase_openai_add_message_to_thread( $thread_id, $content, $api_key );
    $run_thread_response = aiknowledgebase_openai_run_thread( $thread_id, $assistant_details['assistant_id'], $api_key );
    if ( $run_thread_response->status === 'failed' ) {
        $json = '{"run_thread_status":"' . $run_thread_response->status . '","error_code":"' . $run_thread_response->last_error->code . '","error_message":"' . $run_thread_response->last_error->message . '"}';
        return json_decode( $json );
    }
    $run_status = $run_thread_response->status;
    while ( $run_status === 'queued' || $run_status === 'in_progress' ) {
        $retrieve_run_response = aiknowledgebase_openai_retrieve_run( $thread_id, $run_thread_response->id, $api_key );
        $run_status = $retrieve_run_response->status;
        usleep( 1.2 * 1000000 );
    }
    if ( $retrieve_run_response->status === 'failed' ) {
        $json = '{"run_thread_status":"' . $retrieve_run_response->status . '","error_code":"' . $retrieve_run_response->last_error->code . '","error_message":"' . $retrieve_run_response->last_error->message . '"}';
        return json_decode( $json );
    }
    $retrieve_thread_message = aiknowledgebase_openai_retrieve_message( $thread_id, $run_thread_response->id, $api_key );
    return array(
        'assistant_id'     => $assistant_details['assistant_id'],
        'message_response' => $retrieve_thread_message,
        'token_usage'      => $retrieve_run_response->usage,
        'used_model'       => $retrieve_run_response->model,
    );
}

function aiknowledgebase_openai_add_message_to_thread(  $thread_id, $content, $api_key  ) {
    $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v2');
    $post_fields = array(
        'role'    => 'user',
        'content' => $content,
    );
    $response = aiknowledgebase_execute_curl(
        $url,
        'POST',
        $headers,
        $post_fields,
        true
    );
    return json_decode( $response );
}

function aiknowledgebase_openai_run_thread(  $thread_id, $assistant_id, $api_key  ) {
    $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v2');
    $post_fields = array(
        'assistant_id' => $assistant_id,
    );
    $response = aiknowledgebase_execute_curl(
        $url,
        'POST',
        $headers,
        $post_fields,
        true
    );
    return json_decode( $response );
}

function aiknowledgebase_openai_retrieve_run(  $thread_id, $run_id, $api_key  ) {
    $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id;
    $headers = array('Authorization: Bearer ' . $api_key, 'OpenAI-Beta: assistants=v2');
    $response = aiknowledgebase_execute_curl( $url, 'GET', $headers );
    return json_decode( $response );
}

function aiknowledgebase_openai_retrieve_message(  $thread_id, $run_id, $api_key  ) {
    $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages?run_id=' . $run_id;
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v2');
    $response = aiknowledgebase_execute_curl( $url, 'GET', $headers );
    return json_decode( $response );
}

function aiknowledgebase_openai_delete_thread(  $thread_id, $api_key  ) {
    $url = 'https://api.openai.com/v1/threads/' . $thread_id;
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v1');
    $response = aiknowledgebase_execute_curl( $url, 'DELETE', $headers );
    return json_decode( $response );
}

function aiknowledgebase_openai_delete_assistant(  $assistant_id, $api_key  ) {
    $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;
    $headers = array('Authorization: Bearer ' . $api_key, 'Content-Type: application/json', 'OpenAI-Beta: assistants=v1');
    $response = aiknowledgebase_execute_curl( $url, 'DELETE', $headers );
    return json_decode( $response );
}

function aiknowledgebase_openai_delete_file(  $file_id, $api_key  ) {
    $url = 'https://api.openai.com/v1/files/' . $file_id;
    $headers = array('Authorization: Bearer ' . $api_key);
    $response = aiknowledgebase_execute_curl( $url, 'DELETE', $headers );
    return json_decode( $response );
}
