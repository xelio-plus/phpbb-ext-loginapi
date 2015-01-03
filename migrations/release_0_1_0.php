<?php

namespace xelioplus\loginapi\migrations;

class release_0_1_0 extends \phpbb\db\migration\migration
{
    public function update_data()
    {
        return array(
            array('permission.add', array('a_external')),
            array('permission.role_add', array('ROLE_ADMIN_EXTERNAL', 'a_', 'ROLE_DESCRIPTION_ADMIN_EXTERNAL')),
            array('permission.permission_set', array('ROLE_ADMIN_EXTERNAL', 'a_external')),
        );
    }
}
