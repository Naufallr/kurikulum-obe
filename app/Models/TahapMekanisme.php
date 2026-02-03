<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahapMekanisme extends Model
{
    use HasFactory;

    protected $table = 'tbl_tahap_mekanisme_penilaian';
    // Asumsikan tabel memiliki kolom auto-increment "id" sebagai primary key
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        // 'id' tidak perlu dimasukkan ke fillable karena akan di-generate oleh DB
        'id_cpl',
        'id_mk',
        'id_cpmk',
        'tahap_penilaian',
        'teknik_penilaian',
        'instrumen',
        'kriteria',
        'bobot',
        'bobot_mk',
    ];
}
