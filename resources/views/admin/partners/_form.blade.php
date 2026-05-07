<input type="hidden" name="async_uploaded_logo" value="{{ old('async_uploaded_logo', $partner['logo'] ?? '') }}">
<div class="async-upload-box compact" data-partner-upload-status>
    <strong>Smart logo upload is active.</strong>
    <p>The logo is uploaded first, then the partner is saved. This helps avoid large POST errors.</p>
</div>

<div class="admin-grid two">
    <div class="form-group">
        <label for="name">Partner name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $partner['name'] ?? '') }}" required>
        @error('name')<small class="field-error">{{ $message }}</small>@enderror
    </div>

    <div class="form-group">
        <label for="website">Website link</label>
        <input id="website" type="text" name="website" value="{{ old('website', $partner['website'] ?? '') }}" placeholder="https://example.com">
        @error('website')<small class="field-error">{{ $message }}</small>@enderror
    </div>
</div>

<div class="admin-grid two">
    <div class="form-group">
        <label for="tagline">Tagline</label>
        <input id="tagline" type="text" name="tagline" value="{{ old('tagline', $partner['tagline'] ?? '') }}" placeholder="Brand partner or short note">
        @error('tagline')<small class="field-error">{{ $message }}</small>@enderror
    </div>

    <div class="form-group">
        <label for="sort_order">Display order</label>
        <input id="sort_order" type="text" name="sort_order" value="{{ old('sort_order', $partner['sort_order'] ?? 0) }}">
        @error('sort_order')<small class="field-error">{{ $message }}</small>@enderror
    </div>
</div>

@if (! empty($partner['logo']))
    <div class="current-image-preview">
        <span>Current logo</span>
        <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] ?? 'Partner' }} logo">
    </div>
@endif

<input type="hidden" name="existing_logo" value="{{ old('existing_logo', $partner['logo'] ?? '') }}">

<div class="admin-grid two">
    <div class="form-group">
        <label for="logo">Upload logo</label>
        <input id="logo" type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.gif,.svg" data-async-partner-logo>
        <small class="field-hint">Recommended transparent PNG or WEBP.</small>
        @error('logo')<small class="field-error">{{ $message }}</small>@enderror
    </div>

    <div class="form-group check-group">
        <label class="check-item">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $partner['is_active'] ?? true))>
            <span>Show this partner on the website</span>
        </label>
    </div>
</div>
