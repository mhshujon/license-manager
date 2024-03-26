<?php
namespace mhshujon\LicenseManager\EDD;

use mhshujon\LicenseManager\LicenseManager;

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Manages license validation for a plugin using the Easy Digital Downloads (EDD) licensing system.
 *
 * @since 1.0.0
 */
class EDDLicenseManager extends LicenseManager
{
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
    public function validate_license() {
        $api_params = [
            'edd_action' => "{$this->action}_license",
            'license'    => $this->license,
            'item_id'    => $this->item_id, // The ID of the item in RexTheme
            'url'        => home_url()
        ];

        $license_url = $this->request_server;

        //check if license url is working
        $server_status = wp_remote_get( $license_url );

        if( is_wp_error( $server_status ) || empty( $server_status['response']['code'] ) || 200 != $server_status['response']['code'] ) {
            $license_url = $this->proxy_server;
        }

        // Call the custom API.
        $this->response_data = wp_remote_post(
            $license_url,
            [
                'body'      => $api_params,
                'timeout'   => 15,
                'sslverify' => false,
            ]
        );
    }

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
    public function get_response()
    {
        $success  = false;
        $message = '';

        $this->load_response_messages();

        if (is_wp_error($this->response_data) || 200 !== wp_remote_retrieve_response_code($this->response_data)) {
            $message = (is_wp_error($this->response_data) && !empty($this->response_data->get_error_message())) ? $this->response_data->get_error_message() : $this->error_messages[ 'default' ];
        }
        else {
            $license_data = json_decode(wp_remote_retrieve_body($this->response_data));
            if ( !empty( $license_data->success ) ) {
                $success = true;
            }
            else {
                $message = $this->error_messages[$license_data->error] ?? $this->error_messages['default'];
            }
        }

        if ( empty( $message ) ) {
            $message = $this->error_messages[ $this->action ] ?? $this->error_messages[ 'default' ];
        }

        return [
            'success'        => $success,
            'license_status' => $license_data->license ?? 'invalid',
            'message'        => $message,
            'response_data'  => $license_data ?? []
        ];
    }
}
