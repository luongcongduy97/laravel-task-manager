<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Team extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
  ];

  public function users()
  {
    return $this->belongsToMany(User::class);
  }

  public function projects()
  {
    return $this->hasMany(Project::class);
  }
}