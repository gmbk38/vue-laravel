<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Invoice;
use App\Models\InvoiceDevices;
use App\Models\Payer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends MainController
{
    // Расчитываем стоимость и новую дату окончания
    protected function calcDev(&$row, &$totalSum, $period)
    {
        // ...
    }

    // Загружаем список устройств. Если какие-то выбраны, производим для них расчёты
    public function loadDevList(Request $request)
    {
        $this->checkUserAccess($request);

        $this->getDevices();

        if (empty($this->devices)) {
            return $this->myResponse(['message' => 'Data not found'], 404);
        }

        // Устройства + тарифы
        $all_devices = '...';
        $totalSum = 0;

        // Если выбраны какие-то контроллеры, рассчитываем стоимость для них
        if ($request->has('devices')) {
            foreach ($all_devices as $index => $row) {
                if (in_array($row->ID, explode(',', $request->input('devices')))) {
                    $this->calcDev($row, $totalSum, $request->input('period'));
                }
                $all_devices[$index] = $row;
            }
        }

        // ...

        return $this->myResponse(['devices' => $all_devices, 'payers' => $payers, 'total_sum' => $totalSum], 200);
    }

    public function create(Request $request)
    {
        $this->checkUserAccess($request);

        $this->getDevices();

        $validator = Validator::make($request->all(), ['period' => 'required|string']);

        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Нельзя создать пустой invoice
        if ($request->has('devices')) {
            
            // ...

            if (!$payer) {
                return $this->myResponse(['message' => 'Payer not found'], 404);
            }

            if (empty($this->devices)) {
                return $this->myResponse(['message' => 'Data not found'], 404);
            }

            $invoice = Invoice::create([
                // ...
            ]);

            $input_devices = $request->input('devices');
            // Устройства + тарифы
            $all_devices = '...';
            $totalSum = 0;

            // Для каждого выбранного устройства расчитываем стоимость и сохраняем в базу
            foreach ($all_devices as $row) {
                if (in_array($row->ID, explode(',', $input_devices))) {
                    $this->calcDev($row, $totalSum, $request->input('period'));

                    if ($request->input('period') == 'quarter' || $request->input('period') == 'month') {
                        $row->tariff->Article = str_replace('-Y-', '-M-', $row->tariff->Article);
                    }

                    InvoiceDevices::create([
                        // ...
                    ]);
                }
            }

            return $this->myResponse(['invoice' => $invoice, 'message' => 'Invoice created successfully'], 200);
        } else {
            return $this->myResponse(['message' => 'Devices not provided'], 403);
        }
    }

    public function delete(Request $request)
    {
        $this->checkUserAccess($request);

        $validator = Validator::make($request->all(), ['invoiceId' => 'required|integer']);

        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // ...

        if ($invoice->get()->isEmpty()) {
            return $this->myResponse(['message' => 'Invoice not found'], 404);
        }

        try {
            $invoice->update([
                // ...
            ]);

            return $this->myResponse(['message' => 'Invoice updated successfully', 'invoices' => $invoices], 200);
        } catch (\Exception $e) {
            return $this->myResponse(['message' => 'Failed to update invoice', 'error' => $e->getMessage()], 500);
        }
    }

    // Загружаем список созданных invoices
    public function load(Request $request)
    {
        $this->checkUserAccess($request);

        $validator = Validator::make($request->all(), ['invoiceId' => 'required|integer']);

        // ...

        if ($invoices->isEmpty()) {
            return $this->myResponse(['message' => 'Invoice not found'], 404);
        }

        return $this->myResponse(['invoices' => $invoices], 200);
    }

    // Экспорт invoice в pdf
    public function genFile(Request $request)
    {
        $this->checkUserAccess($request);

        $validator = Validator::make($request->all(), ['invoiceId' => 'required|integer']);

        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        } else {
            // ...            
        }

        if (!$invoice) {
            return $this->myResponse(['message' => 'Invoice not found'], 404);
        }

        $html = $this->genHTML(['invoice' => $invoice->toArray()], 'invoice');  // Собираем html код через docgen
        $url = $this->exportFile($html, 'export_invoice_' . $request->input('invoiceId'), 'pdf');  // Генерируем файл, сохраняем в s3 и получаем временную ссылку на скачивание

        return $this->myResponse(['url' => $url, 'invoice' => $invoice], 200);
    }
}
