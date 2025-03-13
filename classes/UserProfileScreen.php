<?php

namespace OTGS\FooUserFields;

class UserProfileScreen {

	const FIELD_PREFIX = 'otgs_foo_user_fields_';

	public function loadActionsAndFilters() {
		add_action( 'show_user_profile', [ $this, 'showFields' ] );
		add_action( 'edit_user_profile', [ $this, 'showFields' ] );

		add_action( 'personal_options_update', [ $this, 'saveFields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'saveFields' ] );
	}

	/**
	 * @param \WP_User $user
	 *
	 * @return void
	 */
	public function showFields( $user ) {
		$fields = get_option( ManageFieldsScreen::OPTION_NAME, [] );

		if ( ! $fields ) {
			return;
		}

		echo '<h3>' . esc_html__( 'OTGS Foo User Fields', 'otgs-foo-user-fields' ) . '</h3><table class="form-table">';

		foreach ( $fields as $fieldLabel ) {
			$key        = sanitize_key( $fieldLabel );
			$fieldValue = get_user_meta( $user->ID, $key, true );
			echo '<tr><th><label>' . esc_html( $fieldLabel ) . '</label></th><td>';
			echo '<input type="text" name="' . self::FIELD_PREFIX . $key . '" value="' . esc_attr( $fieldValue ) . '" class="regular-text"></td></tr>';
		}

		echo '</table>';
	}

	/**
	 * @param int $userId
	 *
	 * @return void
	 */
	public function saveFields( $userId ) {
		if ( ! current_user_can( 'edit_user', $userId ) ) {
			return;
		}

		$fields = get_option( ManageFieldsScreen::OPTION_NAME, [] );

		do_action( 'otgs_foo_user_fields_user_save_start', $userId );

		foreach ( $fields as $fieldLabel ) {
			$fieldKey = sanitize_key( $fieldLabel );

			if ( isset( $_POST[ self::FIELD_PREFIX . $fieldKey ] ) ) {
				$fieldValue = sanitize_text_field( $_POST[ self::FIELD_PREFIX . $fieldKey ] );
				update_user_meta( $userId, $fieldKey, $fieldValue );
				do_action( 'otgs_foo_user_fields_user_save_field', $fieldValue, $fieldLabel, $userId );
			}
		}

		do_action( 'otgs_foo_user_fields_user_save_end', $userId );
	}
}
