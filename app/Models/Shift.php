<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $fillable = [
        'name',
        'time_in',
        'time_out'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'departments_shifts')
            ->withTimestamps();
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
