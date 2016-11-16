<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}



class Gravity_Flow_Field_Vacation extends GF_Field_Number {

	public $type = 'workflow_vacation';

	public function add_button( $field_groups ) {
		$field_groups = $this->maybe_add_workflow_field_group( $field_groups );

		return parent::add_button( $field_groups );
	}

	public function maybe_add_workflow_field_group( $field_groups ) {
		foreach ( $field_groups as $field_group ) {
			if ( $field_group['name'] == 'workflow_fields' ) {
				return $field_groups;
			}
		}
		$field_groups[] = array( 'name' => 'workflow_fields', 'label' => __( 'Workflow Fields', 'gravityforms' ), 'fields' => array() );
		return $field_groups;
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'workflow_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	function get_form_editor_field_settings() {
		$number_field_settings = parent::get_form_editor_field_settings();
		if ( ( $key = array_search( 'number_format_setting', $number_field_settings ) ) !== false ) {
			unset( $number_field_settings[ $key ] );
		}
		$number_field_settings[] = 'vacation_days_format_setting';
		return $number_field_settings;
	}


	public function get_form_editor_field_title() {
		return __( 'Vacation Days', 'gravityflow' );
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		if ( $this->is_form_editor() ) {
			$remaining = '';
		} else {
			$created_by = $entry['created_by'];
			$remaining = self::get_balance( $created_by );
		}
		$html = '<div class="gravityflow-vacation-request-container">';
		$html .= parent::get_field_input( $form, $value, $entry );
		$html .= '<div class="gravityflow-vacation-request-balance gfield_description">';
		$html .= esc_html__( 'Current balance:', 'gravityflowvacation' ) . ' ' . esc_html( $remaining );
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$days = parent::get_value_entry_detail( $value, $currency, $use_text, $format, $media );

		$html = '<div class="gravityflow-vacation-days-container">';
		$html .= '<strong>' . sprintf( esc_html__( 'Days requested: %s', 'gravityflowvacation' ), $days ) . '</strong>';
		$html .= '</div>';
		return $html;
	}

	public function get_approved_time_off() {
		return gravity_flow_vacation()->get_approved_time_off();
	}

	public static function get_balance( $user_id = null ) {
		return gravity_flow_vacation()->get_balance( $user_id );
	}
}

GF_Fields::register( new Gravity_Flow_Field_Vacation() );
