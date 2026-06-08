<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublishSettings\UpdateRequest;
use App\Models\PublishSettings;
use App\Services\Admin\PublishSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublishSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.publish-settings.edit', [
            'settings' => PublishSettings::singleton(),
        ]);
    }

    public function update(UpdateRequest $request, PublishSettingsService $service): RedirectResponse
    {
        $service->update(PublishSettings::singleton(), $request->validated());

        return redirect()
            ->route('admin.publish-settings.edit')
            ->with('success', 'Yayin ayarlari guncellendi.');
    }
}
