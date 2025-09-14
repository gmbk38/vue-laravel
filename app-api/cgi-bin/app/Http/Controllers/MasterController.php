<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MasterAdmin;
use App\Models\MasterSession;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MasterController extends MainController
{
    private $tariffs = [];
    private $activities = [];
    private $payTypes = [];

    public function createMasterAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|email|max:50',
            'pwd' => 'required|string|min:8|max:20'
        ]);

        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // ...

        if ($adminExists) {
            return $this->myResponse(['message' => 'Admin exists'], 404);
        }

        try {
            $admin = MasterAdmin::create([
                // ...
            ]);

            return $this->myResponse(['message' => 'Admin created successfully', 'data' => $admin], 201);
        } catch (\Exception $e) {
            return $this->myResponse(['message' => 'Failed to create admin', 'error' => $e->getMessage()], 500);
        }
    }

    public function createMasterSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'pwd' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->myResponse(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // ...

        if (!$admin || !$adminCheck) {
            return $this->myResponse(['message' => 'Admin not found'], 404);
        }

        try {
            $session = MasterSession::create([
                // ...
            ]);

            return $this->myResponse(['message' => 'Session created successfully', 'session' => $session->key], 201);
        } catch (\Exception $e) {
            return $this->myResponse(['message' => 'Failed to create session', 'error' => $e->getMessage()], 500);
        }
    }

    public function getMasterDevices(Request $request)
    {
        $this->checkUserAccess($request);

        // Маппинг фильтров на условия запроса
        $filtersToConditions = [
            'all' => null,
            '...' => fn($query) => $query->where('...', true),
            '...' => fn($query) => $query->whereNotNull('...'),
            // ...
        ];

        try {
            // ...

            // Применение фильтра
            if ($request->has('filter') && isset($filtersToConditions[$request->input('filter')])) {
                $filterCondition = $filtersToConditions[$request->input('filter')];
                if (is_callable($filterCondition)) {
                    $filterCondition($query);
                }
            }

            // Полное количество записей (без пагинации и фильтрации)
            $recordsTotal = $query->count();

            // Поиск
            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    // ...
                });
            }

            // Поиски по столбцам
            // ...

            // Индивидуальные поиски по столбцам
            $columnFilters = [
                // ...
            ];

            foreach ($columnFilters as $requestKey => $column) {
                if ($request->has($requestKey) && !empty(trim($request->input($requestKey)))) {
                    // ...
                }
            }

            // Количество записей после поиска
            $recordsFiltered = $query->count();

            // Сортировка
            if ($request->has('orderColumn') && $request->has('orderDir')) {
                $orderColumn = $request->input('orderColumn');
                $orderDir = $request->input('orderDir');

                $columns = [
                    // ...
                ];

                if (isset($columns[$orderColumn])) {
                    $query->orderBy($columns[$orderColumn], $orderDir);
                }
            }

            // Пагинация
            if ($request->has('start') && $request->has('length')) {
                $query
                    ->offset($request->input('start'))
                    ->limit($request->input('length'));
            }

            // Получение устройств с владельцами
            $devices = $query->get();

            // Получаем ID устройств для загрузки пользователей
            $deviceIds = $devices->pluck('ID')->unique();

            // Загружаем всех пользователей для этих устройств
            $allUsers = '...';

            // Добавляем пользователей к каждому устройству
            $devices->transform(function ($device) use ($allUsers) {
                $device->AllUsers = $allUsers
                    ->get($device->ID, collect())
                    ->map(function ($user) {
                        return [
                            'Level' => $user->Level,
                            'ID' => $user->ID,
                            'Email' => $user->Email,
                            'Name' => $user->Name
                        ];
                    })
                    ->values()
                    ->toArray();
                return $device;
            });

            // Преобразуем в массив
            $data = $devices->map(function ($item) {
                return (array) $item;
            })->toArray();

            return $this->myResponse([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'message' => 'Success',
            ], 200);
        } catch (\Exception $e) {
            return $this->myResponse([
                'message' => 'Database error: ' . $e->getMessage(),
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ], 500);
        }
    }

    public function updateMasterDevice(Request $request)
    {
        $this->checkUserAccess($request);

        $deviceData = $request->input('device');

        // ...

        if (!$device) {
            return $this->myResponse(['message' => 'Device not found'], 404);
        }

        try {
            $device->update([
                // ...
            ]);
        } catch (\Exception $e) {
            abort($this->myResponse(['message' => 'Could not connect to MySQL', 'error' => $e], 500));
        }
        return $this->myResponse(['message' => 'Success', 'devices' => $device], 200);
    }

    public function updateMasterDevicesGroup(Request $request)
    {
        $this->checkUserAccess($request);

        // ...

        return $this->myResponse(['message' => 'Devices updated successfully'], 200);
    }
}
