<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\School;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'db_name',
        'db_username',
        'db_password',
        'location',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
} 