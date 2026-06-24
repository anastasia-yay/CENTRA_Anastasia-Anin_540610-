<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisasterType extends Model
{
    protected $fillable = [
        'nama_bencana',
        'icon_path',
        'deskripsi',
    ];

    public function disasterEvents()
    {
        return $this->hasMany(DisasterEvent::class);
    }
}
