<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemetaanCplCpmkMk extends Model
{
    use HasFactory;

    protected $table = 'tbl_pemetaan_cpl_cpmk_mk';

    protected $primaryKey = 'desk_cpmk';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_cpl',
        'desk_cpl',
        'id_cpmk',
        'desk_cpmk',
        'id_mk',
    ];

    public function getRouteKeyName()
    {
        return 'desk_cpmk';
    }
}
