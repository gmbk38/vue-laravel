<?php

namespace App\Http\Controllers;

use App\Models\MasterSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    protected $userId = '';
    protected $adminId = '';
    protected $clientCode = '';
    protected $devices = [];

    protected function checkUserAccess(Request $request)
    {
        // Если админ, пропускаем проверку пользователя
        if ($this->checkMasterAccess($request)) {
            return;
        }

        // ...
    }

    protected function checkMasterAccess(Request $request)
    {
        // ...
    }

    // Получаем массив DevID устройств
    // HINT: Лучше сделать ассоциативный массив
    protected function getDevices(int $levelAccess = 90)
    {
        // ...
    }

    protected function myResponse(array $data, int $code)
    {
        return response()->json($data, $code);
    }

    // Генерация HTML кода из tpl шаблонов через docgen
    protected function genHTML(array $jsonData, string $docType)
    {
        // Адрес контейнера docgen
        $url = env('DOCGEN_HOST');

        // Задаём тип документа(шаблон), который будет использоваться генератором для обработки данных
        $jsonData['docType'] = $docType;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: text/html'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // docgen возвращает html
        $html = curl_exec($ch);

        return $html;
    }

    // Экспорт файла на базе s3
    protected function exportFile(string $body, string $fileName, string $extension, array $exportOptions = [])
    {
        $fileName .= '.' . $extension;
        $subPath = $this->userId . '/';
        $fullPath = $subPath . $fileName;

        // Если пользователь уже генерировал этот документ, возвращаем ссылку на скачивание
        if (Storage::disk('s3')->exists($fullPath)) {
            return Storage::disk('s3')->temporaryUrl($fullPath, now()->addMinutes(30));
        }

        if ($extension != 'pdf') {
            Storage::disk('s3')->put($fullPath, $body);

            if (!Storage::disk('s3')->exists($fullPath)) {
                abort(response()->json(['message' => 'Could not save file into S3'], 500));
            }

            return Storage::disk('s3')->temporaryUrl($fullPath, now()->addMinutes(30));
        } else {
            $uploadUrl = Storage::disk('s3')->temporaryUploadUrl($fullPath, now()->addMinutes(30))['url'];

            // Настройки pdf файла
            $options = [
                'page-size' => isset($exportOptions['page-size']) ? $exportOptions['page-size'] : 'A4',
                'orientation' => isset($exportOptions['orientation']) ? $exportOptions['orientation'] : 'Portrait',
                'margin-top' => isset($exportOptions['margin-top']) ? $exportOptions['margin-top'] : '10',
                'margin-bottom' => isset($exportOptions['margin-bottom']) ? $exportOptions['margin-bottom'] : '10',
                'margin-right' => isset($exportOptions['margin-right']) ? $exportOptions['margin-right'] : '10',
                'margin-left' => isset($exportOptions['margin-left']) ? $exportOptions['margin-left'] : '10',
                'dpi' => isset($exportOptions['dpi']) ? $exportOptions['dpi'] : '150',
                'encoding' => isset($exportOptions['encoding']) ? $exportOptions['encoding'] : 'UTF-8'
            ];

            // Конвертация кодировки
            $body = mb_convert_encoding($body, 'UTF-8', mb_detect_encoding($body));
            if (strpos($body, 'charset=') === false) {
                $body = str_replace('<head>', '<head><meta charset="UTF-8">', $body);
            }

            $jsonData = [
                'contents' => base64_encode($body),
                'upload_url' => $uploadUrl,
                'options' => $options
            ];

            // Адрес генератора pdf
            $pdfgenUrl = env('PDF_GEN_SERVICE');

            // Отправляем запрос синхронно
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($pdfgenUrl, $jsonData);

            // Проверяем статус ответа
            if (!$response->successful()) {
                abort(response()->json([
                    'message' => 'PDF generation failed',
                    'error' => $response->body()
                ], 500));
            }

            // Проверяем существование файла после успешного ответа
            if (!Storage::disk('s3')->exists($fullPath)) {
                abort(response()->json(['message' => 'PDF was not uploaded to S3'], 500));
            }

            return Storage::disk('s3')->temporaryUrl($fullPath, now()->addMinutes(30));
        }
    }
}
