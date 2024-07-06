<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organisation extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, OrganisationUser::class);
    }

    public function hasUserWithId(string $id): bool
    {
        return $this->members->contains(function ($user) use ($id) {
            return $user->id === $id;
        });
    }
}
