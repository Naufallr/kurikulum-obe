<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaianLulusanProdi extends Model
{
    use HasFactory;

    protected $table = 'mst_cplp';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'desk',
        'aspek',
    ];
}
