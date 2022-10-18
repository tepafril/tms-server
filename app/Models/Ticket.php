<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Status;
use App\Models\Type;
use App\Models\User;
use App\Models\Level;


class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'assigner_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function severity()
    {
        return $this->belongsTo(Level::class, 'severity_id');
    }

    public function priority()
    {
        return $this->belongsTo(Level::class, 'priority_id');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'summary',
        'description',
        'assignee_id',
        'assigner_id',
        'type_id',
        'status_id',
        'severity_id',
        'priority_id',
    ];
}
