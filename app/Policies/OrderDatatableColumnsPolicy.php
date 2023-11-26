<?php

namespace App\Policies;

use App\OrderDatatableColumns;
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
     * @param OrderDatatableColumns $orderDatatableColumns
     * @return bool
     */
    private function recordBelongsToUser(User $user, OrderDatatableColumns $orderDatatableColumns): bool
    {
        return $user->id === $orderDatatableColumns->user_id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param OrderDatatableColumns $orderDatatableColumns
     * @return bool
     */
    public function view(User $user, OrderDatatableColumns $orderDatatableColumns): bool
    {
        return $this->recordBelongsToUser($user, $orderDatatableColumns);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param OrderDatatableColumns $orderDatatableColumns
     * @return Response|bool
     */
    public function update(User $user, OrderDatatableColumns $orderDatatableColumns): Response|bool
    {
        return $this->recordBelongsToUser($user, $orderDatatableColumns);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param OrderDatatableColumns $orderDatatableColumns
     * @return bool
     */
    public function delete(User $user, OrderDatatableColumns $orderDatatableColumns): bool
    {
        return $this->recordBelongsToUser($user, $orderDatatableColumns);
    }
}
