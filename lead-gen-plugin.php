<?php
	/**
	 * Plugin Name:     Lead Gen Plugin
	 * Version:         0.1.0
	 * @package         Lead_Gen_Plugin
	 */
	require('post-types/customer.php');


	add_action('wp_ajax_save_customer', 'customer_save_post');
	add_action('wp_ajax_nopriv_save_customer', 'customer_save_post');
	add_action('save_post_customer', 'save_customer_fields');
	add_action('add_meta_boxes', function () {
		add_meta_box('customer_fields', __('Customer Fields'), 'customer_fields_callback', 'customer');

	});

	add_shortcode('lead-gen', function ($atts) {

		if (defined('REST_REQUEST'))
			return;

		$nameLabel = isset($atts["name-label"]) && $atts["name-label"] ? $atts["name-label"] : "Full name";
		$nameMax = isset($atts["name-max"]) && $atts["name-max"] ? $atts["name-max"] : "50";
		$phoneLabel = isset($atts["phone-label"]) && $atts["phone-label"] ? $atts["phone-label"] : "Phone name";
		$phoneMax = isset($atts["phone-max"]) && $atts["phone-max"] ? $atts["phone-max"] : "12";
		$budgetLabel = isset($atts["budget-label"]) && $atts["budget-label"] ? $atts["budget-label"] : "Desired Budget";
		$budgetMax = isset($atts["budget-max"]) && $atts["budget-max"] ? $atts["budget-max"] : "200";
		$messageLabel = isset($atts["message-label"]) && $atts["message-label"] ? $atts["message-label"] : "Message";
		$rows = isset($atts["message-rows"]) && $atts["message-rows"] ? $atts["message-rows"] : "4";
		$cols = isset($atts["message-cols"]) && $atts["message-cols"] ? $atts["message-cols"] : "50";

		$form = "<form class='w-full max-w-sm customer-form' method='post'>
	               	%s
					<input type='hidden' name='action' value='save_customer' />

					<div class='flex items-center border-b border-teal-500 py-4'>
						<input  name='name'  required class='appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none' maxlength='%s' type='text' placeholder='%s'>
					</div>

					<div class='flex items-center border-b border-teal-500 py-4'>
						<input name='phone' required  class='appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none' maxlength='%s' type='phone' placeholder='%s' />
					</div>

					<div class='flex items-center border-b border-teal-500 py-4'>
						<input name='budget' required class='appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none' type='text'  maxlength='%s' placeholder='%s' />
					</div>

					<div class='flex items-center border-b border-teal-500 py-4'>
						<textarea name='message' class='appearance-none bg-transparent border-none  text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none'  rows='%s' cols='%s' placeholder='%s' ></textarea>
					</div>

					<button class='mt-4  bg-gray-500 hover:bg-gray-700   text-base  text-white py-1 px-4 rounded' type=\"submit\">
						Submit
					</button>
				</form>";


		$html = sprintf($form, wp_nonce_field('save_customer', 'customer'), $nameMax, $nameLabel, $phoneMax, $phoneLabel, $budgetMax, $budgetLabel, $rows, $cols, $messageLabel);
		return $html;
	});
	add_action('wp_enqueue_scripts', function () {
		wp_enqueue_script('tailwind', 'https://unpkg.com/tailwindcss-jit-cdn', array(), null, true);
		wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.6.4.slim.min.js', array(), null, true);
		wp_enqueue_script('customer', plugin_dir_url(__FILE__) . 'js/customer.js', array(), null, true);
		wp_localize_script('customer', 'customer', array('ajax_url' => admin_url('admin-ajax.php')));
	});
	
	/**
	 * Displays the customer data and form for admin
	 *
	 * @param object $post Object containing the current customer.
	 */
	function customer_fields_callback($post)
	{
		wp_nonce_field('save_customer', 'customer');
		echo "<table>";
		echo '<tr><td><label >Name:</label></td>';
		echo '<td><input type="text" name="name" value="' . esc_attr(get_post_meta($post->ID, 'name', true)) . '"></td></tr>';
		echo '<tr><td><label >Phone:</label></td>';
		echo '<td><input type="text"  name="phone" value="' . esc_attr(get_post_meta($post->ID, 'phone', true)) . '"></td></tr>';
		echo '<tr><td><label>Budget:</label></td>';
		echo '<td><input type="text" id="name" name="budget" value="' . esc_attr(get_post_meta($post->ID, 'budget', true)) . '"></td></tr>';
		echo '<tr><td><label>Message:</label></td>';
		echo '<td><textarea type="text" id="name" name="message">' . esc_attr(get_post_meta($post->ID, 'message', true)) . '</textarea></td></tr>';
		echo "</table>";
	}

	/**
	 * Save the customer custom fields
	 *
	 * @param INT $post_id ID of the customer for saving custom fields.
	 */
	function save_customer_fields($post_id)
	{
		if (!isset($_POST['customer']) || !wp_verify_nonce($_POST['customer'], 'save_customer'))
			return $post_id;

		if (isset($_POST['name']))
			update_post_meta($post_id, 'name', sanitize_text_field($_POST['name']));
		if (isset($_POST['phone']))
			update_post_meta($post_id, 'phone', sanitize_text_field($_POST['phone']));
		if (isset($_POST['budget']))
			update_post_meta($post_id, 'budget', sanitize_text_field($_POST['budget']));
		if (isset($_POST['message']))
			update_post_meta($post_id, 'message', sanitize_text_field($_POST['message']));

	}

	/**
	 * Save the customer as customer post type
	 *
	 */
	function customer_save_post()
	{

		if (!wp_verify_nonce($_POST['customer'], 'save_customer'))
			wp_send_json_error(array('message' => "Error"));

		$post_data = array('post_title' => sanitize_text_field($_POST['name']), 'post_type' => 'customer', 'post_status' => 'private',);
		$post_id = wp_insert_post($post_data);
		if (!is_wp_error($post_id)) {
			wp_send_json_success(array('post_id' => $post_id));
		} else {
			wp_send_json_error(array('message' => $post_id->get_error_message()));
		}
	}

