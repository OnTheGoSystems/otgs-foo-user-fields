<?php

namespace OTGS\FooUserFields;

class WPMLIntegration {

	const USER_PACKAGE_KIND_SLUG = 'otgs-foo-user-fields';
	const USER_PACKAGE_KIND_TITLE = 'OTGS Foo User Fields';

	public function loadActionsAndFilters() {
		// Declare our package kind.
		add_filter( 'wpml_active_string_package_kinds', [ $this, 'declareUserFieldsPackageKind' ] );

		// User fields life cycle management.
		add_action( 'otgs_foo_user_fields_user_save_start', [ $this, 'onUserSaveStart' ] );
		add_action( 'otgs_foo_user_fields_user_save_field', [ $this, 'onUserSaveField' ], 10, 3 );
		add_action( 'otgs_foo_user_fields_user_save_end', [ $this, 'onUserSaveEnd' ] );
		add_action( 'deleted_user', [ $this, 'onUserDeleted' ] );

		// Display user fields.
		add_filter( 'otgs_foo_user_fields_display_value', [ $this, 'translateUserFieldDisplayValue' ], 10, 3 );
	}

	/**
	 * @param array $kinds
	 *
	 * @return array
	 */
	public function declareUserFieldsPackageKind( $kinds ) {
		$kinds[ self::USER_PACKAGE_KIND_SLUG ] = [
			'slug'   => self::USER_PACKAGE_KIND_SLUG,
			'title'  => self::USER_PACKAGE_KIND_TITLE,
			'plural' => self::USER_PACKAGE_KIND_TITLE,
		];

		return $kinds;
	}

	/**
	 * @param int $userId
	 *
	 * @return void
	 */
	public function onUserSaveStart( $userId ) {
		do_action( 'wpml_start_string_package_registration', $this->getUserFieldPackage( $userId ) );
	}

	/**
	 * @param string $fieldValue
	 * @param string $fieldLabel
	 * @param int    $userId
	 *
	 * @return void
	 */
	public function onUserSaveField( $fieldValue, $fieldLabel, $userId ) {
		do_action( 'wpml_register_string', $fieldValue, sanitize_key( $fieldLabel ), $this->getUserFieldPackage( $userId ), $fieldLabel, 'LINE' );
	}

	/**
	 * @param int $userId
	 *
	 * @return void
	 */
	public function onUserSaveEnd( $userId ) {
		do_action( 'wpml_delete_unused_package_strings', $this->getUserFieldPackage( $userId ) );
	}

	/**
	 * @param int $userId
	 *
	 * @return void
	 */
	public function onUserDeleted( $userId ) {
		do_action( 'wpml_delete_package', $userId, self::USER_PACKAGE_KIND_TITLE );
	}

	/**
	 * @param string $fieldValue
	 * @param int    $userId
	 *
	 * @return string
	 */
	public function translateUserFieldDisplayValue( $fieldValue, $fieldLabel, $userId ) {
		return apply_filters( 'wpml_translate_string', $fieldValue, sanitize_key( $fieldLabel ), $this->getUserFieldPackage( $userId ) );
	}

	/**
	 * @param int $userId
	 *
	 * @return array
	 */
	private function getUserFieldPackage( $userId ) {
		$user = get_user_by( 'id', $userId );

		return [
			'kind_slug' => self::USER_PACKAGE_KIND_SLUG,
			'kind'      => self::USER_PACKAGE_KIND_TITLE,
			'name'      => $userId,
			'title'     => $user->display_name ?? "User #$userId",
		];
	}
}
