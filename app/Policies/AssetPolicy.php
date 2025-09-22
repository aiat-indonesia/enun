

namespace App\Policies;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Auth\Access\Response;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->can('view_asset');
    }

    public function create(User $user): bool
    {
        return $user->can('create_asset');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->can('update_asset');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->can('delete_asset');
    }

    public function restore(User $user, Asset $asset): bool
    {
        return $user->can('restore_asset');
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->can('force_delete_asset');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_asset');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_asset');
    }

    public function replicate(User $user, Asset $asset): bool
    {
        return $user->can('replicate_asset');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_asset');
    }
}
