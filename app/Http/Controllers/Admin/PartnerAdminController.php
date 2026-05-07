<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PartnerStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PartnerAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.partners.index', [
            'partners' => PartnerStore::all(),
            'emptyPartner' => $this->emptyPartner(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $partner = $this->buildPayload($request, $data);
        PartnerStore::upsert($partner);

        return redirect()->route('admin.partners.index')->with('success', 'Partner added successfully.');
    }

    public function edit(string $id): View
    {
        $partner = PartnerStore::find($id);
        abort_if($partner === null, 404);

        return view('admin.partners.edit', [
            'partner' => $partner,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $existing = PartnerStore::find($id);
        abort_if($existing === null, 404);

        $data = $this->validatedData($request);
        $partner = $this->buildPayload($request, $data, $existing);
        PartnerStore::upsert($partner, $id);

        return redirect()->route('admin.partners.index')->with('success', 'Partner updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $existing = PartnerStore::find($id);
        abort_if($existing === null, 404);

        PartnerStore::delete($id);

        return redirect()->route('admin.partners.index')->with('success', 'Partner removed successfully.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'website' => ['nullable', 'url', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:6144'],
            'existing_logo' => ['nullable', 'string', 'max:255'],
            'async_uploaded_logo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    protected function buildPayload(Request $request, array $data, array $existing = []): array
    {
        $logo = trim((string) ($data['async_uploaded_logo'] ?? ''));

        if ($logo === '') {
            $logo = trim((string) ($data['existing_logo'] ?? ($existing['logo'] ?? 'assets/images/logo.png')));
        }

        if ($request->hasFile('logo')) {
            $logo = $this->storeUploadedLogo($request->file('logo'));
        }

        return [
            'id' => $existing['id'] ?? null,
            'name' => $data['name'],
            'website' => $data['website'] ?? '',
            'tagline' => $data['tagline'] ?? '',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'logo' => $logo,
            'is_active' => $request->boolean('is_active', $existing['is_active'] ?? true),
        ];
    }

    protected function storeUploadedLogo($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'webp');
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = $safeName !== '' ? $safeName : 'partner-logo';
        $filename = now()->format('YmdHis').'-'.Str::random(8).'-'.$safeName.'.'.$extension;
        $destination = public_path('assets/images/partners/uploads');

        if (! is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $file->move($destination, $filename);

        return 'assets/images/partners/uploads/'.$filename;
    }

    protected function emptyPartner(): array
    {
        return [
            'id' => '',
            'name' => '',
            'website' => '',
            'tagline' => '',
            'sort_order' => 0,
            'logo' => '',
            'is_active' => true,
        ];
    }
}
