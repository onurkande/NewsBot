<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PoolSetting\UpdateRequest;
use App\Models\PoolSetting;
use App\Services\Admin\PoolSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PoolSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.pool-settings.edit', [
            'settings' => PoolSetting::singleton(),
        ]);
    }

    public function update(UpdateRequest $request, PoolSettingService $service): RedirectResponse
    {
        $service->update(PoolSetting::singleton(), $request->validated());

        return redirect()
            ->route('admin.pool-settings.edit')
            ->with('success', 'Havuz ayarlari guncellendi.');
    }
}
