<?php
namespace App\Http\Controllers\Admin\Twscrape;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Twscrape\ExecuteCommandRequest;
use App\Services\Admin\Twscrape\TwscrapeCommandService;

class TwscrapeCommandController extends Controller
{
    public function index()
    {
        return view('admin.twscrape-commands.index');
    }

    public function execute(ExecuteCommandRequest $request, TwscrapeCommandService $service)
    {
        try {
            $result = $service->execute($request->validated()['command']);

            return response()->json([
                'success' => $result['success'],
                'output' => $result['output']
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'output' => 'Laravel hatası: ' . $e->getMessage()
            ], 500);
        }
    }
}
