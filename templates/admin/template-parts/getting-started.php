<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
$openai_settings_tab_url = admin_url( 'admin.php?page=aiknowledgebase-openai-settings&tab=openai-settings&nonce=' . wp_create_nonce( 'aiknowledgebase_tab_nonce' ) );
$assistant_settings_tab_url = admin_url( 'admin.php?page=aiknowledgebase-assistant-settings' );
$content_generator_tab_url = admin_url( 'admin.php?page=aiknowledgebase-content-generator' );
?>
<div class="wrap">
	<h3><b><?php 
echo esc_html__( 'Getting Started', "ai-knowledgebase" );
?></b></h3>
	<ol>
		<li><?php 
echo esc_html__( 'Set up the ', "ai-knowledgebase" );
?><b><a href="<?php 
echo esc_url( $openai_settings_tab_url );
?>"><?php 
echo esc_html__( 'API Key', "ai-knowledgebase" );
?></a></b><?php 
echo esc_html__( ' in order to interact with the OpenAI API.', "ai-knowledgebase" );
?></li>
		<li><?php 
echo esc_html__( 'Go to the ', "ai-knowledgebase" );
?><b><a href="<?php 
echo esc_url( $assistant_settings_tab_url );
?>"><?php 
echo esc_html__( 'Assistant Settings', "ai-knowledgebase" );
?></a></b><?php 
echo esc_html__( ' tab, select a training method &amp; model for your assistant and then generate it.', "ai-knowledgebase" );
?></li>
		<li><?php 
echo esc_html__( 'You can now make use of the ', "ai-knowledgebase" );
?><b><?php 
echo esc_html__( 'AI Search Shortcode', "ai-knowledgebase" );
?></b><?php 
echo esc_html__( ' or visit the ', "ai-knowledgebase" );
?><b><a href="<?php 
echo esc_url( $content_generator_tab_url );
?>"><?php 
echo esc_html__( 'Content Generator', "ai-knowledgebase" );
?></a></b><?php 
echo esc_html__( ' tab of the plugin to ask your newly trained AI about anything.', "ai-knowledgebase" );
?></li>
	</ol>

	<br />
	<h3><b><?php 
echo esc_html__( 'AI Search Shortcode', "ai-knowledgebase" );
?></b></h3>
	<p><b><?php 
echo esc_html__( 'Usage:', "ai-knowledgebase" );
?></b><?php 
echo esc_html__( ' The shortcode can be added in any page, post or editor.', "ai-knowledgebase" );
?></p>
	<p><b><input type="text" readonly value="<?php 
echo esc_attr( '&#91;aiknowledgebase_chat_session&#93;' );
?>"></b></p>
	<?php 
?>

	<?php 
?>

	<br />
	<h3><b><?php 
echo esc_html__( 'Documentation &amp; Support', "ai-knowledgebase" );
?></b></h3>
	<p>
		<?php 
echo esc_html__( 'You can open a support ticket at ', "ai-knowledgebase" );
?><b><a href="<?php 
echo esc_url( 'https://modeltheme.ticksy.com/' );
?>" target="_blank"><?php 
echo esc_url( 'https://modeltheme.ticksy.com/' );
?></a></b>
		<?php 
echo esc_html__( 'or read our ', "ai-knowledgebase" );
?><b><a href="<?php 
echo esc_url( 'https://docs.modeltheme.com/plugins/woocommerce-product-campaign-notes' );
?>" target="_blank"><?php 
echo esc_html__( 'Online Documentation', "ai-knowledgebase" );
?></a></b>
		<?php 
echo esc_html__( 'We recommend you to open a support ticket from our support page for awesome &amp; nice support experience.', "ai-knowledgebase" );
?>
	</p>
</div>
