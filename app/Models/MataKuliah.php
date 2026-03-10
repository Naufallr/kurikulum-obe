<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mst_mk';

    protected $primaryKey = 'nama_mk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_mk',
        'sks',
        'jenis_mk',
        'status',
        'tahun',
        'semester'
    ];
}
