<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
global $aiknowledgebase__;
$screen_array = array(
    'toplevel_page_ai-knowledgebase',
    'ai-knowledgebase_page_aiknowledgebase-content-generator',
    'ai-knowledgebase_page_aiknowledgebase-openai-settings',
    'ai-knowledgebase_page_aiknowledgebase-assistant-settings'
);
$aiknowledgebase__ = array(
    'plugin'      => array(
        'name'            => 'AI KnowledgeBase',
        'version'         => '1.0.0',
        'page_limit'      => 10,
        'screen'          => $screen_array,
        'notice_interval' => 15,
    ),
    'notice'      => array(),
    'fields_list' => array(),
);
$menu_array = array();
$menu_array_tabs = array(
    esc_html__( 'Getting Started', "ai-knowledgebase" ),
    esc_html__( 'Content Generator', "ai-knowledgebase" ),
    esc_html__( 'OpenAI Settings', "ai-knowledgebase" ),
    esc_html__( 'Assistant Settings', "ai-knowledgebase" )
);
$menu_array['ai-knowledgebase'] = $menu_array_tabs;
$menu_array['content-generator'] = $menu_array_tabs;
$menu_array['openai-settings'] = $menu_array_tabs;
$menu_array['assistant-settings'] = $menu_array_tabs;
// menu items.
$aiknowledgebase__['menu'] = $menu_array;
// hook to modify global $aiknowledgebase__ data variable.
do_action( 'aiknowledgebase_modify_core_data' );