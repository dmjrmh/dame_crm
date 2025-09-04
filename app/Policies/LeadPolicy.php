<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeadPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return in_array($user->role, ['sales', 'manager']);
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, Lead $lead): bool
  {
    return $user->role === 'manager' || $lead->user_id === $user->id;
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    return in_array($user->role, ['sales', 'manager']);
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Lead $lead): bool
  {
    return $user->role === 'manager' || $lead->user_id === $user->id;
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Lead $lead): bool
  {
    // Manager can delete all leads
    if ($user->role == 'manager') {
      return true;
    }

    // sales only
    return $lead->user_id == $user->id;
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, Lead $lead): bool
  {
    return $user->role === 'manager';
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, Lead $lead): bool
  {
    return $user->role === 'manager';
  }
}
