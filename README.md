## Installation

Install the SDK via composer with the following command

Package URL: [https://packagist.org/packages/mhshujon/license-manager](https://packagist.org/packages/mhshujon/license-manager)

```
composer require mhshujon/license-manager
```

## How to use?

> Easy Digital Downloads (EDD)
>> Validate your license
```
$license_manager = new \mhshujon\LicenseManager\EDD\EDDLicenseManager(
    'action', // Check https://easydigitaldownloads.com/docs/software-licensing-api/ for action list
    'item-id', // Check https://easydigitaldownloads.com/docs/software-licensing-api/
    'license-key',
    'product-title',
    'license-server-host-url'
);
$license_manager->validate_license();
$response = $license_manager->get_response();
```

> Easy Digital Downloads (EDD)
>> Get plugin update

```
// retrieve our license key from the DB
$license_key = trim( get_option( 'edd_sample_license_key' ) ); 

// setup the updater
$edd_updater = new \mhshujon\LicenseManager\EDD\PluginUpdater(
    'version' 	=> '1.0',   // current plugin version number
    'license' 	=> $license_key,    // license key (used get_option above to retrieve from DB)
    'item_id'   => EDD_SAMPLE_ITEM_ID,  // id of this plugin
    'author' 	=> 'Author Name',   // author of this plugin
    'beta'      => false    // set to true if you wish customers to receive update notifications of beta releases
);
```