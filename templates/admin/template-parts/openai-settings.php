<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="wrap">
	<form method="post" action="">
		<?php wp_nonce_field( 'update_settings', 'settings_nonce' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="api_key"><?php echo esc_html__('OpenAI API Key'); ?></label></th>
				<td>
					<?php aiknowledgebase_api_field(); ?>
				</td>
			</tr>
		</table>
		<?php submit_button( esc_html__('Save', "ai-knowledgebase") ); ?>
	</form>
</div>
