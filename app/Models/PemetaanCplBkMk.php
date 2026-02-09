<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemetaanCplBkMk extends Model
{
    use HasFactory;

    protected $table = 'tbl_cpl_bk_mk';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_cpl',
        'id_bk',
        'id_mk',
    ];
}
