<?php

namespace OLOG\CRUD;

interface InterfaceCurrentUserHasAnyOfPermissions
{
    static public function currentUserHasAnyOfPermissions(array $permission_codes_arr);
}