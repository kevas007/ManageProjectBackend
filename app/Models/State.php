<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    /** @use HasFactory<\Database\Factories\StateFactory> */
    use HasFactory;

    protected  $fillable = [
        'name',
        'description',
    ];
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

}
