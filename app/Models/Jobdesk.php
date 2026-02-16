<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jobdesk extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'nama',
        'spesialisasi',
        'description',
    ];

    // eloquent relationship
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
