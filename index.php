<?php
/**
 * Plugin Name: OTGS Foo User Fields
 * Description: Allows admin to dynamically add custom text fields to user profiles, displayed automatically on author pages.
 * Version: 1.0.0
 * Author: OnTheGoSystems
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function() {
	$classes = [
		OTGS\FooUserFields\ManageFieldsScreen::class,
		OTGS\FooUserFields\UserProfileScreen::class,
		OTGS\FooUserFields\FrontendAuthorPage::class,
	];

	foreach ( $classes as $class ) {
		require_once( __DIR__ . '/classes/' . str_replace( 'OTGS\FooUserFields\\', '', $class ) . '.php' );
		( new $class )->loadActionsAndFilters();
	}
} );
