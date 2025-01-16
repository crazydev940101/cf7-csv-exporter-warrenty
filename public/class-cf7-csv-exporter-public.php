<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ieproductions.com
 * @since      1.0.0
 *
 * @package    Cf7_Csv_Exporter
 * @subpackage Cf7_Csv_Exporter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cf7_Csv_Exporter
 * @subpackage Cf7_Csv_Exporter/public
 * @author     Ariel Cruz <ariel@ieproductions.com>
 */

use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\RSA;

class Cf7_Csv_Exporter_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_Csv_Exporter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_Csv_Exporter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-csv-exporter-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_Csv_Exporter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_Csv_Exporter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-csv-exporter-public.js', array( 'jquery' ), $this->version, false );

	}

	function process_warranty_info_from_plugin($data) {
		// Your existing code
			
		error_log(json_encode($data));
		
		$upload_dir = wp_upload_dir();
		$csv_dir = $upload_dir['basedir'] . '/cf7-w-submissions/';
	
		$csv_file = $csv_dir . 'warranty-submissions.csv';
	
		// Ensure the directory exists
		if (!file_exists($csv_dir)) {
			if (!wp_mkdir_p($csv_dir)) {
				error_log('Failed to create directory: ' . $csv_dir);
				return;
			}
		}
	
		// Open the file in append mode
		$file = fopen($csv_file, 'a');
		if ($file === false) {
			error_log('Failed to open file for writing');
			return;
		}

		// Set the timezone to Mountain Time
		date_default_timezone_set('America/Denver');
	
		// Add current date to data
		$data['Date'] = date('Y-m-d H:i:s'); // Include timestamp
	
		// Write the header row only if the file is new
		if (filesize($csv_file) === 0) {
			fputcsv($file, array_keys($data));
		}
	
		// Write the data row
		fputcsv($file, array_values($data));
	
		// Close the file
		fclose($file);
	
		$option_name_sftp_host = 'sftp_host_for_csv_exporter';
		$option_name_sftp_port = 'sftp_port_for_csv_exporter';
		$option_name_sftp_user = 'sftp_user_for_csv_exporter';
		$option_name_sftp_pass = 'sftp_pass_for_csv_exporter';

		$value_sftp_host = get_option($option_name_sftp_host);
		$value_sftp_port = get_option($option_name_sftp_port);
		$value_sftp_user = get_option($option_name_sftp_user);
		$value_sftp_pass = get_option($option_name_sftp_pass);

		// SFTP details
		$sftp_host = $value_sftp_host;
		$sftp_port = $value_sftp_port; // Default SFTP port
		$sftp_username = $value_sftp_user;
		$sftp_password = $value_sftp_pass;
		$remote_file = '/Import/' . basename($csv_file);
	
		// Create SFTP connection
		$sftp = new SFTP($sftp_host, $sftp_port);
		if (!$sftp->login($sftp_username, $sftp_password)) {
			error_log('SFTP login failed');
			return;
		}

		// Ensure the remote directory exists
		$remote_dir = dirname($remote_file);
		if (!$sftp->is_dir($remote_dir)) {
			if (!$sftp->mkdir($remote_dir, -1, true)) {
				error_log('Failed to create remote directory: ' . $remote_dir);
				return;
			}
		}
	
		// Upload file to SFTP server
		if ($sftp->put($remote_file, $csv_file, SFTP::SOURCE_LOCAL_FILE)) {
			error_log('Successfully uploaded ' . $csv_file . ' to ' . $remote_file);
		} else {
			error_log('Failed to upload ' . $csv_file . ' to ' . $remote_file);
		}
	}
	
	function cleanup_csv_files() {

		// Get the upload directory
		$upload_dir = wp_upload_dir();
		$csv_dir = $upload_dir['basedir'] . '/cf7-w-submissions/';
	
		// Delete local CSV files
		if (file_exists($csv_dir)) {
			$files = glob($csv_dir . '*.csv');
			foreach ($files as $file) {
				unlink($file); // Delete the file
			}
		}
	
		// Add code to connect to SFTP and delete remote files
		$sftp_host = get_option('sftp_host_for_csv_exporter');
		$sftp_port = get_option('sftp_port_for_csv_exporter');
		$sftp_user = get_option('sftp_user_for_csv_exporter');
		$sftp_pass = get_option('sftp_pass_for_csv_exporter');
	
		// Connect to SFTP server
		$sftp = new SFTP($sftp_host, $sftp_port);
		if ($sftp->login($sftp_user, $sftp_pass)) {
			// Delete remote files
			$remote_dir = '/Import/';
			$remote_files = $sftp->nlist($remote_dir);
			foreach ($remote_files as $file) {
				if (strpos($file, '.csv') !== false) {
					$sftp->delete($remote_dir . $file);
				}
			}
		} else {
			error_log('SFTP login failed during cleanup');
		}
	}

}
