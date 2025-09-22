

namespace App\Policies;

use App\Models\User;
use App\Models\Place;
use Illuminate\Auth\Access\Response;

class PlacePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_place');
    }

    public function view(User $user, Place $place): bool
    {
        return $user->can('view_place');
    }

    public function create(User $user): bool
    {
        return $user->can('create_place');
    }

    public function update(User $user, Place $place): bool
    {
        return $user->can('update_place');
    }

    public function delete(User $user, Place $place): bool
    {
        return $user->can('delete_place');
    }

    public function restore(User $user, Place $place): bool
    {
        return $user->can('restore_place');
    }

    public function forceDelete(User $user, Place $place): bool
    {
        return $user->can('force_delete_place');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_place');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_place');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_place');
    }

    public function replicate(User $user, Place $place): bool
    {
        return $user->can('replicate_place');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_place');
    }
}
