<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_email',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function deploymentChecks()
    {
        return $this->hasMany(DeploymentCheck::class);
    }
}
