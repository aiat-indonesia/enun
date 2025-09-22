

namespace App\Policies;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Auth\Access\Response;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_subject');
    }

    public function view(User $user, Subject $subject): bool
    {
        return $user->can('view_subject');
    }

    public function create(User $user): bool
    {
        return $user->can('create_subject');
    }

    public function update(User $user, Subject $subject): bool
    {
        return $user->can('update_subject');
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->can('delete_subject');
    }

    public function restore(User $user, Subject $subject): bool
    {
        return $user->can('restore_subject');
    }

    public function forceDelete(User $user, Subject $subject): bool
    {
        return $user->can('force_delete_subject');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_subject');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_subject');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_subject');
    }

    public function replicate(User $user, Subject $subject): bool
    {
        return $user->can('replicate_subject');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_subject');
    }
}
