<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nama',
        'description',
    ];

    // orm
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public const ADMIN = 'admin';
    public const BOSS = 'boss';
    public const PEGAWAI = 'pegawai';

}
