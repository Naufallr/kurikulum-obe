<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaianPembelajaranMataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mst_cpmk';

    protected $primaryKey = 'desk_cpmk';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_cpl',
        'desk_cpl',
        'id_cpmk',
        'desk_cpmk',
    ];

    public function getRouteKeyName()
    {
        return 'desk_cpmk';
    }
}
