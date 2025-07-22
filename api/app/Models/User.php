<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract()
    {
        return $this->contracts()->where('active', true)->with('plan')->first();
    }
}
