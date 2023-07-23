<?php

namespace App\Services\Orders;

use App\Entities\Order;
use App\Entities\OrderFiles;
use App\Entities\OrderPayment;
use App\Repositories\SpeditionExchangeRepository;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

readonly class OrderDatatableService
{
    public function __construct(
        private SpeditionExchangeRepository $speditionExchangeRepository
    ) {
        $this->dtColumns = [
            'clientFirstname' => 'customer_addresses.firstname',
            'clientLastname' => 'customer_addresses.lastname',
            'clientEmail' => 'customer_addresses.email',
            'clientPhone' => 'customer_addresses.phone',
            'statusName' => 'statuses.name',
            'name' => 'users.name',
            'orderId' => 'orders.id',
            'orderDate' => 'orders.created_at',
        ];
    }


    protected array $dtColumns;

    /**
     * @param null $selectAllDates
     * @return Builder
     */
    private function getQueryForDataTables($selectAllDates = null): Builder
    {
        return DB::table('orders')
            ->distinct()
            ->select(
                '*',
                'orders.created_at as orderDate',
                'orders.id as orderId',
                'customers.login as clientEmail',
                'statuses.name as statusName',
                'customer_addresses.firstname as clientFirstname',
                'customer_addresses.lastname as clientLastname',
                'customer_addresses.phone as clientPhone',
                'sel_tr__transaction.tr_CheckoutFormPaymentId as sello_payment',
                'sel_tr__transaction.tr_CheckoutFormId as sello_form',
                'task_times.date_start as production_date',
                'taskUser.firstname as taskUserFirstName',
                'taskUser.lastname as taskUserLastName'
            )
            //poniższe left joiny mają na celu wyświetlenie czasów oraz wykonwaców zadań z tabeli tasks na "gridzie"
            ->leftJoin('tasks', 'orders.id', '=', 'tasks.order_id')
            ->leftJoin('tasks as parentTask', 'parentTask.id', '=', 'tasks.parent_id')
            ->leftJoin('users as taskUser', DB::raw('COALESCE(parentTask.user_id, tasks.user_id)'), '=', 'taskUser.id')
            ->leftJoin('task_times', 'task_times.task_id', '=', DB::raw('COALESCE(parentTask.id, tasks.id)'))
            //tasks - koniec
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('warehouses', 'orders.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('statuses', 'orders.status_id', '=', 'statuses.id')
            ->leftJoin('sel_tr__transaction', 'orders.sello_id', '=', 'sel_tr__transaction.id')
            ->leftJoin('users', 'orders.employee_id', '=', 'users.id')
            ->leftJoin('order_dates', 'orders.id', '=', 'order_dates.order_id')
            ->leftJoin('customer_addresses', function ($join) {
                $join->on('customers.id', '=', 'customer_addresses.customer_id')
                    ->where('type', '=', 'STANDARD_ADDRESS');
            })->where(function ($query) {
                if (Auth::user()->role_id == 4) {
                    $query->where('orders.employee_id', '=', Auth::user()->id);
                }
            })->where(function ($query) use (&$selectAllDates) {
                if ($selectAllDates === 'false') {
                    $query->where('orders.created_at', '>', Carbon::now()->addMonths(-3))
                        ->orWhere('orders.updated_at', '>', Carbon::now()->addMonths(-3));
                }
            });
    }

    /**
     * @param $data
     * @param bool $withoutPagination
     * @param bool $minId
     * @return array
     */
    public function prepareCollection($data, bool $withoutPagination = false,  bool $minId = false): array
    {
        $sortingColumnId = $data['order'][0]['column'];
        $sortingColumnDirection = $data['order'][0]['dir'];

        $sortingColumns = [
            4 => 'users.name',
            13 => 'orders.created_at',
            14 => 'orders.id',
            17 => 'statuses.name',
            10 => 'symbol',
            20 => 'customer_notices',
            22 => 'customer_addresses.phone',
            23 => 'customer_addresses.email',
            24 => 'customer_addresses.firstname',
            25 => 'customer_addresses.lastname',
            46 => 'sel_tr__transaction.tr_CheckoutFormPaymentId'
        ];

        if (array_key_exists($sortingColumnId, $sortingColumns)) {
            $sortingColumn = $sortingColumns[$sortingColumnId];
        } else {
            $sortingColumn = 'orders.id';
        }
        $query = $this->getQueryForDataTables($data['selectAllDates'])->orderBy($sortingColumn, $sortingColumnDirection);

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                if ($column['name'] != "shipment_date") {
                    if (array_key_exists($column['name'], $this->dtColumns)) {
                        if ($column['name'] == 'statusName' && $column['search']['regex']) {
                            $query->whereRaw($this->dtColumns[$column['name']] . ' REGEXP ' . "'{$column['search']['value']}'");
                        } else {
                            $query->where($this->dtColumns[$column['name']], 'LIKE', "%{$column['search']['value']}%");
                        }
                    } else {
                        $columnName = $this->replaceSearch[$column['name']] ?? $column['name'];

                        if ($column['name'] == 'sello_payment') {
                            $searchValue = trim($column['search']['value']);
                            $query->where(function ($query) use ($column, $columnName, $searchValue) {
                                $query->orWhere($columnName, 'LIKE', "%{$searchValue}%");
                                $query->orWhere('orders.return_payment_id', 'LIKE', "%{$searchValue}%");
                                $query->orWhere('orders.allegro_payment_id', 'LIKE', "%{$searchValue}%");
                            });
                        } else {
                            $query->where($columnName, 'LIKE', "%{$column['search']['value']}%");
                        }
                    }
                }
            } else {
                if ($column['name'] == "shipment_date" && !empty($column['search']['value'])) {
                    $now = new Carbon();

                    switch ($column['search']['value']) {
                        case "yesterday":
                            $query->where("orders.shipment_date", '<', $now->toDateString());
                            $query->where("orders.shipment_date", '>=', $now->subDay()->toDateString());
                            break;
                        case "today":
                            $query->where("orders.shipment_date", '>=', $now->toDateString());
                            $query->where("orders.shipment_date", '<', $now->addDay()->toDateString());
                            break;
                        case "tomorrow":
                            $query->where("orders.shipment_date", '>=', $now->addDay()->toDateString());
                            $query->where("orders.shipment_date", '<', $now->addDay()->toDateString());
                            break;
                        case "from_tomorrow":
                            $query->where("orders.shipment_date", '>=', $now->addDay()->toDateString());
                            break;
                        case "all":
                        default:
                            break;
                    }

                } elseif ($column['name'] == "remainder_date" && isset($column['search']['value'])) {
                    $val = filter_var($column['search']['value'], FILTER_VALIDATE_BOOLEAN);
                    if ($val) {
                        $query->whereRaw('remainder_date < Now()');
                    }
                } elseif ($column['name'] == "search_on_lp" && !empty($column['search']['value'])) {
                    $query->leftJoin('order_packages', 'orders.id', '=', 'order_packages.order_id');
                    $query->whereRaw('order_packages.letter_number' . ' REGEXP ' . "'{$column['search']['value']}'");
                } elseif (in_array($column['name'], $this->getLabelGroupsNames()) && !empty($column['search']['value'])) {
                    $query->whereExists(function ($innerQuery) use ($column) {
                        $innerQuery->select("*")
                            ->from('order_labels')
                            ->whereRaw("order_labels.order_id = orders.id and order_labels.label_id = {$column['search']['value']}");
                    });
                } elseif ($column['name'] == "packages_sent" && !empty($column['search']['value'])) {
                    $searched = explode(' ', $column['search']['value']);
                    if ($searched[0] === 'plus') {
                        $query->whereExists(function ($innerQuery) use ($column, $searched) {
                            $innerQuery->select("*")
                                ->from('order_packages')
                                ->whereRaw("order_packages.order_id = orders.id AND order_packages.delivery_cost_balance > " . $searched[1] . " AND order_packages.status IN ('SENDING', 'DELIVERED')");
                        });
                    } else if ($searched[0] === 'minus') {
                        $query->whereExists(function ($innerQuery) use ($column, $searched) {
                            $innerQuery->select("*")
                                ->from('order_packages')
                                ->whereRaw("order_packages.order_id = orders.id AND order_packages.delivery_cost_balance < -" . $searched[1] . " AND order_packages.status IN ('SENDING', 'DELIVERED')");
                        });
                    } else {
                        $query->whereExists(function ($innerQuery) use ($column) {
                            $innerQuery->select("*")
                                ->from('order_packages')
                                ->whereRaw("order_packages.order_id = orders.id AND order_packages.delivery_courier_name LIKE '{$column['search']['value']}' AND order_packages.status IN ('SENDING', 'DELIVERED')");
                        });
                    }
                } elseif ($column['name'] == "packages_not_sent" && !empty($column['search']['value'])) {
                    $query->whereExists(function ($innerQuery) use ($column) {
                        $innerQuery->select("*")
                            ->from('order_packages')
                            ->whereRaw("order_packages.order_id = orders.id AND order_packages.delivery_courier_name LIKE '{$column['search']['value']}' AND order_packages.status NOT IN ('SENDING', 'DELIVERED', 'CANCELLED')");
                    });
                } elseif ($column['name'] == 'sum_of_payments' && !empty($column['search']['value'])) {
                    $query->whereRaw('(select sum(amount) from order_payments where order_payments.order_id = orders.id)' . ' LIKE ' . "'%{$column['search']['value']}%'");
                } elseif ($column['name'] == 'sum_of_gross_values' && !empty($column['search']['value'])) {
                    $query->whereRaw('CAST((select sum(net_selling_price_commercial_unit * quantity * 1.23) from order_items where order_id = orders.id) AS DECIMAL (12,2)) + IFNULL(orders.additional_service_cost, 0) + IFNULL(orders.additional_cash_on_delivery_cost, 0) + IFNULL(orders.shipment_price_for_client, 0)' . ' LIKE ' . "'%{$column['search']['value']}%'");
                } elseif ($column['name'] == 'left_to_pay' && !empty($column['search']['value'])) {
                    $sumQuery = '
                    IFNULL((
                            CAST(
                                (SELECT sum(gross_selling_price_commercial_unit * quantity) FROM order_items where order_id = orders.id)
                            AS DECIMAL (12,2))
                            + IFNULL(orders.additional_service_cost, 0)
                            + IFNULL(orders.additional_cash_on_delivery_cost, 0)
                            + IFNULL(orders.shipment_price_for_client, 0)
                        )
                        - IFNULL(
                            (SELECT sum(amount) FROM order_payments WHERE order_payments.order_id = orders.id AND order_payments.promise != "1")
                        , 0)
                    , 0)
                    ';
                    if ($data['differenceMode']) {
                        $differenceUp = (float)($column['search']['value'] + 2);
                        $differenceDown = (float)($column['search']['value'] - 2);
                        $query->whereRaw($sumQuery . ' < ' . "{$differenceUp}" . ' AND ' . $sumQuery . ' > ' . $differenceDown);
                    } else {
                        $query->whereRaw($sumQuery . ' = ' . "'{$column['search']['value']}'");
                    }
                }
            }
        }

        if ($minId) {
            $query->where($sortingColumns[14], '>', $minId);
        }

        if (isset($data['same'])) {
            $query->whereRaw("date({$data["dateColumn"]}) = '{$data['dateFrom']}'");
        } else {
            if (isset($data['dateFrom'])) {
                $query->whereRaw("date({$data["dateColumn"]}) >= '{$data['dateFrom']}'");
            }
            if (isset($data['dateTo'])) {
                $query->whereRaw("date({$data["dateColumn"]}) <= '{$data['dateTo']}'");
            }
        }

        if (!empty($data['customerId'])) {
            $query->where('orders.customer_id', $data['customerId']);
        }

        if (array_key_exists('selectOnlyWrongInvoiceBilansOrders', $data) && $data['selectOnlyWrongInvoiceBilansOrders'] === 'true') {
            $query->whereRaw('orders.invoice_bilans = 0');
        }

        //$query->whereRaw('COALESCE(last_status_update_date, orders.created_at) < DATE_ADD(NOW(), INTERVAL -30 DAY)');

        $count = $query->count();

        if ($withoutPagination) {
            $collection = $query
                ->get();
        } else {
            $collection = $query
                ->limit($data['length'])->offset($data['start'])
                ->get();
        }

        foreach ($collection as $row) {
            $orderId = $row->orderId;
            $row->speditionCost = 0;
            $row->allegro_commission = abs(DB::table('order_allegro_commissions')->where('order_id', $row->orderId)->sum('amount'));
            $row->items = DB::table('order_items')->where('order_id', $row->orderId)->get();
            $row->connected = DB::table('orders')->where('master_order_id', $row->orderId)->get();
            $row->payments = OrderPayment::withTrashed()->where('order_id', $row->orderId)->get();
            $row->packages = DB::table('order_packages')->where('order_id', $row->orderId)->get();

            foreach ($row->packages as $package) {
                $package->realSpecialCosts = DB::table('order_packages_real_cost_for_company')
                        ->select('const')
                        ->where('order_package_id', $package->id)
                        ->groupBy('order_package_id')
                        ->first();
                $row->speditionCost += $package->realSpecialCosts?->sum;
            }

            $row->packages?->map(function ($item) {
                $item->sumOfCosts = DB::table('order_packages_real_cost_for_company')
                    ->select(DB::raw('SUM(cost) as sum'))
                    ->where('order_package_id', $item->id)
                    ->groupBy('order_package_id')
                    ->first();

                return $item;
            });

            $row->otherPackages = DB::table('order_other_packages')->where('order_id', $row->orderId)->get();
            $row->addresses = DB::table('order_addresses')->where('order_id', $row->orderId)->get();
            $row->history = Order::where('customer_id', $row->customer_id)->with('labels')->get();
            $row->left_to_pay = 0;
            $row->history = $row->history->reduce(function ($acu, $current) {
                $insert = ['id' => $current->id, 'labels' => $current->labels];
                $acu[] = $insert;
                return $acu;
            }, []);
            $invoices = DB::table('order_order_invoices')->where('order_id', $row->orderId)->get(['invoice_id']);
            $invoiceValues = DB::table('order_invoice_values')->where('order_id', $row->orderId)->get();
            $arrInvoice = [];
            foreach ($invoices as $invoice) {
                $arrInvoice[] = $invoice->invoice_id;
            }
            if (count($arrInvoice) > 0) {
                $row->invoices = DB::table('order_invoices')->whereIn('id', $arrInvoice)->get();
            }
            $row->allegroGeneralExpenses = DB::table('allegro_general_expenses')->where('order_id', $row->orderId)->get();

            $labels = [];
            $labelsIds = DB::table('order_labels')->where('order_id', $row->orderId)->get();
            if (!empty($labelsIds)) {
                foreach ($labelsIds as $labelId) {
                    $labels[] = DB::table('labels')->where('id', $labelId->label_id)->get();
                }
                foreach ($labels as $label) {
                    if (isset($label[0])) {
                        if (!empty($label[0]->label_group_id)) {
                            $label[0]->label_group = DB::table('label_groups')->where(
                                'id',
                                $label[0]->label_group_id
                            )->get();
                        }

                        foreach ($labelsIds as $labelId) {
                            if ($labelId->label_id === $label[0]->id) {
                                $label[0]->added_type = $labelId->added_type;
                                break;
                            }
                        }
                    }
                }
            }
            $row->labels = $labels;
            $row->invoiceValues = $invoiceValues;
            $row->closest_label_schedule_type_c = DB::table('order_label_schedulers')
                ->where('order_id', $row->orderId)
                ->where('type', 'C')
                ->where('triggered_at', null)
                ->orderBy('trigger_time')
                ->first();
            $row->generalMessage = $this->generateMessagesForTooltip($row->orderId, "GENERAL");
            $row->shippingMessage = $this->generateMessagesForTooltip($row->orderId, "SHIPPING", false);
            $row->warehouseMessage = $this->generateMessagesForTooltip($row->orderId, "WAREHOUSE");
            $row->complaintMessage = $this->generateMessagesForTooltip($row->orderId, "COMPLAINT");
            $row->transport_exchange_offers = $this->speditionExchangeRepository
                ->with(['speditionOffers', 'chosenSpedition'])
                ->whereHas('items', function ($q) use ($orderId) {
                    $q->where('order_id', '=', $orderId);
                })
                ->all();
            $row->files = OrderFiles::where('order_id', $row->orderId)->get();
        }

        foreach ($collection as $item) {
            $item->packages->map(function ($package) {
                $package->realCosts = DB::table('order_packages_real_cost_for_company')
                    ->where('order_package_id', $package->id)
                    ->get();
                return $package;
            });
        }

        return [$collection, $count];
    }

    /**
     * @param $orderId
     * @param $type
     * @param bool $allSources
     * @return string
     */
    protected function generateMessagesForTooltip($orderId, $type, bool $allSources = true): string
    {
        $messagesDb = DB::table('order_messages')->where('order_id', $orderId)->where('type', $type);
        if ($allSources) {
            $messagesDb = $messagesDb->get();
        } else {
            $messagesDb = $messagesDb->where('source', 'FORM')->get();
        }

        $messages = [];
        if (count($messagesDb)) {
            foreach ($messagesDb as $message) {
                $messages[] = $message->message;
            }
        }

        return implode(' ----------- ', $messages);
    }


    /**
     * @return array
     */
    private function getLabelGroupsNames(): array
    {
        return [
            'label_platnosci',
            'label_produkcja',
            'label_transport',
            'label_info_dodatkowe',
            'label_fakury_zakupu',
        ];
    }

}
