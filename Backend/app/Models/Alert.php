<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'alert',
        'observation_id'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function observation()
    {
        return $this->belongsTo(Observation::class, 'observation_id', 'id');
    }
}
