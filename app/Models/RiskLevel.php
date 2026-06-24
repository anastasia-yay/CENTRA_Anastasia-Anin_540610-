<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskLevel extends Model
{
    protected $fillable = [
        'nama_tingkat',
        'warna_hex',
        'urutan',
    ];

    public function disasterEvents()
    {
        return $this->hasMany(DisasterEvent::class);
    }
}
