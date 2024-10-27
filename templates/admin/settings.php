<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="aiknowledgebase-admin-wrap aiknowledgebase-settings">
	<div class="aiknowledgebase-heading">
		<?php 
aiknowledgebase_settings_display_title();
?>
	</div>
	<div class="aiknowledgebase-notice">
		<?php 
aiknowledgebase_settings_display_saved_notices();
?>
	</div>
	<div class="aiknowledgebase-settings">
		<div class="aiknowledgebase-col-12" id="aiknowledgebase-main">
			<div class="aiknowledgebase-row">
				<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
					<?php 
aiknowledgebase_settings_menu();
?>
				</nav>
			</div>
			<div class="aiknowledgebase-row sections">
				<?php 
aiknowledgebase_settings_display_section();
?>
			</div>
		</div>
	</div>
</div>
