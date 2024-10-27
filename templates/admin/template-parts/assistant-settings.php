<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
$api_key = aiknowledgebase_api_key();
$url = admin_url( 'admin.php?page=aiknowledgebase-openai-settings&tab=openai-settings&nonce=' . wp_create_nonce( 'aiknowledgebase_tab_nonce' ) );
$openai_models = array('gpt-3.5-turbo-0125', 'gpt-4-turbo', 'gpt-4o');
$assistant_details = get_option( 'aiknowledgebase_assistant_details' );
$customize_assistant = get_option( "aiknowledgebase_customize_assistant" );
?>
<div class="wrap">
  <?php 
if ( !$api_key ) {
    ?>
    <h3>
      <?php 
    echo esc_html__( 'You\'ll need to set up the ', "ai-knowledgebase" );
    ?>
      <a href="<?php 
    echo esc_url( $url );
    ?>"><?php 
    echo esc_html__( 'OpenAI API Key' );
    ?></a>
      <?php 
    echo esc_html__( ' first.', "ai-knowledgebase" );
    ?>
    </h3>
  <?php 
}
?>
  <?php 
if ( $assistant_details && $api_key ) {
    ?>
    <h3>
      <?php 
    echo esc_html__( 'Assistant ', "ai-knowledgebase" );
    ?>
      <i><?php 
    echo esc_html( $assistant_details['assistant_id'] );
    ?></i>
      <?php 
    echo esc_html__( ' already generated with file ', "ai-knowledgebase" );
    ?>
      <a href="<?php 
    echo esc_url( $assistant_details['file_url'] );
    ?>" target="_blank"><i><?php 
    echo esc_html( $assistant_details['file_name'] );
    ?></i></a>
      <i>( <?php 
    echo esc_html__( 'OpenAI File ID:', "ai-knowledgebase" );
    ?> <?php 
    echo esc_html( $assistant_details['file_id'] );
    ?> )</i>
    </h3>
    <form id="reset_assistant_form" method="POST" data-nonce="<?php 
    echo esc_attr( wp_create_nonce( 'wp_rest' ) );
    ?>" data-rest-location="<?php 
    echo esc_url( get_rest_url() );
    ?>">
      <?php 
    submit_button(
        esc_html__( 'Reset OpenAI Assistant', "ai-knowledgebase" ),
        'large',
        'reset_assistant',
        false
    );
    ?>
    </form>
  <?php 
} else {
    if ( !$assistant_details && $api_key ) {
        ?>
    <form id="assistant_file_form" method="post" data-nonce="<?php 
        echo esc_attr( wp_create_nonce( 'wp_rest' ) );
        ?>" data-rest-location="<?php 
        echo esc_url( get_rest_url() );
        ?>">
      <?php 
        wp_nonce_field( 'update_assistant_settings', 'assistant_settings_nonce' );
        ?>
      <table class="form-table">
        <tr>
          <th scope="row">
            <label for="assistant_training_type"><?php 
        echo esc_html__( 'Assistant Training Type', "ai-knowledgebase" );
        ?></label>
          </th>
          <td>
            <select name="assistant_training_type" id="assistant_training_type" required <?php 
        if ( !$api_key ) {
            echo esc_attr( "disabled" );
        }
        ?>>
              <option value="" selected><?php 
        echo esc_html__( 'Select Training Type ...', "ai-knowledgebase" );
        ?></option>
              <hr />
              <option value="file_upload"><?php 
        echo esc_html__( 'File Upload', "ai-knowledgebase" );
        ?></option>
              <?php 
        ?>
            </select>
          </td>
        </tr>
        <tr id="assistant_file_wrapper">
          <th scope="row">
            <label for="assistant_file"><?php 
        echo esc_html__( 'Assistant File', "ai-knowledgebase" );
        ?></label>
          </th>
          <td>
            <input name="assistant_file" type="file" id="assistant_file" required <?php 
        if ( !$api_key ) {
            echo esc_attr( "disabled" );
        }
        ?>>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="assistant_model"><?php 
        echo esc_html__( 'Assistant Model', "ai-knowledgebase" );
        ?></label>
          </th>
          <td>
            <select name="assistant_model" id="assistant_model" required <?php 
        if ( !$api_key ) {
            echo esc_attr( "disabled" );
        }
        ?>>
              <?php 
        foreach ( $openai_models as $model ) {
            ?>
                <option value="<?php 
            echo esc_attr( $model );
            ?>"><?php 
            echo esc_html( $model );
            ?></option>
              <?php 
        }
        ?>
            </select>
          </td>
        </tr>
      </table>
      <?php 
        if ( $api_key ) {
            ?>
        <?php 
            submit_button(
                esc_html__( 'Generate OpenAI Assistant', "ai-knowledgebase" ),
                'primary',
                'submit_assistant_settings',
                false
            );
            ?>
      <?php 
        }
        ?>
    </form>
  <?php 
    }
}
?>
  <br />
  <hr />
  <br />
  <form id="assistant_customize_form" method="POST" data-nonce="<?php 
echo esc_attr( wp_create_nonce( 'wp_rest' ) );
?>" data-rest-location="<?php 
echo esc_url( get_rest_url() );
?>">
    <?php 
wp_nonce_field( 'customize_assistant', 'customize_assistant_nonce' );
?>
    <h3><?php 
echo esc_html__( 'Customize Assistant', 'ai-knowledgebase' );
?></h3>
    <div class="assistant_customize_fields">
      <label for="assistant_shortcode_input_placeholder"><?php 
echo esc_html__( 'Shortcode Input Placeholder', 'ai-knowledgebase' );
?></label>
      <input
        id="assistant_shortcode_input_placeholder"
        name="assistant_shortcode_input_placeholder"
        class="customize-assistant-input"
        value="<?php 
echo ( $customize_assistant && isset( $customize_assistant["shortcode_input_placeholder"] ) ? esc_html( $customize_assistant["shortcode_input_placeholder"] ) : '' );
?>"
        placeholder="<?php 
echo esc_html__( "Default: Type your message here ...", "ai-knowledgebase" );
?>"
      >

      <label for="assistant_shortcode_submit_button"><?php 
echo esc_html__( 'Shortcode Submit Button', 'ai-knowledgebase' );
?></label>
      <input
        id="assistant_shortcode_submit_button"
        name="assistant_shortcode_submit_button"
        class="customize-assistant-input"
        value="<?php 
echo ( $customize_assistant && isset( $customize_assistant["shortcode_submit_button"] ) ? esc_html( $customize_assistant["shortcode_submit_button"] ) : '' );
?>"
        placeholder="<?php 
echo esc_html__( "Default: Submit", "ai-knowledgebase" );
?>"
      >
      <?php 
?>
    </div>
    <?php 
submit_button(
    esc_html__( 'Save Changes' ),
    'primary',
    'submit_assistant_customize',
    false
);
?>
  </form>
</div>
