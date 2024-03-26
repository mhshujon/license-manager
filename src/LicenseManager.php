<?php
namespace mhshujon\LicenseManager;

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Manages license validation for a plugin using the Easy Digital Downloads (EDD) licensing system.
 *
 * @since 1.0.0
 */
abstract class LicenseManager
{
    /**
     * @var string The action to perform (e.g., activate, deactivate, check).
     *
     * @since 1.0.0
     */
    protected $action;

    /**
     * @var int The ID of the item in RexTheme.
     *
     * @since 1.0.0
     */
    protected $item_id;

    /**
     * @var string The license key.
     *
     * @since 1.0.0
     */
    protected $license;

    /**
     * @var string The URL of the Licensing server.
     *
     * @since 1.0.0
     */
    protected $request_server;

    /**
     * @var string The URL of the Licensing proxy server.
     *
     * @since 1.0.0
     */
    protected $proxy_server;

    /**
     * @var string The text domain for localization.
     *
     * @since 1.0.0
     */
    protected $text_domain;

    /**
     * @var string The name of the plugin.
     *
     * @since 1.0.0
     */
    protected $plugin_name;

    /**
     * @var array Error messages for different license validation outcomes.
     *
     * @since 1.0.0
     */
    protected $error_messages;

    /**
     * @var mixed The response data received after license validation.
     *
     * @since 1.0.0
     */
    protected $response_data;


    /**
     * Constructs a new LicenseManager instance.
     *
     * Initializes the LicenseManager object with provided parameters.
     *
     * @param string $action         The action to perform (e.g., activate, deactivate, check).
     * @param int    $item_id        The ID of the item in RexTheme.
     * @param string $license        The license key.
     * @param string $plugin_name    The name of the plugin.
     * @param string $text_domain    The text domain for localization.
     * @param string $request_server The URL of the custom API server.
     * @param string $proxy_server The URL of the custom API server.
     *
     * @since 1.0.0
     */
    public function __construct( $action, $item_id, $license, $plugin_name, $text_domain, $request_server, $proxy_server = null )
    {
        $this->action         = $action;
        $this->item_id        = $item_id;
        $this->license        = $license;
        $this->plugin_name    = $plugin_name;
        $this->text_domain    = $text_domain;
        $this->request_server = esc_url( $request_server );
        $this->proxy_server   = esc_url( $proxy_server );
    }

    /**
     * Loads response messages for different license validation actions.
     *
     * Sets error messages based on license validation outcomes, including activation,
     * deactivation, validation, and various error scenarios.
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function load_response_messages()
    {
        $this->error_messages = [
            'activate'            => __('Your license has been successfully activated.', $this->text_domain),
            'deactivate'          => __('Your license has been successfully deactivated.', $this->text_domain),
            'check'               => __('Your license has been successfully validated.', $this->text_domain),
            'revoked'             => __('Your license key has been disabled.', $this->text_domain),
            'missing'             => __('Invalid license.', $this->text_domain),
            'invalid'             => __('Your license is not active for this URL.', $this->text_domain),
            'site_inactive'       => __('Your license is not active for this URL.', $this->text_domain),
            'item_name_mismatch'  => sprintf(__('This appears to be an invalid license key for %s.', $this->text_domain), $this->plugin_name),
            'no_activations_left' => __('Your license key has reached its activation limit.', $this->text_domain),
            'deactivated'         => __('Your license successfully deactivate.', $this->text_domain),
            'failed'              => __('Your license deactivation failed.', $this->text_domain),
            'default'             => __('An unknown error occurred, please try again.', $this->text_domain)
        ];

        if ( !empty( $this->response_data->expires ) ) {
            $this->error_messages[ 'expired' ] = sprintf(
                __('Your license key expired on %s.', $this->text_domain),
                date_i18n(
                    get_option('date_format'),
                    strtotime( $this->response_data->expires, current_time('timestamp'))
                )
            );
        }
    }

    /**
     * Validates the license by making a request to the EDD licensing API.
     *
     * This method constructs API parameters and sends a POST request to the EDD licensing API endpoint.
     * It sets the response data property with the result of the API call.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public abstract function validate_license();

    /**
     * Retrieves the response data after license validation.
     *
     * This method checks the response data received from the license validation request.
     * It sets the success status and message based on the response.
     *
     * @since 1.0.0
     *
     * @return array The response data containing success status, license status, message, and response data.
     */
    public abstract function get_response();
}