<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin access control (server-side)
    |--------------------------------------------------------------------------
    |
    | All admin routes under the 'admin' middleware are checked against
    | tbl_role_privileges / tbl_modules. The current request path (e.g.
    | "admin/acl/events/view/xyz") is matched to module routes stored in
    | tbl_modules.route (e.g. "admin/acl/events"). If the path equals or
    | starts with a module route the user has privilege for, access is allowed.
    |
    */

    'admin' => [
        'path_prefix' => 'cmcontrol',
        'login_path' => 'cmcontrol/login',
        'dashboard_path' => 'cmcontrol/dashboard',
        'logout_path' => 'cmcontrol/logout',
    ],

];
