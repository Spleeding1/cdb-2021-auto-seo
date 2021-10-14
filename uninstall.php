<?php

defined('ABSPATH') or exit;

// exit if uninstall constant is not defined
defined('WP_UNINSTALL_PLUGIN') or exit;

// delete plugin options
delete_option('CDB_2021_SILENT_SEO_VERSION');
delete_option('cdb_2021_silent_seo_options');
