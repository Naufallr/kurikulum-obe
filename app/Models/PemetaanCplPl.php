<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemetaanCplPl extends Model
{
    use HasFactory;

    protected $table = 'tbl_pl_cpl';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pl',
        'id_cplp',
    ];
}
