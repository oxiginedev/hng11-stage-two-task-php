<?php

namespace App\Concerns;

use App\Models\Organisation;
use App\Models\OrganisationUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasOrganisations
{
    /**
     * Check if the user belongs to the given organisation
     *
     * @param \App\Models\Organisation $organisation
     * @return bool
     */
    public function belongsToOrganisation($organisation): bool
    {
        if (is_null($organisation)) {
            return false;
        }

        return $this->organisations->contains(function ($o) use ($organisation) {
            return $o->id === $organisation->id;
        });
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class, OrganisationUser::class);
    }
}
