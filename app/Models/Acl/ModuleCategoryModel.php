<?php

namespace App\Models\Acl;

use Illuminate\Database\Eloquent\Model;

class ModuleCategoryModel extends Model
{
    protected $table = 'tbl_module_categories';

    protected $primaryKey = 'ID';

    protected $fillable = ['category_name', 'css_class', 'display_order'];

    public function modules()
    {
        return $this->hasMany(ModuleModel::class, 'module_category_ID', 'ID');
    }
}
