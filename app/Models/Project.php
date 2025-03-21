<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'deadline',
        'state_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
