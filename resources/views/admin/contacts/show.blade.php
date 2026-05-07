@extends('layouts.admin', ['title' => 'View Contact Message | Afayar'])

@section('content')
<section class="admin-panel-card form-page-head">
    <div>
        <span class="eyebrow">Contact lead</span>
        <h1>{{ $message['subject'] }}</h1>
        <p>{{ $message['name'] }} · {{ $message['email'] }} @if($message['phone']) · {{ $message['phone'] }} @endif</p>
    </div>
    <div class="dashboard-actions">
        <a href="mailto:{{ $message['email'] }}?subject={{ rawurlencode('Re: '.$message['subject']) }}" class="admin-primary-button">Reply by email</a>
        <a href="{{ route('admin.contacts.index') }}" class="admin-link-button">Back to inbox</a>
    </div>
</section>

<section class="admin-grid two">
    <article class="admin-panel-card">
        <div class="admin-section-head">
            <span class="eyebrow">Message</span>
            <h2>Client details</h2>
        </div>

        <div class="admin-detail-stack">
            <div><strong>Name:</strong> {{ $message['name'] }}</div>
            <div><strong>Email:</strong> {{ $message['email'] }}</div>
            <div><strong>Phone:</strong> {{ $message['phone'] ?: 'Not provided' }}</div>
            <div><strong>Status:</strong> <span class="status-pill status-{{ $message['status'] }}">{{ str_replace('_', ' ', $message['status']) }}</span></div>
            <div><strong>Source:</strong> {{ $message['source'] }}</div>
            <div><strong>Sent at:</strong> {{ $message['created_at'] }}</div>
            <div><strong>IP:</strong> {{ $message['ip'] ?: 'Unknown' }}</div>
        </div>

        <div class="message-body-card">
            <h3>Full message</h3>
            <p>{!! nl2br(e($message['message'])) !!}</p>
        </div>
    </article>

    <article class="admin-panel-card">
        <div class="admin-section-head">
            <span class="eyebrow">Manage</span>
            <h2>Update status and notes</h2>
        </div>

        <form action="{{ route('admin.contacts.update', $message['id']) }}" method="POST" class="admin-form-grid">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="new" @selected(old('status', $message['status']) === 'new')>New</option>
                    <option value="in_progress" @selected(old('status', $message['status']) === 'in_progress')>In progress</option>
                    <option value="replied" @selected(old('status', $message['status']) === 'replied')>Replied</option>
                    <option value="archived" @selected(old('status', $message['status']) === 'archived')>Archived</option>
                </select>
            </div>

            <div class="form-group">
                <label for="admin_note">Admin note</label>
                <textarea id="admin_note" name="admin_note" rows="8" placeholder="Write follow-up notes here...">{{ old('admin_note', $message['admin_note']) }}</textarea>
            </div>

            <div class="form-actions-sticky">
                <button type="submit" class="admin-primary-button">Save changes</button>
            </div>
        </form>
    </article>
</section>
@endsection
