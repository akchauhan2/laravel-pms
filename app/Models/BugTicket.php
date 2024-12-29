<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'status',
        'project_id',
        'assigned_to',
        'screenshot',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
