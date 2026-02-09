<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotPenilaian extends Model
{
    use HasFactory;

    protected $table = 'tbl_bobot_penilaian';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $casts = [
        'partisipasi' => 'decimal:2',
        'observasi' => 'decimal:2',
        'unjuk_kerja' => 'decimal:2',
        'tes_uts' => 'decimal:2',
        'tes_uas' => 'decimal:2',
        'tes_lisan' => 'decimal:2',
        'total' => 'decimal:2',
        'total_nilai_mk' => 'decimal:2',
    ];

    protected $fillable = [
        'id_mk',
        'nama_mk',
        'id_cpl',
        'id_cpmk',
        'mbkm',
        'partisipasi',
        'observasi',
        'unjuk_kerja',
        'tes_uts',
        'tes_uas',
        'tes_lisan',
        'total',
        'total_nilai_mk',
    ];
}
