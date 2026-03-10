<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCpmk extends Model
{
    use HasFactory;

    protected $table = 'mst_subcpmk';

    protected $primaryKey = 'id_subcpmk';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_cpl',
        'id_cpmk',
        'desk_cpmk',
        'id_subcpmk',
        'desk_subcpmk',
    ];

    public function getRouteKeyName()
    {
        return 'id_subcpmk';
    }
}
