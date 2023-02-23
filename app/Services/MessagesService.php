<?php

namespace App\Services;

use App\Entities\Product;
use App\Entities\Employee;
use App\Entities\OrderItem;
use App\Helpers\MessagesHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MessagesService {

    /**
     * Prepare list of products
     *
     * @param MessagesHelper $helper
     * @return Collection<OrderItem|null>
     */
    public function prepareOrderItemsCollection(MessagesHelper $helper)
    {
        try {
            $chatUser = $helper->getCurrentUser();
            $order = $helper->getOrder();

            if (is_a($chatUser, Employee::class)) {
                return $order->items->filter(function ($item) use ($chatUser) {
                    return empty($item->product->firm) || $item->product->firm->id == $chatUser->firm->id;
                });
            }
            return $order->items;
        } catch (\Exception $e) {
            Log::error('Cannot prepare product list',
                ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]);
            return collect();
        }
    }

    /**
     * Prepare Employees for possible Users
     *
     * @param Collection<Product> $employeesIds
     * @param Collection $currentEmployeesOnChat - collections with Employees ids
     * @return Collection<Employee>
     */
    public function prepareEmployees(Collection $employeesIds, Collection $currentEmployeesOnChat): Collection {
        
        $employeesIdsFiltered = [];

        foreach($employeesIds as $productEmployees) {
            $productEmployees = json_decode($productEmployees);

            if(!empty($productEmployee)) continue;

            foreach($productEmployees as $employeeId) {
                $employeesIdsFiltered[] = $employeeId;
            }
        }

        // remove no unique employees
        $employeesIdsFiltered = collect($employeesIdsFiltered)->unique();
        // remove already existed as chat users employees
        $employeesIdsFiltered = $employeesIdsFiltered->diff($currentEmployeesOnChat);

        $possibleUsers = Employee::findMany($employeesIdsFiltered);

        return $possibleUsers;
    }
}
