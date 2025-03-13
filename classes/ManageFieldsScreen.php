<?php

namespace OTGS\FooUserFields;

class ManageFieldsScreen {

	const OPTION_NAME = 'otgs_foo_user_fields';
	const NONCE_NAME  = 'save_custom_fields';
	const LABELS_USER_ID = '-- Labels --'; // Using a non-existing user ID to use our existing package kind to translate the field labels.

	public function loadActionsAndFilters() {
		add_action( 'admin_menu', function () {
			add_users_page(
				'OTGS Foo User Fields',
				'User Fields',
				'manage_options',
				'otgs-foo-user-fields',
				[ $this, 'manageUserFields' ]
			);
		} );
	}

	public function manageUserFields() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['fields'] ) && check_admin_referer( self::NONCE_NAME ) ) {
			$fields = array_map( 'sanitize_text_field', explode( PHP_EOL, trim( $_POST['fields'] ) ) );
			update_option( self::OPTION_NAME, $fields );

			do_action( 'otgs_foo_user_fields_user_save_start', self::LABELS_USER_ID );

			foreach ( $fields as $fieldLabel ) {
				do_action( 'otgs_foo_user_fields_user_save_field', $fieldLabel, $fieldLabel, self::LABELS_USER_ID );
			}

			do_action( 'otgs_foo_user_fields_user_save_end', self::LABELS_USER_ID );

			echo '<div class="updated"><p>' . esc_html__( 'Fields updated.', 'otgs-foo-user-fields' ) . '</p></div>';
		}

		$fields = get_option( self::OPTION_NAME, [] );

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'OTGS Foo User Fields', 'otgs-foo-user-fields' ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( self::NONCE_NAME ); ?>
				<textarea name="fields" rows="10" cols="50"
				          class="large-text"><?php echo esc_textarea( implode( PHP_EOL, $fields ) ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Enter one field per line.', 'otgs-foo-user-fields' ) ?></p>
				<p><input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Fields', 'otgs-foo-user-fields' ) ?>"></p>
			</form>
		</div>
		<?php
	}
}
