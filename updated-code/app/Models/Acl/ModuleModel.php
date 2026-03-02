<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;

class ModuleModel extends Model
{
    protected $table = 'tbl_modules';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'module_category_ID',
        'module_name',
        'route',
        'show_in_menu',
        'css_class',
        'display_order',
    ];

    public function category()
    {
        return $this->belongsTo(ModuleCategoryModel::class, 'module_category_ID', 'ID');
    }

    public function roles()
    {
        return $this->hasMany(RolePrivilegeModel::class, 'module_ID', 'ID');
    }
}
