@extends('layouts.admin', ['title' => 'Manage Partners | Afayar'])

@section('content')
<section class="admin-panel-card dashboard-header-card">
    <div>
        <span class="eyebrow">Partners</span>
        <h1>Partners and logos</h1>
        <p>Add, edit, disable, or remove partners directly from the admin panel. These logos appear on the website collaboration sections.</p>
    </div>
</section>

<section class="admin-grid two">
    <article class="admin-panel-card">
        <div class="admin-section-head">
            <span class="eyebrow">Add partner</span>
            <h2>New partner</h2>
        </div>

        <form action="{{ route('admin.partners.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-grid" data-async-upload-form="partner">
            @csrf
            @include('admin.partners._form', ['partner' => $emptyPartner, 'isEdit' => false])
            <div class="form-actions-sticky">
                <button type="submit" class="admin-primary-button">Save partner</button>
            </div>
        </form>
    </article>

    <article class="admin-panel-card">
        <div class="admin-section-head inline">
            <div>
                <span class="eyebrow">Current partners</span>
                <h2>Logo list</h2>
            </div>
        </div>

        <div class="partner-admin-grid">
            @forelse ($partners as $partner)
                <article class="partner-admin-card align-left">
                    <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }} logo">
                    <strong>{{ $partner['name'] }}</strong>
                    <span>{{ $partner['tagline'] ?: 'No tagline added' }}</span>
                    <small>{{ $partner['website'] ?: 'No link' }}</small>
                    <div class="table-actions full-width-actions">
                        <a href="{{ route('admin.partners.edit', $partner['id']) }}" class="admin-primary-button small">Edit</a>
                        <form action="{{ route('admin.partners.destroy', $partner['id']) }}" method="POST" onsubmit="return confirm('Remove this partner?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-danger-button small">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="empty-state">No partners added yet.</p>
            @endforelse
        </div>
    </article>
</section>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-async-upload-form="partner"]');
    if (!form) return;

    const logoInput = form.querySelector('[data-async-partner-logo]');
    const hiddenInput = form.querySelector('input[name="async_uploaded_logo"]');
    const statusBox = form.querySelector('[data-partner-upload-status]');
    let isSubmitting = false;

    const csrf = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const setStatus = (message, type = 'info') => {
        if (!statusBox) return;
        statusBox.classList.remove('is-error', 'is-success', 'is-working');
        if (type === 'error') statusBox.classList.add('is-error');
        if (type === 'success') statusBox.classList.add('is-success');
        if (type === 'working') statusBox.classList.add('is-working');
        statusBox.innerHTML = `<strong>${type === 'error' ? 'Upload problem.' : type === 'success' ? 'Upload ready.' : 'Preparing upload...'}</strong><p>${message}</p>`;
    };

    form.addEventListener('submit', async (event) => {
        if (isSubmitting || form.dataset.uploadsReady === '1') return;
        if (!logoInput?.files?.length) return;

        event.preventDefault();
        isSubmitting = true;
        setStatus('The selected logo is being uploaded before the partner is saved.', 'working');

        try {
            const data = new FormData();
            data.append('file', logoInput.files[0]);

            const response = await fetch('{{ route('admin.upload.partner-logo') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: data,
                credentials: 'same-origin',
            });

            const result = await response.json().catch(() => ({}));
            if (!response.ok || !result.path) {
                throw new Error(result?.message || result?.errors?.file?.[0] || 'Could not upload the selected logo.');
            }

            if (hiddenInput) hiddenInput.value = result.path;
            if (logoInput) logoInput.value = '';
            form.dataset.uploadsReady = '1';
            setStatus('The logo finished uploading. The partner will be saved now.', 'success');
            form.submit();
        } catch (error) {
            setStatus(error.message || 'The upload failed. Please use a smaller image.', 'error');
            isSubmitting = false;
        }
    });
});
</script>
@endpush
