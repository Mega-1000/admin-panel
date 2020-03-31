<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Jobs\Orders\ChangeWarehouseStockJob;
use App\Mail\ConfirmData;
use App\Mail\DifferentCustomerData;
use App\Repositories\LabelRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemoveLabelJob extends Job
{
    protected $order;
    protected $labelIdsToRemove;
    protected $loopPreventionArray;
    protected $customLabelIdsToAddAfterRemoval = [];

    /**
     * RemoveLabelJob constructor.
     * @param $order
     * @param $labelIdsToRemove
     * @param $loopPreventionArray
     * @param $customLabelIdsToAddAfterRemoval
     */
    public function __construct($order, $labelIdsToRemove, &$loopPreventionArray = [], $customLabelIdsToAddAfterRemoval = [])
    {
        $this->order                           = $order;
        $this->labelIdsToRemove                = $labelIdsToRemove;
        $this->loopPreventionArray             = $loopPreventionArray;
        if (is_array($customLabelIdsToAddAfterRemoval)) {
            $this->customLabelIdsToAddAfterRemoval = $customLabelIdsToAddAfterRemoval;
        } else {
            array_push($this->customLabelIdsToAddAfterRemoval, $customLabelIdsToAddAfterRemoval);
        }
    }

    /**
     * Execute the job.
     *
     */
    public function handle(OrderRepository $orderRepository, LabelRepository $labelRepository)
    {
        if (!($this->order instanceof Order)) {
            $this->order = $orderRepository->find($this->order);
        }

        if (count($this->labelIdsToRemove) < 1) {
            return;
        }

        foreach ($this->labelIdsToRemove as $labelId) {
            if (!empty($this->loopPreventionArray['already-removed']) && in_array($labelId, $this->loopPreventionArray['already-removed'])) {
                continue;
            }

            if ($labelId == 49 && Auth::user()->role_id == 4) {
                continue;
            }

            if ($labelId == 41) {
                $noData = DB::table('gt_invoices')->where('order_id', $this->order->id)->where('gt_invoice_status_id', '13')->first();
                if (!empty($noData)) {
                    try {
                        \Mailer::create()
                            ->to($this->order->customer->login)
                            ->send(new DifferentCustomerData('Wybór danych do wystawienia faktury - zlecenie'.$this->order->id, $this->order->id, $noData->id));
                    } catch (\Swift_TransportException $e) {

                    }
                } else {
                    try {
                        \Mailer::create()
                            ->to($this->order->customer->login)
                            ->send(new ConfirmData('Wybór danych do wystawienia faktury  - zlecenie'.$this->order->id, $this->order->id));
                    } catch (\Swift_TransportException $e) {

                    }
                }
            }

            $label = $labelRepository->find($labelId);

            if ($label->manual_label_selection_to_add_after_removal) {
                $labelIdsToAttach = $this->customLabelIdsToAddAfterRemoval;
            } else {
                $labelIdsToAttach = [];
                foreach ($label->labelsToAddAfterRemoval as $item) {
                    $labelIdsToAttach[] = $item->id;
                    if ($item->id == 50) {
                        $response = dispatch_now(new ChangeWarehouseStockJob($this->order));
                        if (strlen((string) $response) > 0) {
                            return $response;
                        } else {
                            $this->order->labels()->detach($label);
                            $this->loopPreventionArray['already-removed'][] = $labelId;

                            //attaching labels to add after removal
                            if (count($labelIdsToAttach) > 0) {
                                dispatch_now(new AddLabelJob($this->order, $labelIdsToAttach, $this->loopPreventionArray));
                            }

                            //detaching labels to remove after removal
                            if (count($label->labelsToRemoveAfterRemoval) > 0) {
                                $labelIdsToDetach = [];
                                foreach ($label->labelsToRemoveAfterRemoval as $item) {
                                    $labelIdsToDetach[] = $item->id;
                                }
                                dispatch_now(new RemoveLabelJob($this->order, $labelIdsToDetach, $this->loopPreventionArray));
                            }
                        }
                    }
                }
            }

            $this->order->labels()->detach($label);
            $this->loopPreventionArray['already-removed'][] = $labelId;

            //attaching labels to add after removal
            if (count($labelIdsToAttach) > 0) {
                dispatch_now(new AddLabelJob($this->order, $labelIdsToAttach, $this->loopPreventionArray));
            }

            //detaching labels to remove after removal
            if (count($label->labelsToRemoveAfterRemoval) > 0) {
                $labelIdsToDetach = [];
                foreach ($label->labelsToRemoveAfterRemoval as $item) {
                    $labelIdsToDetach[] = $item->id;
                }
                dispatch_now(new RemoveLabelJob($this->order, $labelIdsToDetach, $this->loopPreventionArray));
            }
        }
    }
}
