<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Work;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WorkPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Work');
    }

    public function view(AuthUser $authUser, Work $work): bool
    {
        return $authUser->can('View:Work');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Work');
    }

    public function update(AuthUser $authUser, Work $work): bool
    {
        return $authUser->can('Update:Work');
    }

    public function delete(AuthUser $authUser, Work $work): bool
    {
        return $authUser->can('Delete:Work');
    }

    public function restore(AuthUser $authUser, Work $work): bool
    {
        return $authUser->can('Restore:Work');
    }

    public function forceDelete(AuthUser $authUser, Work $work): bool
    {
        return $authUser->can('ForceDelete:Work');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Work');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Work');
    }

    public function replicate(AuthUser $authUser, Work $work): bool
    {
        return $authUser->can('Replicate:Work');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Work');
    }
}
