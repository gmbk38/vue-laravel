<?php

namespace App\Http\Controllers;

use App\Models\Payer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayerController extends MainController
{
    public $requestValidation = [
        // ...
    ];

    public function create(Request $request)
    {
        $this->checkUserAccess($request);

        $validator = Validator::make($request->all(), $this->requestValidation);

        // Если валидация не прошла, возвращаем ошибку
        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $payer = Payer::create([
                // ...
            ]);
            return $this->myResponse(['message' => 'Payer created successfully', 'data' => $payer], 201);
        } catch (\Exception $e) {
            return $this->myResponse(['message' => 'Failed to create payer', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $this->checkUserAccess($request);

        $this->requestValidation['ID'] = 'required|integer';

        $validator = Validator::make($request->all(), $this->requestValidation);

        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // ...

        // Если плательщик не найден, возвращаем ошибку
        if (!$payer) {
            return $this->myResponse(['message' => 'Payer not found'], 404);
        }

        // Обновляем данные плательщика
        try {
            $payer->update([
                // ...
            ]);

            return $this->myResponse(['message' => 'Payer updated successfully', 'data' => $payer], 200);
        } catch (\Exception $e) {
            return $this->myResponse(['message' => 'Failed to update payer', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        $this->checkUserAccess($request);

        $validator = Validator::make($request->all(), [
            'ID' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // ...

        // Если плательщик не найден, возвращаем ошибку
        if (!$payer) {
            return $this->myResponse(['message' => 'Payer not found'], 404);
        }

        // Обновляем данные плательщика
        try {
            $payer->update([
                // ...
            ]);

            return $this->myResponse(['message' => 'Payer updated successfully', 'data' => $payer], 200);
        } catch (\Exception $e) {
            return $this->myResponse(['message' => 'Failed to update payer', 'error' => $e->getMessage()], 500);
        }
    }

    public function load(Request $request)
    {
        $this->checkUserAccess($request);

        // ...

        // Возвращаем плательщиков и устройства пользователя
        return $this->myResponse(['payers' => $payers, 'devices' => $this->devices], 200);
    }
}
