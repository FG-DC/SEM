<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'equipments';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'division_id',
        'equipment_type_id',
        'consumption',
        'standby',
        'activity'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id', 'id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class, 'equipments_observations', 'equipment_id', 'observation_id')->withPivot('standby_mode');
    }
}
