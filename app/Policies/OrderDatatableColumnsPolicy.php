<?php

namespace App\Policies;

use App\OrderDatatableColumn;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OrderDatatableColumnsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can make actions on the model.
     *
     * @param User $user
     * @param OrderDatatableColumn $orderDatatableColumns
     * @return bool
     */
    private function recordBelongsToUser(User $user, OrderDatatableColumn $orderDatatableColumns): bool
    {
        return $user->id === $orderDatatableColumns->user_id && $orderDatatableColumns->hidden === false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param OrderDatatableColumn $orderDatatableColumns
     * @return bool
     */
    public function view(User $user, OrderDatatableColumn $orderDatatableColumns): bool
    {
        return $this->recordBelongsToUser($user, $orderDatatableColumns);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param OrderDatatableColumn $orderDatatableColumns
     * @return Response|bool
     */
    public function update(User $user, OrderDatatableColumn $orderDatatableColumns): Response|bool
    {
        return $this->recordBelongsToUser($user, $orderDatatableColumns);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param OrderDatatableColumn $orderDatatableColumns
     * @return bool
     */
    public function delete(User $user, OrderDatatableColumn $orderDatatableColumns): bool
    {
        return $this->recordBelongsToUser($user, $orderDatatableColumns);
    }
}
