<?php

namespace xelioplus\loginapi\acp;

class loginapi_module
{
    public $u_action;

    public function main($id, $mode)
    {
        global $db, $user, $auth, $template, $cache, $request;
        global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

        $user->add_lang('acp/common');
        $this->tpl_name = 'loginapi_body';
        $this->page_title = $user->lang('ACP_LOGINAPI_TITLE');
        add_form_key('xelioplus/loginapi');

        if ($request->is_set_post('submit')) {
            if (!check_form_key('xelioplus/loginapi')) {
                trigger_error('FORM_INVALID');
            }

            $config->set('xelioplus_loginapi_token', $request->variable('xelioplus_loginapi_token', ''));

            trigger_error($user->lang('ACP_LOGINAPI_SETTINGS_SAVED').adm_back_link($this->u_action));
        }

        $template->assign_vars(array(
            'U_ACTION' => $this->u_action,
            'XELIOPLUS_LOGINAPI_TOKEN' => $config['xelioplus_loginapi_token'],
        ));
    }
}
