<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Notifiers
 */

/**
 * Represents the logic for showing the notification.
 */
class WPSEO_Configuration_Notifier implements WPSEO_Listener {

	/**
	 * Option name use to determine whether the notice has been dismissed.
	 *
	 * @var string
	 */
	const META_NAME = 'wpseo-dismiss-configuration-notice';

	/**
	 * Default value.
	 *
	 * @var string
	 */
	const META_VALUE = 'yes';

	/**
	 * Should the notification be shown.
	 *
	 * @var bool
	 */
	protected $show_notification;

	/**
	 * Constructs the object by setting the show notification property based the given options.
	 */
	public function __construct() {
		$this->show_notification = WPSEO_Options::get( 'show_onboarding_notice', false );
	}

	/**
	 * Returns the content of the notification.
	 *
	 * @return string A string with the notification HTML, or empty string when no notification is needed.
	 */
	public function notify() {
		if ( ! $this->show_notification() ) {
			$this->re_run_notification();
		}
		else {
			return $this->first_time_notification();
		}
	}

	/**
	 * Listens to an argument in the request URL. When triggered just set the notification to dismissed.
	 *
	 * @return void
	 */
	public function listen() {
		if ( ! $this->show_notification() || ! $this->dismissal_is_triggered() ) {
			return;
		}

		$this->set_dismissed();
	}

	/**
	 * Checks if the dismissal should be triggered.
	 *
	 * @return bool True when action has been triggered.
	 */
	protected function dismissal_is_triggered() {
		return filter_input( INPUT_GET, 'dismiss_get_started' ) === '1';
	}

	/**
	 * Checks if the current user has dismissed the notification.
	 *
	 * @return bool True when the notification has been dismissed.
	 */
	protected function is_dismissed() {
		return get_user_meta( get_current_user_id(), self::META_NAME, true ) === self::META_VALUE;
	}

	/**
	 * Sets the dismissed state for the current user.
	 *
	 * @return void
	 */
	protected function set_dismissed() {
		update_user_meta( get_current_user_id(), self::META_NAME, self::META_VALUE );
	}

	/**
	 * Checks if the notification should be shown.
	 *
	 * @return bool True when notification should be shown.
	 */
	protected function show_notification() {
		return $this->show_notification && ! $this->is_dismissed();
	}

	/**
	 * Returns the notification to re-run the config wizard.
	 *
	 * @return string The notification.
	 */
	private function re_run_notification() {
		$note = new Wizard_Notification();
		$notification = $note->get_notification( 2 );

		$notification_center = Yoast_Notification_Center::get();
		$notification_center->add_notification( $notification );
	}

	/**
	 * Returns the notification to start the config wizard for the first time.
	 *
	 * @return string The notification.
	 */
	private function first_time_notification() {
		$note = new Wizard_Notification();
		$notification = $note->get_notification( 0 );

		$notification_center = Yoast_Notification_Center::get();
		$notification_center->add_notification( $notification );
	}

}
