<?php

namespace xelioplus\loginapi\acp;

class loginapi_info
{
    public function module()
    {
        return array(
            'filename'    => '\xelioplus\loginapi\acp\loginapi_module',
            'title'        => 'ACP_LOGINAPI_TITLE',
            'version'    => '0.1.0',
            'modes'        => array(
                'settings'    => array('title' => 'ACP_LOGINAPI', 'auth' => 'ext_xelioplus/loginapi && acl_a_board', 'cat' => array('ACP_LOGINAPI_TITLE')),
            ),
        );
    }
}
