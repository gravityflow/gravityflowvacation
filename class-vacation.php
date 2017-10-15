<?php


// Make sure Gravity Forms is active and already loaded.
if ( class_exists( 'GFForms' ) ) {

	class Gravity_Flow_Vacation extends Gravity_Flow_Extension {

		private static $_instance = null;

		public $_version = GRAVITY_FLOW_VACATION_VERSION;

		public $edd_item_name = GRAVITY_FLOW_VACATION_EDD_ITEM_NAME;

		// The Framework will display an appropriate message on the plugins page if necessary
		protected $_min_gravityforms_version = '1.9.10';

		protected $_slug = 'gravityflowvacation';

		protected $_path = 'gravityflowvacation/vacation.php';

		// Title of the plugin to be used on the settings page, form settings and plugins page.
		protected $_title = 'Vacation Requests Extension';

		// Short version of the plugin title to be used on menus and other places where a less verbose string is useful.
		protected $_short_title = 'Vacation Requests';

		protected $_capabilities = array(
			'gravityflowvacation_uninstall',
			'gravityflowvacation_settings',
			'gravityflowvacation_edit_profiles',
		);

		protected $_capabilities_app_settings = 'gravityflowvacation_settings';
		protected $_capabilities_uninstall = 'gravityflowvacation_uninstall';

		public static function get_instance() {
			if ( self::$_instance == null ) {
				self::$_instance = new Gravity_Flow_Vacation();
			}

			return self::$_instance;
		}

		private function __clone() {
		} /* do nothing */

		public function init() {
			parent::init();
			add_filter( 'gform_entry_field_value', array( $this, 'filter_gform_entry_field_value' ), 10, 4 );
		}

		public function init_admin() {
			parent::init_admin();
			add_action( 'show_user_profile', array( $this, 'show_user_profile' ) );
			add_action( 'edit_user_profile', array( $this, 'show_user_profile' ) );
			add_action( 'personal_options_update', array( $this, 'user_profile_options_update' ) );
			add_action( 'edit_user_profile_update', array( $this, 'user_profile_options_update' ) );

			add_action( 'gform_field_standard_settings', array( $this, 'vacation_days_format_setting' ) );

			add_filter( 'manage_users_columns', array( $this, 'filter_manage_users_columns' ) );
			add_filter( 'manage_users_custom_column', array( $this, 'filter_manage_users_custom_column' ), 10, 3 );
		}

		/**
		 * Add the extension capabilities to the Gravity Flow group in Members.
		 *
		 * @since 1.1-dev
		 *
		 * @param array $caps The capabilities and their human readable labels.
		 *
		 * @return array
		 */
		public function get_members_capabilities( $caps ) {
			$prefix = $this->get_short_title() . ': ';

			$caps['gravityflowvacation_settings']      = $prefix . __( 'Manage Settings', 'gravityflowvacation' );
			$caps['gravityflowvacation_uninstall']     = $prefix . __( 'Uninstall', 'gravityflowvacation' );
			$caps['gravityflowvacation_edit_profiles'] = $prefix . __( 'Edit Users', 'gravityflowvacation' );

			return $caps;
		}

		function show_user_profile( $user ) {
			$approved  = gravity_flow_vacation()->get_approved_time_off( $user->ID );
			$remaining = gravity_flow_vacation()->get_balance( $user->ID );
			$disabled  = $this->current_user_can_any( 'gravityflowvacation_edit_profiles' ) ? '' : 'disabled="disabled"';
			?>
			<h3><?php esc_html_e( 'Vacation Information', 'gravityflowvacation' ); ?></h3>
			<table class="form-table">
				<tr>
					<th><label
							for="gravityflow_vacation_pto"><?php esc_html_e( 'Annual Paid Time Off (PTO)', 'gravityflowvacation' ); ?></label>
					</th>
					<td>
						<input type="text" <?php echo $disabled; ?> name="gravityflow_vacation_pto"
						       id="gravityflow_vacation_pto" class="small-text"
						       value="<?php echo esc_attr( $this->get_user_option( 'gravityflow_vacation_pto', $user->ID ) ); ?>"/><br/>
						<span
							class="description"><?php esc_html_e( 'Days based on service', 'gravityflowvacation' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label
							for="gravityflow_vacation_comp_days"><?php esc_html_e( 'Comp Days', 'gravityflowvacation' ); ?></label>
					</th>
					<td>
						<input type="text" <?php echo $disabled; ?> name="gravityflow_vacation_comp_days"
						       id="gravityflow_vacation_comp_days" class="small-text"
						       value="<?php echo esc_attr( $this->get_user_option( 'gravityflow_vacation_comp_days', $user->ID ) ); ?>"/><br/>
						<span
							class="description"><?php esc_html_e( 'Compensatory Days', 'gravityflowvacation' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label
							for="gravityflow_vacation_hr_adjustment"><?php esc_html_e( 'HR Adjustment', 'gravityflowvacation' ); ?></label>
					</th>
					<td>
						<input type="text" <?php echo $disabled; ?> name="gravityflow_vacation_hr_adjustment"
						       id="gravityflow_vacation_hr_adjustment" class="small-text"
						       value="<?php echo esc_attr( $this->get_user_option( 'gravityflow_vacation_hr_adjustment', $user->ID ) ); ?>"/><br/>
						<span
							class="description"><?php esc_html_e( 'Adjustment days', 'gravityflowvacation' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label
							for="gravityflow_vacation_carry"><?php esc_html_e( 'Carry Over Days', 'gravityflowvacation' ); ?></label>
					</th>
					<td>
						<input type="text" <?php echo $disabled; ?> name="gravityflow_vacation_carry"
						       id="gravityflow_vacation_carry" class="small-text"
						       value="<?php echo esc_attr( $this->get_user_option( 'gravityflow_vacation_carry', $user->ID ) ); ?>"/><br/>
						<span
							class="description"><?php esc_html_e( 'Days carried over from last year', 'gravityflowvacation' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Days approved', 'gravityflowvacation' ); ?></th>
					<td>
						<?php echo $approved; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Balance remaining', 'gravityflowvacation' ); ?></th>
					<td>
						<?php echo $remaining; ?>
					</td>
				</tr>
			</table>
			<?php
		}

		function user_profile_options_update( $user_id ) {
			if ( $this->current_user_can_any( 'gravityflowvacation_edit_profiles' ) ) {
				update_user_option( $user_id, 'gravityflow_vacation_pto', sanitize_text_field( $_POST['gravityflow_vacation_pto'] ) );
				update_user_option( $user_id, 'gravityflow_vacation_comp_days', sanitize_text_field( $_POST['gravityflow_vacation_comp_days'] ) );
				update_user_option( $user_id, 'gravityflow_vacation_hr_adjustment', sanitize_text_field( $_POST['gravityflow_vacation_hr_adjustment'] ) );
				update_user_option( $user_id, 'gravityflow_vacation_carry', sanitize_text_field( $_POST['gravityflow_vacation_carry'] ) );
			}
		}

		function vacation_days_format_setting( $position ) {
			if ( $position != 1400 ) {
				return;
			}
			?>
			<li class="vacation_days_format_setting field_setting">
				<label for="field_number_format">
					<?php esc_html_e( 'Number Format', 'gravityforms' ); ?>
					<?php gform_tooltip( 'form_field_number_format' ) ?>
				</label>
				<select id="field_number_format" onchange="SetFieldProperty('numberFormat', this.value);jQuery('.field_calculation_rounding').toggle(this.value != 'currency');">
					<option value="decimal_dot">9,999.99</option>
					<option value="decimal_comma">9.999,99</option>
				</select>

			</li>
			<?php
		}

		public function get_approved_time_off( $user_id = null ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			if ( empty( $user_id ) ) {
				return false;
			}

			$this->log_debug( __METHOD__ . '(): starting calculation of approved time of for user ID ' . $user_id );

			$forms = GFAPI::get_forms();

			$base_search_criteria = array(
				'status'        => 'active',
				'field_filters' => array(
					'mode' => 'all',
					array( 'key' => 'created_by', 'value' => $user_id ),
				),
			);

			// Default holiday year starts on 1 Jan
			$month = '01';

			/**
			 * Allows the start month for the holiday year to be filtered.
			 *
			 * @since 1.1
			 *
			 * @param $month
			 */
			apply_filters( 'gravityflowvacation_start_month', $month );

			// Ensure the month is prefixed with a leading zero.
			$month = sprintf( '%02d', $month );

			// Current year
			$year = date( 'Y' );

			$current_month = date( 'm' );

			if ( (int) $current_month < (int) $month ) {
				// The current holiday year started the during the previous calendar year
				$year -= 1;
			}

			$start_date           = $year . '-' . $month . '-01';
			$start_date_timestamp = strtotime( $start_date );

			$end_date           = date( 'Y-m-d', strtotime( '+1 year', $start_date_timestamp ) );
			$end_date_timestamp = strtotime( $end_date );
			$end_date_timestamp = strtotime( '-1 day', $end_date_timestamp );
			$end_date           = date( 'Y-m-d', $end_date_timestamp );

			$total = 0;

			foreach ( $forms as $form ) {
				$search_criteria = $base_search_criteria;
				$vacation_fields = GFAPI::get_fields_by_type( $form, 'workflow_vacation' );
				if ( ! empty( $vacation_fields ) ) {
					$date_fields = GFAPI::get_fields_by_type( $form, 'date' );
					if ( empty( $date_fields ) ) {
						$search_criteria['start_date'] = $start_date . ' 00:00:00';
						$search_criteria['end_date']   = $end_date . ' 23:59:59';
					} else {
						$date_field                         = $date_fields[0];
						$search_criteria['field_filters'][] = array( 'key'      => $date_field->id,
						                                             'value'    => $start_date,
						                                             'operator' => '>='
						);
						$search_criteria['field_filters'][] = array( 'key'      => $date_field->id,
						                                             'value'    => $end_date,
						                                             'operator' => '<'
						);
					}

					$search_criteria['field_filters'][] = array( 'key'   => 'workflow_final_status',
					                                             'value' => 'approved'
					);


					$paging = array( 'offset' => 0, 'page_size' => 150 );

					$entries = GFAPI::get_entries( $form['id'], $search_criteria, null, $paging );

					foreach ( $entries as $entry ) {
						foreach ( $vacation_fields as $vacation_field ) {
							$days = $entry[ (string) $vacation_field->id ];
							$this->log_debug( __METHOD__ . '(): adding ' . $days . ' days from entry ' . $entry['id'] );
							$total += $days;
						}
					}
				}
			}

			return $total;

		}

		public function get_balance( $user_id = null ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			if ( empty( $user_id ) ) {
				return false;
			}

			$approved = $this->get_approved_time_off( $user_id );

			$annual_paid_time_off = $this->get_user_option( 'gravityflow_vacation_pto', $user_id );

			$comp_days = $this->get_user_option( 'gravityflow_vacation_comp_days', $user_id );

			$hr_adjustment = $this->get_user_option( 'gravityflow_vacation_hr_adjustment', $user_id );

			$carry = $this->get_user_option( 'gravityflow_vacation_carry', $user_id );

			$total_available = $annual_paid_time_off + $comp_days + $hr_adjustment + $carry - $approved;

			/**
			 * Allows the user's balance to be filtered.
			 *
			 * @since 1.1
			 *
			 * @param float $total_available      The total balance available for the user.
			 * @param int   $user_id              The User ID.
			 * @param float $annual_paid_time_off The value of the paid time off setting for the user.
			 * @param float $comp_days            The value of the Comp Days setting for the user.
			 * @param float $hr_adjustment        The value of the HR adjustment setting for the user.
			 * @param float $carry                The value of the Carry Over setting for the user.
			 * @param float $approved             The number of days approved for the user.
			 */
			$total_available = apply_filters( 'gravityflowvacation_balance', $total_available, $user_id, $annual_paid_time_off, $comp_days, $hr_adjustment, $carry, $approved );

			return $total_available;
		}

		public function get_user_option( $meta_key, $user_id = false ) {

			$value = get_user_option( $meta_key, $user_id );

			if ( $value === false ) {
				$defaults = $this->get_user_option_defaults();
				if ( isset( $defaults[ $meta_key ] ) ) {
					$value = $defaults[ $meta_key ];
				}
			}

			return $value;
		}

		public function get_user_option_defaults() {
			$defaults = array(
				'gravityflow_vacation_pto'           => 20,
				'gravityflow_vacation_comp_days'     => 0,
				'gravityflow_vacation_hr_adjustment' => 0,
				'gravityflow_vacation_carry'         => 0,
			);

			return $defaults;
		}

		public function filter_manage_users_columns( $columns ) {

			$columns['gravityflow_vacation_pto']               = esc_html__( 'PTO', 'gravityflowvacation' );
			$columns['gravityflow_vacation_comp_days']         = esc_html__( 'Comp Days', 'gravityflowvacation' );
			$columns['gravityflow_vacation_hr_adjustment']     = esc_html__( 'HR Adjustment', 'gravityflowvacation' );
			$columns['gravityflow_vacation_carry']             = esc_html__( 'Carry Over', 'gravityflowvacation' );
			$columns['gravityflow_vacation_approved']          = esc_html__( 'Approved Time Off', 'gravityflowvacation' );
			$columns['gravityflow_vacation_balance_remaining'] = esc_html__( 'Balance Remaining', 'gravityflowvacation' );

			return $columns;
		}

		public function filter_manage_users_custom_column( $value, $column_name, $user_id ) {

			switch ( $column_name ) {
				case 'gravityflow_vacation_pto' :
				case 'gravityflow_vacation_comp_days' :
				case 'gravityflow_vacation_hr_adjustment' :
				case 'gravityflow_vacation_carry' :
					$value = $this->get_user_option( $column_name, $user_id );
					break;
				case 'gravityflow_vacation_approved' :
					$value = $this->get_approved_time_off( $user_id );
					break;
				case 'gravityflow_vacation_balance_remaining' :
					$value = $this->get_balance( $user_id );
			}

			return $value;
		}

		public function filter_gform_entry_field_value( $display_value, $field, $entry, $form ) {
			if ( $field->type == 'workflow_vacation' ) {
				$user_id       = $entry['created_by'];
				$remaining     = self::get_balance( $user_id );
				$html          = '<div class="gravityflow-vacation-days-balance-container">';
				$html          .= esc_html__( 'Current balance:', 'gravityflowvacation' ) . ' ' . esc_html( $remaining );
				$html          .= '<div>';
				$display_value = $html . $display_value;
			}

			return $display_value;
		}
	}
}
