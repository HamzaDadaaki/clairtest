@extends('layouts.admin', ['title' => 'Edit Partner | Afayar'])

@section('content')
<section class="admin-panel-card form-page-head">
    <div>
        <span class="eyebrow">Partners</span>
        <h1>Edit partner</h1>
        <p>Update the logo, name, link, status, and display order.</p>
    </div>
    <a href="{{ route('admin.partners.index') }}" class="admin-link-button">Back to partners</a>
</section>

<form action="{{ route('admin.partners.update', $partner['id']) }}" method="POST" enctype="multipart/form-data" class="admin-form-grid" data-async-upload-form="partner">
    @csrf
    @method('PUT')

    <section class="admin-panel-card">
        @include('admin.partners._form', ['partner' => $partner, 'isEdit' => true])
    </section>

    <div class="form-actions-sticky">
        <a href="{{ route('admin.partners.index') }}" class="admin-link-button">Cancel</a>
        <button type="submit" class="admin-primary-button">Update partner</button>
    </div>
</form>
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
