<?php

/**
 * Registers the `customer` post type.
 */
function customer_init() {
	register_post_type(
		'customer',
		[
			'labels'                => [
				'name'                  => __( 'Customers', 'lead-gen-plugin' ),
				'singular_name'         => __( 'Customer', 'lead-gen-plugin' ),
				'all_items'             => __( 'All Customers', 'lead-gen-plugin' ),
				'archives'              => __( 'Customer Archives', 'lead-gen-plugin' ),
				'attributes'            => __( 'Customer Attributes', 'lead-gen-plugin' ),
				'insert_into_item'      => __( 'Insert into Customer', 'lead-gen-plugin' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Customer', 'lead-gen-plugin' ),
				'featured_image'        => _x( 'Featured Image', 'customer', 'lead-gen-plugin' ),
				'set_featured_image'    => _x( 'Set featured image', 'customer', 'lead-gen-plugin' ),
				'remove_featured_image' => _x( 'Remove featured image', 'customer', 'lead-gen-plugin' ),
				'use_featured_image'    => _x( 'Use as featured image', 'customer', 'lead-gen-plugin' ),
				'filter_items_list'     => __( 'Filter Customers list', 'lead-gen-plugin' ),
				'items_list_navigation' => __( 'Customers list navigation', 'lead-gen-plugin' ),
				'items_list'            => __( 'Customers list', 'lead-gen-plugin' ),
				'new_item'              => __( 'New Customer', 'lead-gen-plugin' ),
				'add_new'               => __( 'Add New', 'lead-gen-plugin' ),
				'add_new_item'          => __( 'Add New Customer', 'lead-gen-plugin' ),
				'edit_item'             => __( 'Edit Customer', 'lead-gen-plugin' ),
				'view_item'             => __( 'View Customer', 'lead-gen-plugin' ),
				'view_items'            => __( 'View Customers', 'lead-gen-plugin' ),
				'search_items'          => __( 'Search Customers', 'lead-gen-plugin' ),
				'not_found'             => __( 'No Customers found', 'lead-gen-plugin' ),
				'not_found_in_trash'    => __( 'No Customers found in trash', 'lead-gen-plugin' ),
				'parent_item_colon'     => __( 'Parent Customer:', 'lead-gen-plugin' ),
				'menu_name'             => __( 'Customers', 'lead-gen-plugin' ),
			],
			'public'                => false,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => [ 'title', 'editor' ],
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-admin-users',
			'show_in_rest'          => true,
			'rest_base'             => 'customer',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'publicly_queryable'  => false,
			'taxonomies'         => array( 'category', 'post_tag' ),


		]
	);

}

add_action( 'init', 'customer_init' );

/**
 * Sets the post updated messages for the `customer` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `customer` post type.
 */
function customer_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['customer'] = [
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Customer updated. <a target="_blank" href="%s">View Customer</a>', 'lead-gen-plugin' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'lead-gen-plugin' ),
		3  => __( 'Custom field deleted.', 'lead-gen-plugin' ),
		4  => __( 'Customer updated.', 'lead-gen-plugin' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Customer restored to revision from %s', 'lead-gen-plugin' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Customer published. <a href="%s">View Customer</a>', 'lead-gen-plugin' ), esc_url( $permalink ) ),
		7  => __( 'Customer saved.', 'lead-gen-plugin' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Customer submitted. <a target="_blank" href="%s">Preview Customer</a>', 'lead-gen-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Customer scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Customer</a>', 'lead-gen-plugin' ), date_i18n( __( 'M j, Y @ G:i', 'lead-gen-plugin' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Customer draft updated. <a target="_blank" href="%s">Preview Customer</a>', 'lead-gen-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	];

	return $messages;
}

add_filter( 'post_updated_messages', 'customer_updated_messages' );

/**
 * Sets the bulk post updated messages for the `customer` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `customer` post type.
 */
function customer_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages['customer'] = [
		/* translators: %s: Number of Customers. */
		'updated'   => _n( '%s Customer updated.', '%s Customers updated.', $bulk_counts['updated'], 'lead-gen-plugin' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Customer not updated, somebody is editing it.', 'lead-gen-plugin' ) :
						/* translators: %s: Number of Customers. */
						_n( '%s Customer not updated, somebody is editing it.', '%s Customers not updated, somebody is editing them.', $bulk_counts['locked'], 'lead-gen-plugin' ),
		/* translators: %s: Number of Customers. */
		'deleted'   => _n( '%s Customer permanently deleted.', '%s Customers permanently deleted.', $bulk_counts['deleted'], 'lead-gen-plugin' ),
		/* translators: %s: Number of Customers. */
		'trashed'   => _n( '%s Customer moved to the Trash.', '%s Customers moved to the Trash.', $bulk_counts['trashed'], 'lead-gen-plugin' ),
		/* translators: %s: Number of Customers. */
		'untrashed' => _n( '%s Customer restored from the Trash.', '%s Customers restored from the Trash.', $bulk_counts['untrashed'], 'lead-gen-plugin' ),
	];

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', 'customer_bulk_updated_messages', 10, 2 );
