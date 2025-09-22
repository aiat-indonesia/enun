

namespace App\Policies;

use App\Models\User;
use App\Models\Item;
use Illuminate\Auth\Access\Response;

class ItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_item');
    }

    public function view(User $user, Item $item): bool
    {
        return $user->can('view_item');
    }

    public function create(User $user): bool
    {
        return $user->can('create_item');
    }

    public function update(User $user, Item $item): bool
    {
        return $user->can('update_item');
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->can('delete_item');
    }

    public function restore(User $user, Item $item): bool
    {
        return $user->can('restore_item');
    }

    public function forceDelete(User $user, Item $item): bool
    {
        return $user->can('force_delete_item');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_item');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_item');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_item');
    }

    public function replicate(User $user, Item $item): bool
    {
        return $user->can('replicate_item');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_item');
    }
}
