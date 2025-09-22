

namespace App\Policies;

use App\Models\User;
use App\Models\Agent;
use Illuminate\Auth\Access\Response;

class AgentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_agent');
    }

    public function view(User $user, Agent $agent): bool
    {
        return $user->can('view_agent');
    }

    public function create(User $user): bool
    {
        return $user->can('create_agent');
    }

    public function update(User $user, Agent $agent): bool
    {
        return $user->can('update_agent');
    }

    public function delete(User $user, Agent $agent): bool
    {
        return $user->can('delete_agent');
    }

    public function restore(User $user, Agent $agent): bool
    {
        return $user->can('restore_agent');
    }

    public function forceDelete(User $user, Agent $agent): bool
    {
        return $user->can('force_delete_agent');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_agent');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_agent');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_agent');
    }

    public function replicate(User $user, Agent $agent): bool
    {
        return $user->can('replicate_agent');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_agent');
    }
}
