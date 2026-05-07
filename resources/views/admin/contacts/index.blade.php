@extends('layouts.admin', ['title' => 'Contact Inbox | Afayar'])

@section('content')
<section class="admin-panel-card dashboard-header-card">
    <div>
        <span class="eyebrow">Inbox</span>
        <h1>Contact messages and emails</h1>
        <p>All messages sent from the website contact page appear here with the sender email, phone, status, and full message content.</p>
    </div>
</section>

<section class="admin-stats-grid four">
    <article class="admin-stat-card"><strong>{{ $stats['total'] }}</strong><span>Total</span></article>
    <article class="admin-stat-card"><strong>{{ $stats['new'] }}</strong><span>New</span></article>
    <article class="admin-stat-card"><strong>{{ $stats['in_progress'] }}</strong><span>In progress</span></article>
    <article class="admin-stat-card"><strong>{{ $stats['replied'] }}</strong><span>Replied</span></article>
</section>

<section class="admin-panel-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $message)
                    <tr>
                        <td>{{ $message['created_at'] }}</td>
                        <td>{{ $message['name'] }}</td>
                        <td>{{ $message['email'] }}</td>
                        <td>{{ $message['subject'] }}</td>
                        <td><span class="status-pill status-{{ $message['status'] }}">{{ str_replace('_', ' ', $message['status']) }}</span></td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.contacts.show', $message['id']) }}" class="admin-primary-button small">Open</a>
                                <a href="mailto:{{ $message['email'] }}?subject={{ rawurlencode('Re: '.$message['subject']) }}" class="admin-link-button small">Reply</a>
                                <form action="{{ route('admin.contacts.destroy', $message['id']) }}" method="POST" onsubmit="return confirm('Delete this message?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-danger-button small">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No contact messages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
