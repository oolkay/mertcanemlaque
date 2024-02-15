<?php
/**
 * This class can be customized to quickly add a review request system.
 *
 * It includes:
 * - Multiple trigger groups which can be ordered by priority.
 * - Multiple triggers per group.
 * - Customizable messaging per trigger.
 * - Link to review page.
 * - Request reviews on a per user basis rather than per site.
 * - Allows each user to dismiss it until later or permanently seamlessly via AJAX.
 * - Integrates with attached tracking server to keep anonymous records of each triggers effectiveness.
 *   - Tracking Server API: https://gist.github.com/danieliser/0d997532e023c46d38e1bdfd50f38801
 *
 * To use this please include the following credit block as well as completing the following TODOS.
 *
 * Original Author: danieliser
 * Original Author URL: https://danieliser.com
 *
 * TODO Search & Replace elementinvader_ with your elementinvader
 * TODO Search & Replace elementinvader_ with your elementinvader
 * TODO Search & Replace 'elementinvader' with your 'elementinvader'
 * TODO Change the $api_url if your using the accompanying tracking server. Leave it blank to disable this feature.
 * TODO Modify the ::triggers function array with your custom triggers & text.
 * TODO Keep in mind highest priority group/code combination that has all passing conditions will be chosen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class elementinvader_Modules_Reviews
 *
 * This class adds a review request system for your plugin or theme to the WP dashboard.
 */
class elementinvader_Modules_Reviews {

	/**
	 * Tracking API Endpoint.
	 *
	 * @var string
	 */
	public static $api_url = '';

	/**
	 *
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'hooks' ) );
		add_action( 'wp_ajax_elementinvader_review_action', array( __CLASS__, 'ajax_handler' ) );
	}

	/**
	 * Hook into relevant WP actions.
	 */
	public static function hooks() {
		if ( is_admin() && current_user_can( 'edit_posts' ) ) {
			self::installed_on();
			add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
			add_action( 'network_admin_notices', array( __CLASS__, 'admin_notices' ) );
			add_action( 'user_admin_notices', array( __CLASS__, 'admin_notices' ) );
		}
	}

	/**
	 * Get the install date for comparisons. Sets the date to now if none is found.
	 *
	 * @return false|string
	 */
	public static function installed_on() {
		$installed_on = get_option( 'elementinvader_reviews_installed_on', false );

		if ( ! $installed_on ) {
			$installed_on = current_time( 'mysql' );
			update_option( 'elementinvader_reviews_installed_on', $installed_on );
		}

		return $installed_on;
	}

	/**
	 *
	 */
	public static function ajax_handler() {
		$args = wp_parse_args( $_REQUEST, array(
			'group'  => self::get_trigger_group(),
			'code'   => self::get_trigger_code(),
			'pri'    => self::get_current_trigger( 'pri' ),
			'reason' => 'maybe_later',
		) );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'elementinvader_review_action' ) ) {
			wp_send_json_error();
		}

		try {
			$user_id = get_current_user_id();

			$dismissed_triggers                   = self::dismissed_triggers();
			$dismissed_triggers[ $args['group'] ] = $args['pri'];
			update_user_meta( $user_id, '_elementinvader_reviews_dismissed_triggers', $dismissed_triggers );
			update_user_meta( $user_id, '_elementinvader_reviews_last_dismissed', current_time( 'mysql' ) );

			switch ( $args['reason'] ) {
				case 'maybe_later':
					update_user_meta( $user_id, '_elementinvader_reviews_last_dismissed', current_time( 'mysql' ) );
					break;
				case 'am_now':
				case 'already_did':
					self::already_did( true );
					break;
				case 'improve_suggestion':
					break;
			}

			wp_send_json_success();

		} catch ( Exception $e ) {
			wp_send_json_error( $e );
		}
	}

	/**
	 * @return int|string
	 */
	public static function get_trigger_group() {
		static $selected;

		if ( ! isset( $selected ) ) {

			$dismissed_triggers = self::dismissed_triggers();

			$triggers = self::triggers();

			foreach ( $triggers as $g => $group ) {
				foreach ( $group['triggers'] as $t => $trigger ) {
					if ( ! in_array( false, $trigger['conditions'] ) && ( empty( $dismissed_triggers[ $g ] ) || $dismissed_triggers[ $g ] < $trigger['pri'] ) ) {
						$selected = $g;
						break;
					}
				}

				if ( isset( $selected ) ) {
					break;
				}
			}
		}

		return $selected;
	}

	/**
	 * @return int|string
	 */
	public static function get_trigger_code() {
		static $selected;

		if ( ! isset( $selected ) ) {

			$dismissed_triggers = self::dismissed_triggers();

			foreach ( self::triggers() as $g => $group ) {
				foreach ( $group['triggers'] as $t => $trigger ) {
					if ( ! in_array( false, $trigger['conditions'] ) && ( empty( $dismissed_triggers[ $g ] ) || $dismissed_triggers[ $g ] < $trigger['pri'] ) ) {
						$selected = $t;
						break;
					}
				}

				if ( isset( $selected ) ) {
					break;
				}
			}
		}

		return $selected;
	}

	/**
	 * @param null $key
	 *
	 * @return bool|mixed|void
	 */
	public static function get_current_trigger( $key = null ) {
		$group = self::get_trigger_group();
		$code  = self::get_trigger_code();

		if ( ! $group || ! $code ) {
			return false;
		}

		$trigger = self::triggers( $group, $code );

		return empty( $key ) ? $trigger : ( isset( $trigger[ $key ] ) ? $trigger[ $key ] : false );
	}

	/**
	 * Returns an array of dismissed trigger groups.
	 *
	 * Array contains the group key and highest priority trigger that has been shown previously for each group.
	 *
	 * $return = array(
	 *   'group1' => 20
	 * );
	 *
	 * @return array|mixed
	 */
	public static function dismissed_triggers() {
		$user_id = get_current_user_id();

		$dismissed_triggers = get_user_meta( $user_id, '_elementinvader_reviews_dismissed_triggers', true );

		if ( ! $dismissed_triggers ) {
			$dismissed_triggers = array();
		}

		return $dismissed_triggers;
	}

	/**
	 * Returns true if the user has opted to never see this again. Or sets the option.
	 *
	 * @param bool $set If set this will mark the user as having opted to never see this again.
	 *
	 * @return bool
	 */
	public static function already_did( $set = false ) {
		$user_id = get_current_user_id();

		if ( $set ) {
			update_user_meta( $user_id, '_elementinvader_reviews_already_did', true );

			return true;
		}

		return (bool) get_user_meta( $user_id, '_elementinvader_reviews_already_did', true );
	}

	/**
	 * Gets a list of triggers.
	 *
	 * @param null $group
	 * @param null $code
	 *
	 * @return bool|mixed|void
	 */
	public static function triggers( $group = null, $code = null ) {
		static $triggers;

		if ( ! isset( $triggers ) ) {

			$open_message = __( 'Hey, I noticed you recently hit %d popup views on your site – that’s awesome!. I would very much appreciate if you could quickly give it a 5-star rating on WordPress, just to help us spread the word.', 'elementinvader' );
			$time_message = __( "Hey, you've been using Element Invader for %s on your site - I hope that its been helpful. I would very much appreciate if you could quickly give it a 5-star rating on WordPress, just to help us spread the word and motivate us to make it even better.", 'elementinvader' );

			$triggers = apply_filters( 'elementinvader_reviews_triggers', array(
				'time_installed' => array(
					'triggers' => array(
						'one_week'     => array(
							'message'    => sprintf( $time_message, __( '1 week', 'elementinvader' ) ),
							'conditions' => array(
								strtotime( self::installed_on() . ' +1 week' ) < time(),
							),
							'link'       => 'https://wordpress.org/support/plugin/elementinvader/reviews/?rate=5#rate-response',
							'pri'        => 10,
						),
						'one_month'    => array(
							'message'    => sprintf( $time_message, __( '1 month', 'elementinvader' ) ),
							'conditions' => array(
								strtotime( self::installed_on() . ' +1 month' ) < time(),
							),
							'link'       => 'https://wordpress.org/support/plugin/elementinvader/reviews/?rate=5#rate-response',
							'pri'        => 20,
						),
						'three_months' => array(
							'message'    => sprintf( $time_message, __( '3 months', 'elementinvader' ) ),
							'conditions' => array(
								strtotime( self::installed_on() . ' +3 months' ) < time(),
							),
							'link'       => 'https://wordpress.org/support/plugin/elementinvader/reviews/?rate=5#rate-response',
							'pri'        => 30,
						),

					),
					'pri'      => 10,
				),
				'open_count'     => array(
					'triggers' => array(
						'50_opens'  => array(
							'message'    => sprintf( $open_message, 50 ),
							'conditions' => array(
								get_option( 'elementinvader_total_open_count', 0 ) > 50,
							),
							'link'       => 'https://wordpress.org/support/plugin/elementinvader/reviews/?rate=5#rate-response',
							'pri'        => 10,
						),
						'100_opens' => array(
							'message'    => sprintf( $open_message, 100 ),
							'conditions' => array(
								get_option( 'elementinvader_total_open_count', 0 ) > 100,

							),
							'link'       => 'https://wordpress.org/support/plugin/elementinvader/reviews/?rate=5#rate-response',
							'pri'        => 20,
						),
						'500_opens' => array(
							'message'    => sprintf( $open_message, 500 ),
							'conditions' => array(
								get_option( 'elementinvader_total_open_count', 0 ) > 500,
							),
							'link'       => 'https://wordpress.org/support/plugin/elementinvader/reviews/?rate=5#rate-response',
							'pri'        => 30,
						),

					),
					'pri'      => 50,
				),
			) );

			// Sort Groups
			uasort( $triggers, array( __CLASS__, 'rsort_by_priority' ) );

			// Sort each groups triggers.
			foreach ( $triggers as $k => $v ) {
				uasort( $triggers[ $k ]['triggers'], array( __CLASS__, 'rsort_by_priority' ) );
			}
		}

		if ( isset( $group ) ) {
			if ( ! isset( $triggers[ $group ] ) ) {
				return false;
			}

			return ! isset( $code ) ? $triggers[ $group ] : (isset( $triggers[ $group ]['triggers'][ $code ] ) ? $triggers[ $group ]['triggers'][ $code ] : false);
		}

		return $triggers;
	}

	/**
	 * Render admin notices if available.
	 */
	public static function admin_notices() {
		if ( self::hide_notices() ) {
			return;
		}

		$group  = self::get_trigger_group();
		$code   = self::get_trigger_code();
		$pri    = self::get_current_trigger( 'pri' );
		$tigger = self::get_current_trigger();

		// Used to anonymously distinguish unique site+user combinations in terms of effectiveness of each trigger.
		$uuid = wp_hash( home_url() . '-' . get_current_user_id() );

		?>

		<script type="text/javascript">
			(function ($) {
				var trigger = {
					group: '<?php echo esc_js($group); ?>',
					code: '<?php echo esc_js($code); ?>',
					pri: '<?php echo esc_js($pri); ?>'
				};

				function dismiss(reason) {
					$.ajax({
						method: "POST",
						dataType: "json",
						url: ajaxurl,
						data: {
							action: 'elementinvader_review_action',
							nonce: '<?php echo wp_create_nonce( 'elementinvader_review_action' ); ?>',
							group: trigger.group,
							code: trigger.code,
							pri: trigger.pri,
							reason: reason
						}
					});

					<?php if ( ! empty( self::$api_url ) ) : ?>
					$.ajax({
						method: "POST",
						dataType: "json",
						url: '<?php echo esc_url(self::$api_url); ?>',
						data: {
							trigger_group: trigger.group,
							trigger_code: trigger.code,
							reason: reason,
							uuid: '<?php echo esc_js($uuid); ?>'
						}
					});
					<?php endif; ?>
				}

				$(document)
					.on('click', '.elementinvader-notice .elementinvader-dismiss', function (event) {
						var $this = $(this),
							reason = $this.data('reason'),
							notice = $this.parents('.elementinvader-notice');

						notice.fadeTo(100, 0, function () {
							notice.slideUp(100, function () {
								notice.remove();
							});
						});

						dismiss(reason);
					})
					.ready(function () {
						setTimeout(function () {
							$('.elementinvader-notice button.notice-dismiss').click(function (event) {
								dismiss('maybe_later');
							});
						}, 1000);
					});
			}(jQuery));
		</script>
		<style>
			.elementinvader-notice p {
				margin-bottom: 0;
			}

			.elementinvader-notice img.logo {
				float: right;
				margin-left: 10px;
				width: 75px;
				padding: 0.25em;
				border: 1px solid #ccc;
			}
		</style>


		<div class="notice notice-success is-dismissible elementinvader-notice">

			<p>
				<img class="logo" src="<?php echo ELEMENTINVADER_URL; ?>admin/images/icon-256x256.png" />
				<strong>
					<?php echo esc_html($tigger['message']); ?>
					<br />
					~ Element Invader
				</strong>
			</p>
			<ul>
				<li>
					<a class="elementinvader-dismiss" target="_blank" href="https://wordpress.org/support/plugin/elementinvader/reviews/?rate=5#rate-response" data-reason="am_now">
						<strong><?php _e( 'Ok, I will help you with 5 star rating', 'elementinvader' ); ?></strong>
					</a>
				</li>
				<li>
					<a href="https://elementinvader.com/faq#ticket" target="_blank"class="elementinvader-dismiss" data-reason="improve_suggestion">
						<?php _e( 'No, you doesn\'t deserver 5 stars and I will tell you why!', 'elementinvader' ); ?>
					</a>
				</li>
				<li>
					<a href="#" class="elementinvader-dismiss" data-reason="maybe_later">
						<?php _e( 'Nope, maybe later', 'elementinvader' ); ?>
					</a>
				</li>
				<li>
					<a href="#" class="elementinvader-dismiss" data-reason="already_did">
						<?php _e( 'I already did', 'elementinvader' ); ?>
					</a>
				</li>
			</ul>

		</div>

		<?php
	}

	/**
	 * Checks if notices should be shown.
	 *
	 * @return bool
	 */
	public static function hide_notices() {
		$conditions = array(
			self::already_did(),
			self::last_dismissed() && strtotime( self::last_dismissed() . ' +2 weeks' ) > time(),
			empty( self::get_trigger_code() ),
		);

		return in_array( true, $conditions );
	}

	/**
	 * Gets the last dismissed date.
	 *
	 * @return false|string
	 */
	public static function last_dismissed() {
		$user_id = get_current_user_id();

		return get_user_meta( $user_id, '_elementinvader_reviews_last_dismissed', true );
	}

	/**
	 * Sort array by priority value
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public static function sort_by_priority( $a, $b ) {
		if ( ! isset( $a['pri'] ) || ! isset( $b['pri'] ) || $a['pri'] === $b['pri'] ) {
			return 0;
		}

		return ( $a['pri'] < $b['pri'] ) ? - 1 : 1;
	}

	/**
	 * Sort array in reverse by priority value
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public static function rsort_by_priority( $a, $b ) {
		if ( ! isset( $a['pri'] ) || ! isset( $b['pri'] ) || $a['pri'] === $b['pri'] ) {
			return 0;
		}

		return ( $a['pri'] < $b['pri'] ) ? 1 : - 1;
	}

}

elementinvader_Modules_Reviews::init();

?>