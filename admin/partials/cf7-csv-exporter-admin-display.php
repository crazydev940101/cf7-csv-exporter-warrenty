<?php



/**

 * Provide a admin area view for the plugin

 *

 * This file is used to markup the admin-facing aspects of the plugin.

 *

 * @link       https://ieproductions.com

 * @since      1.0.0

 *

 * @package    Cf7_Csv_Exporter

 * @subpackage Cf7_Csv_Exporter/admin/partials

 */



 

function cf7_csv_exporter_admin_register() {   

    // Add action for handling Location ID

    add_action( "admin_init", 'save_field_callback' );     

    add_settings_section('custom_admin_settings_section', '','section_callback', 'cf7_csv_exporter_admin_menu');

    add_settings_field('custom_input_field', '', 'input_field_callback', 'cf7_csv_exporter_admin_menu', 'custom_admin_settings_section');

}



function section_callback() {



    /**

    * Show section part

    */



    echo '<h2>SFTP Info for CSV exporter</h2>';

}



function input_field_callback() {



    /**

    * Show contact form id

    */



 
    $option_name_sftp_host = 'sftp_host_for_csv_exporter';



    $option_name_sftp_port = 'sftp_port_for_csv_exporter';



    $option_name_sftp_user = 'sftp_user_for_csv_exporter';



    $option_name_sftp_pass = 'sftp_pass_for_csv_exporter';




    $value_sftp_host = get_option($option_name_sftp_host);



    $value_sftp_port = get_option($option_name_sftp_port);



    $value_sftp_user = get_option($option_name_sftp_user);



    $value_sftp_pass = get_option($option_name_sftp_pass);



    echo '<label>SFTP Host: </label>';

    echo '<input type="text" id="sftp_host_for_csv_exporter" name="' . $option_name_sftp_host . '" value="' . esc_attr($value_sftp_host) . '" />';



    echo '<br/><br/>';



    echo '<label>SFTP Port: </label>';

    echo '<input type="text" id="sftp_port_for_csv_exporter" name="' . $option_name_sftp_port . '" value="' . esc_attr($value_sftp_port) . '" />';



    echo '<br/><br/>';



    echo '<label>SFTP Username: </label>';

    echo '<input type="text" id="sftp_user_for_csv_exporter" name="' . $option_name_sftp_user . '" value="' . esc_attr($value_sftp_user) . '" />';



    echo '<br/><br/>';



    echo '<label>SFTP Password: </label>';

    echo '<input type="password" id="sftp_pass_for_csv_exporter" name="' . $option_name_sftp_pass . '" value="' . esc_attr($value_sftp_pass) . '" />';

}



function save_field_callback() {



    /**

    * Save the contact form id

    */



    $option_name_sftp_host = 'sftp_host_for_csv_exporter';



    $option_name_sftp_port = 'sftp_port_for_csv_exporter';



    $option_name_sftp_user = 'sftp_user_for_csv_exporter';



    $option_name_sftp_pass = 'sftp_pass_for_csv_exporter';




    if (isset($_POST[$option_name_sftp_host])) {

        update_option($option_name_sftp_host, sanitize_text_field($_POST[$option_name_sftp_host]));

    }



    if (isset($_POST[$option_name_sftp_port])) {

        update_option($option_name_sftp_port, sanitize_text_field($_POST[$option_name_sftp_port]));

    }



    if (isset($_POST[$option_name_sftp_user])) {

        update_option($option_name_sftp_user, sanitize_text_field($_POST[$option_name_sftp_user]));

    }



    if (isset($_POST[$option_name_sftp_pass])) {

        update_option($option_name_sftp_pass, sanitize_text_field($_POST[$option_name_sftp_pass]));

    }



}



function cf7_csv_exporter_admin_page() {



    /**

    * Register it into the admin page.

    */



    ?>



        <form method="post" action="options.php" class="cf7-csv-exporter-admin-form">

            <?php                

            cf7_csv_exporter_admin_register();

            settings_fields('cf7_csv_exporter_admin_menu');

            do_settings_sections('cf7_csv_exporter_admin_menu');

            submit_button('Save Settings');

            ?>

        </form>

    <?php

}



?>



<!-- This file should primarily consist of HTML with a little bit of PHP. -->

