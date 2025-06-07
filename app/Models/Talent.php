<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talents';

    protected $fillable = [
        'user_id',
        'is_active'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
