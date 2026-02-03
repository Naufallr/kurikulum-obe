<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemetaanSubcpmk extends Model
{
    use HasFactory;

    protected $table = 'tbl_pemetaan_mk_cpmk_subcpmk';

    protected $primaryKey = 'desk_subcpmk';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_mk',
        'id_cpl',
        'id_cpmk',
        'desk_cpmk',
        'sub_cpmk',
        'desk_subcpmk',
    ];

    public function getRouteKeyName()
    {
        return 'desk_subcpmk';
    }
}
