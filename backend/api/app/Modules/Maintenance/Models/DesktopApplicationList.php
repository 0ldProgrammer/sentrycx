<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class DesktopApplicationList extends Model {

    protected $fillable = ['name', 'description', 'is_self_install', 'is_ps', 'ps_or_dl', 'arguments', 'execution_date', 'time'];
    protected $table = 'desktop_application_list';


    public function installer() {
        return $this->hasOne(Installer::class, 'application_id');
    }
}