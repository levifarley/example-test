<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'address',
        'job_title',
    ];
}
