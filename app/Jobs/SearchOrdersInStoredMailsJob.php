<?php

namespace App\Jobs;

use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use PhpMimeMailParser\Parser;

class SearchOrdersInStoredMailsJob extends Job
{
    protected const STORAGE_MAILS_NAME = "app/order-mails";

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        Parser $messageParser,
        OrderMessageRepository $messageRepository,
        OrderPackageRepository $orderPackageRepository,
        OrderRepository $orderRepository
    ) {
        $filesToProceed = array_diff(
            scandir(storage_path(self::STORAGE_MAILS_NAME)),
            ['.', '..', '.gitignore', 'proceeded', 'not-found']
        );

        if (empty($filesToProceed)) {
            return;
        }
        foreach ($filesToProceed as $file) {
            $found = false;
            $currentFilePath = storage_path(self::STORAGE_MAILS_NAME) . DIRECTORY_SEPARATOR . $file;
            $mail = $messageParser->setPath($currentFilePath);
            $dateFolderName = (new \DateTime())->format("Y-m-d");
            $mailSubject = $mail->getHeader('subject');
            $mailContent = empty($mail->getMessageBody('text')) ? "!! NIE ODCZYTANO ZAWARTOŚCI MAILA !!" : $mail->getMessageBody('text');

            $data = [
                'title' => $mailSubject,
                'message' => $mailContent,
                'source' => 'MAIL',
                'status' => 'OPEN',
                'type' => 'GENERAL',
            ];

            $re = '/\s(\d+)\s/i';
            preg_match_all($re, $mailSubject, $matches, PREG_SET_ORDER, 0);
            if (!empty($matches)) {     //found some matching number
                $number = $matches[0][1];
                $orderPackage = $orderPackageRepository->scopeQuery(function ($query) use ($number) {
                    return $query->where("sending_number", $number)
                        ->orWhere("letter_number", $number);
                })->first();
                if (!empty($orderPackage)) {        //found that number as either sending_number or as letter_number
                    $found = true;
                    $data['type'] = "SHIPPING";
                    $data['order_id'] = $orderPackage->order_id;
                } else {
                    $order = $orderRepository->findWhere(["id" => $number])->first();       //found as order id
                    if (!empty($order)) {
                        $found = true;
                        $data['order_id'] = $order->id;
                    }
                }
            }

            if ($found) {
                $messageRepository->create($data);
                $proceededDateFolderName = $this->createDateFolderName($dateFolderName, 'proceeded');
                rename($currentFilePath, $proceededDateFolderName . DIRECTORY_SEPARATOR . $file);
            } else {
                $notFoundFolderName = $this->createDateFolderName($dateFolderName, 'not-found');
                rename($currentFilePath, $notFoundFolderName . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    protected function createDateFolderName($dateFolderName, $name)
    {
        $dirName = storage_path(self::STORAGE_MAILS_NAME . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $dateFolderName);

        if (!file_exists($dirName)) {
            mkdir($dirName);
        }

        return $dirName;
    }
}
