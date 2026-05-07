@php
    $formProduct = [
        'slug' => old('slug', $product['slug'] ?? ''),
        'name' => old('name', $product['name'] ?? ''),
        'type' => old('type', $product['type'] ?? 'Software'),
        'badge' => old('badge', $product['badge'] ?? ''),
        'price_label' => old('price_label', $product['price_label'] ?? ''),
        'short' => old('short', $product['short'] ?? ''),
        'description' => old('description', $product['description'] ?? ''),
        'hero_note' => old('hero_note', $product['hero_note'] ?? ''),
        'features_text' => old('features_text', implode(PHP_EOL, $product['features'] ?? [])),
        'related_text' => old('related_text', implode(', ', $product['related'] ?? [])),
        'existing_gallery_text' => old('existing_gallery_text', implode(PHP_EOL, $product['gallery'] ?? [])),
        'featured' => filter_var(old('featured', !empty($product['featured'])), FILTER_VALIDATE_BOOL),
        'latest' => filter_var(old('latest', !empty($product['latest'])), FILTER_VALIDATE_BOOL),
    ];

    $oldReviewNames = old('review_name');
    $reviews = is_array($oldReviewNames)
        ? collect($oldReviewNames)->map(function ($name, $index) {
            return [
                'name' => old('review_name.'.$index, ''),
                'role' => old('review_role.'.$index, ''),
                'text' => old('review_text.'.$index, ''),
            ];
        })->all()
        : ($product['review'] ?? [['name' => '', 'role' => '', 'text' => '']]);
@endphp

<form action="{{ $submitRoute }}" method="POST" enctype="multipart/form-data" class="admin-form-grid" data-async-upload-form="product">
    @csrf
    @if ($submitMethod !== 'POST')
        @method($submitMethod)
    @endif

    <section class="admin-panel-card">
        <div class="admin-section-head">
            <h2>Basic information</h2>
            <p>Main information shown in product cards and the product details page.</p>
        </div>

        <input type="hidden" name="async_uploaded_main_image" value="{{ old('async_uploaded_main_image', '') }}">

        <div class="async-upload-box" data-upload-status-box>
            <strong>Smart upload mode is active.</strong>
            <p>When you save, the selected product images are uploaded one by one first, then the product is saved. This avoids the usual <em>POST data is too large</em> error when you add many gallery images.</p>
            <small>Best result: use JPG, PNG, or WEBP images under about 8 MB each.</small>
        </div>

        <div class="admin-grid two">
            <div class="form-group">
                <label for="name">Product name</label>
                <input id="name" type="text" name="name" value="{{ $formProduct['name'] }}" required>
                @error('name')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                <input id="slug" type="text" name="slug" value="{{ $formProduct['slug'] }}" placeholder="auto-generated-from-name">
                @error('slug')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" required>
                    <option value="Software" @selected($formProduct['type'] === 'Software')>Software</option>
                    <option value="Service" @selected($formProduct['type'] === 'Service')>Service</option>
                </select>
                @error('type')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="badge">Badge</label>
                <input id="badge" type="text" name="badge" value="{{ $formProduct['badge'] }}" placeholder="Best seller">
                @error('badge')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="price_label">Price label</label>
                <input id="price_label" type="text" name="price_label" value="{{ $formProduct['price_label'] }}" placeholder="From 1500 MAD / year">
                @error('price_label')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group check-group">
                <label class="check-item"><input type="hidden" name="featured" value="0"><input type="checkbox" name="featured" value="1" @checked($formProduct['featured'])> Featured product</label>
                <label class="check-item"><input type="hidden" name="latest" value="0"><input type="checkbox" name="latest" value="1" @checked($formProduct['latest'])> Latest product</label>
            </div>
        </div>

        <div class="form-group">
            <label for="short">Short description</label>
            <textarea id="short" name="short" rows="3" required>{{ $formProduct['short'] }}</textarea>
            @error('short')<small class="field-error">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label for="description">Full description</label>
            <textarea id="description" name="description" rows="7" required>{{ $formProduct['description'] }}</textarea>
            @error('description')<small class="field-error">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label for="hero_note">Hero note</label>
            <input id="hero_note" type="text" name="hero_note" value="{{ $formProduct['hero_note'] }}" placeholder="Built to help Moroccan businesses sell with confidence.">
            @error('hero_note')<small class="field-error">{{ $message }}</small>@enderror
        </div>
    </section>

    <section class="admin-panel-card">
        <div class="admin-section-head">
            <h2>Images and gallery</h2>
            <p>Upload a main product image and extra gallery pictures. Uploaded files go directly into the public website folder.</p>
        </div>

        @if (!empty($product['image']))
            <div class="current-image-preview">
                <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] ?? 'Product image' }}">
                <span>Current main image</span>
            </div>
        @endif

        <input type="hidden" name="async_uploaded_main_image" value="{{ old('async_uploaded_main_image', '') }}">

        <div class="async-upload-box" data-upload-status-box>
            <strong>Smart upload mode is active.</strong>
            <p>When you save, the selected product images are uploaded one by one first, then the product is saved. This avoids the usual <em>POST data is too large</em> error when you add many gallery images.</p>
            <small>Best result: use JPG, PNG, or WEBP images under about 8 MB each.</small>
        </div>

        <div class="admin-grid two">
            <div class="form-group">
                <label for="image">Main image upload</label>
                <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif,image/*" data-async-main-input>
                <small class="field-hint">Single image upload. Large images are uploaded automatically before the product is saved.</small>
                @error('image')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label for="gallery_images">Add gallery images</label>
                <input id="gallery_images" type="file" name="gallery_images[]" accept=".jpg,.jpeg,.png,.webp,.gif,image/*" multiple data-async-gallery-input>
                <small class="field-hint">You can select multiple gallery images. They are uploaded one by one automatically when you click save.</small>
                @error('gallery_images')<small class="field-error">{{ $message }}</small>@enderror
                @error('gallery_images.*')<small class="field-error">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="form-group">
            <label for="existing_gallery_text">Gallery image paths currently used</label>
            <div class="async-upload-list" data-upload-list></div>
            <textarea id="existing_gallery_text" name="existing_gallery_text" rows="6" placeholder="One image path per line">{{ $formProduct['existing_gallery_text'] }}</textarea>
            <small class="field-hint">Keep existing paths here if you want to preserve them. New uploaded images are added automatically.</small>
            @error('existing_gallery_text')<small class="field-error">{{ $message }}</small>@enderror
        </div>
    </section>

    <section class="admin-panel-card">
        <div class="admin-section-head">
            <h2>Features and relations</h2>
            <p>Each feature should be on a new line. Related products should be entered as slugs separated by commas.</p>
        </div>

        <div class="form-group">
            <label for="features_text">Features</label>
            <textarea id="features_text" name="features_text" rows="7" placeholder="Fast cashier experience&#10;Product, category, and inventory management">{{ $formProduct['features_text'] }}</textarea>
            @error('features_text')<small class="field-error">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label for="related_text">Related product slugs</label>
            <input id="related_text" type="text" name="related_text" value="{{ $formProduct['related_text'] }}" placeholder="afayar-pos, maintenance-support">
            <small class="field-hint">Available slugs: {{ collect($allProducts)->pluck('slug')->implode(', ') }}</small>
            @error('related_text')<small class="field-error">{{ $message }}</small>@enderror
        </div>
    </section>

    <section class="admin-panel-card">
        <div class="admin-section-head inline">
            <div>
                <h2>Reviews</h2>
                <p>Add or remove product reviews that show on the product page.</p>
            </div>
            <button type="button" class="admin-link-button" data-add-review>Add review</button>
        </div>

        <div class="reviews-stack" data-review-stack>
            @foreach ($reviews as $review)
                <div class="review-editor" data-review-row>
                    <div class="admin-grid three">
                        <div class="form-group">
                            <label>Reviewer name</label>
                            <input type="text" name="review_name[]" value="{{ $review['name'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Reviewer role</label>
                            <input type="text" name="review_role[]" value="{{ $review['role'] ?? '' }}">
                        </div>
                        <div class="form-group align-end">
                            <button type="button" class="admin-danger-button small" data-remove-review>Remove</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Review text</label>
                        <textarea name="review_text[]" rows="4">{{ $review['text'] ?? '' }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="form-actions-sticky">
        <a href="{{ route('admin.products.index') }}" class="admin-link-button">Cancel</a>
        <button type="submit" class="admin-primary-button">{{ $submitLabel }}</button>
    </div>
</form>

<template id="review-row-template">
    <div class="review-editor" data-review-row>
        <div class="admin-grid three">
            <div class="form-group">
                <label>Reviewer name</label>
                <input type="text" name="review_name[]">
            </div>
            <div class="form-group">
                <label>Reviewer role</label>
                <input type="text" name="review_role[]">
            </div>
            <div class="form-group align-end">
                <button type="button" class="admin-danger-button small" data-remove-review>Remove</button>
            </div>
        </div>
        <div class="form-group">
            <label>Review text</label>
            <textarea name="review_text[]" rows="4"></textarea>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addButton = document.querySelector('[data-add-review]');
        const stack = document.querySelector('[data-review-stack]');
        const template = document.getElementById('review-row-template');

        const bindRemoveButtons = () => {
            document.querySelectorAll('[data-remove-review]').forEach((button) => {
                button.onclick = () => {
                    const row = button.closest('[data-review-row]');
                    if (!row) return;

                    const rows = document.querySelectorAll('[data-review-row]');
                    if (rows.length === 1) {
                        row.querySelectorAll('input, textarea').forEach((field) => field.value = '');
                        return;
                    }

                    row.remove();
                };
            });
        };

        addButton?.addEventListener('click', () => {
            const clone = template.content.cloneNode(true);
            stack.appendChild(clone);
            bindRemoveButtons();
        });

        bindRemoveButtons();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-async-upload-form="product"]');
        if (!form) return;

        const mainInput = form.querySelector('[data-async-main-input]');
        const galleryInput = form.querySelector('[data-async-gallery-input]');
        const mainPathInput = form.querySelector('input[name="async_uploaded_main_image"]');
        const galleryText = form.querySelector('#existing_gallery_text');
        const statusBox = form.querySelector('[data-upload-status-box]');
        const uploadList = form.querySelector('[data-upload-list]');
        let isSubmitting = false;

        const csrf = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const setStatus = (message, type = 'info') => {
            if (!statusBox) return;
            statusBox.classList.remove('is-error', 'is-success', 'is-working');
            if (type === 'error') statusBox.classList.add('is-error');
            if (type === 'success') statusBox.classList.add('is-success');
            if (type === 'working') statusBox.classList.add('is-working');
            statusBox.innerHTML = `<strong>${type === 'error' ? 'Upload problem.' : type === 'success' ? 'Uploads ready.' : 'Preparing uploads...'}</strong><p>${message}</p>`;
        };

        const appendUploadedPath = (path) => {
            if (!galleryText || !path) return;
            const existing = galleryText.value.split(/\r?\n/).map((item) => item.trim()).filter(Boolean);
            if (!existing.includes(path)) {
                existing.push(path);
                galleryText.value = existing.join('\n');
            }
        };

        const addUploadRow = (label) => {
            if (!uploadList) return null;
            const row = document.createElement('div');
            row.className = 'async-upload-row';
            row.innerHTML = `<span>${label}</span><strong>Waiting...</strong>`;
            uploadList.appendChild(row);
            return row;
        };

        const uploadFile = async (file, endpoint, label) => {
            const row = addUploadRow(label);
            if (row) row.querySelector('strong').textContent = 'Uploading...';

            const data = new FormData();
            data.append('file', file);

            const response = await fetch(endpoint, {
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
                const message = result?.message || result?.errors?.file?.[0] || 'Could not upload one of the selected images.';
                if (row) {
                    row.classList.add('is-error');
                    row.querySelector('strong').textContent = 'Failed';
                }
                throw new Error(message);
            }

            if (row) {
                row.classList.add('is-done');
                row.querySelector('strong').textContent = 'Done';
            }

            return result.path;
        };

        form.addEventListener('submit', async (event) => {
            if (isSubmitting || form.dataset.uploadsReady === '1') {
                return;
            }

            const hasMain = Boolean(mainInput?.files?.length);
            const hasGallery = Boolean(galleryInput?.files?.length);

            if (!hasMain && !hasGallery) {
                return;
            }

            event.preventDefault();
            isSubmitting = true;
            if (uploadList) uploadList.innerHTML = '';
            setStatus('Your selected images are being uploaded one by one before the product is saved.', 'working');

            try {
                if (hasMain) {
                    const path = await uploadFile(mainInput.files[0], '{{ route('admin.upload.product-image') }}', `Main image: ${mainInput.files[0].name}`);
                    if (mainPathInput) mainPathInput.value = path;
                    appendUploadedPath(path);
                }

                if (hasGallery) {
                    for (const file of Array.from(galleryInput.files)) {
                        const path = await uploadFile(file, '{{ route('admin.upload.product-image') }}', `Gallery image: ${file.name}`);
                        appendUploadedPath(path);
                    }
                }

                if (mainInput) mainInput.value = '';
                if (galleryInput) galleryInput.value = '';

                form.dataset.uploadsReady = '1';
                setStatus('All selected images finished uploading. The product will be saved now.', 'success');
                form.submit();
            } catch (error) {
                setStatus(error.message || 'A file upload failed. Please use smaller images or upload fewer files at once.', 'error');
                isSubmitting = false;
            }
        });
    });
</script>
@endpush
