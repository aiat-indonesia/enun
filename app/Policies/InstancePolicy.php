

namespace App\Policies;

use App\Models\User;
use App\Models\Instance;
use Illuminate\Auth\Access\Response;

class InstancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_instance');
    }

    public function view(User $user, Instance $instance): bool
    {
        return $user->can('view_instance');
    }

    public function create(User $user): bool
    {
        return $user->can('create_instance');
    }

    public function update(User $user, Instance $instance): bool
    {
        return $user->can('update_instance');
    }

    public function delete(User $user, Instance $instance): bool
    {
        return $user->can('delete_instance');
    }

    public function restore(User $user, Instance $instance): bool
    {
        return $user->can('restore_instance');
    }

    public function forceDelete(User $user, Instance $instance): bool
    {
        return $user->can('force_delete_instance');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_instance');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_instance');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_instance');
    }

    public function replicate(User $user, Instance $instance): bool
    {
        return $user->can('replicate_instance');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_instance');
    }
}
