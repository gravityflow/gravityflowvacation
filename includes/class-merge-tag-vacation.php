<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Merge_Tag_Workflow_Vacation extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.1.2-dev
	 *
	 * @var null
	 */
	public $name = 'workflow_vacation';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.1.2-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_vacation(:(.*?))?}/';

	/**
	 * Replace the {workflow_vacation} merge tags.
	 *
	 * @since 1.1.2-dev
	 *
	 * @param string $text  The text to be processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {

			if ( empty( $this->entry ) || empty( $this->entry['created_by'] ) ) {
				foreach ( $matches as $match ) {
					$full_tag = $match[0];
					$text = str_replace( $full_tag, '', $text );
				}
				return $text;
			}

			$entry = $this->entry;
			$created_by = $entry['created_by'];

			if ( ! empty( $created_by ) ) {
				foreach ( $matches as $match ) {
					$full_tag       = $match[0];
					$modifier = isset( $match[2] ) ? $match[2] : '';

					switch ( $modifier ) {
						case 'pto':
						case 'comp_days':
						case 'hr_adjustment':
						case 'carry':
							$value = gravity_flow_vacation()->get_user_option( 'gravityflow_vacation_' . $modifier, $created_by );
							break;
						case 'approved':
							$value = gravity_flow_vacation()->get_approved_time_off( $created_by );
							break;
						default:
							$value = gravity_flow_vacation()->get_balance( $created_by );
					}

					$text = str_replace( $full_tag, $this->format_value( $value ), $text );
				}
			}
		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Vacation );
