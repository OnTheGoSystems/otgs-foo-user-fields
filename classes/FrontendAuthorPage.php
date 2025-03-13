<?php

namespace OTGS\FooUserFields;

class FrontendAuthorPage {

	public function loadActionsAndFilters() {
		add_filter( 'the_content', [ $this, 'displayProfileFields' ] );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function displayProfileFields( $content ) {
		if ( is_author() ) {
			$author       = get_queried_object();
			$fields       = get_option( ManageFieldsScreen::OPTION_NAME, [] );
			$extraContent = '<div class="otgs-foo-user-fields">';

			foreach ( $fields as $fieldLabel ) {
				$fieldKey   = sanitize_key( $fieldLabel );
				$fieldValue = get_user_meta( $author->ID, $fieldKey, true );

				if ( $fieldValue ) {
					$filteredFieldLabel    = apply_filters( 'otgs_foo_user_fields_display_value', $fieldLabel, $fieldLabel, ManageFieldsScreen::LABELS_USER_ID );
					$filteredFieldValue    = apply_filters( 'otgs_foo_user_fields_display_value', $fieldValue, $fieldLabel, $author->ID );
					$extraContent .= '<p><b>' . esc_html( $filteredFieldLabel ) . ':</b> ' . esc_html( $filteredFieldValue ) . '</p>';
				}
			}

			$extraContent   .= '</div>';

			$content = $extraContent . $content;
		}

		return $content;
	}
}
