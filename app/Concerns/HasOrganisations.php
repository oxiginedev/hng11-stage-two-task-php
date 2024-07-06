<?php

namespace App\Concerns;

use App\Models\Organisation;
use App\Models\OrganisationUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrganisations
{
    /**
     * Check if the user belongs to the given organisation
     *
     * @param  \App\Models\Organisation  $organisation
     */
    public function belongsToOrganisation($organisation): bool
    {
        if (is_null($organisation)) {
            return false;
        }

        return $this->ownsOrganisation($organisation) || $this->organisations->contains(function ($o) use ($organisation) {
            return $o->id === $organisation->id;
        });
    }

    /**
     * Get all organisations the user belongs to
     */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, OrganisationUser::class);
    }

    public function ownsOrganisation($organisation): bool
    {
        if (is_null($organisation)) {
            return false;
        }

        return $this->id === $organisation->{$this->getForeignKey()};
    }

    /**
     * Get all organisations the user owns
     */
    public function ownedOrganisations(): HasMany
    {
        return $this->hasMany(Organisation::class);
    }

    public function allOrganisations()
    {
        return $this->ownedOrganisations->merge($this->organisations)->sortBy('name');
    }
}
