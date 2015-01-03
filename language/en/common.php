<?php

if (!defined('IN_PHPBB')) {
    exit;
}

if (empty($lang) || !is_array($lang)) {
    $lang = array();
}

$lang = array_merge($lang, array(
    'LOGINAPI_INVALID_REQUEST'          => 'The request could not be parsed. Please contact your board administrator.',
    'LOGINAPI_EXPLAIN_GENERAL'          => 'You need to login in order to use the external site.',
    'LOGINAPI_EXPLAIN_SITE'             => 'You need to login in order to use «%1$s».',
    'LOGINAPI_BOT'                      => 'Bots are not allowed to login.',

    'ACP_LOGINAPI_TITLE'                => 'Login API (SSO)',
    'ACP_LOGINAPI'                      => 'Settings',
    'ACP_LOGINAPI_TOKEN'                => 'Secret Token',
    'ACP_LOGINAPI_SETTINGS_SAVED'       => 'Settings have been saved successfully!',

    'ACL_A_EXTERNAL'                    => 'Can manage external sites (Login API)',
    'ROLE_ADMIN_EXTERNAL'               => 'External Site Admin (Login API)',
    'ROLE_DESCRIPTION_ADMIN_EXTERNAL'   => 'Can manage external sites',
));
