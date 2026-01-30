@extends('layouts.admin')

@section('title', 'Contacts Inbox | INDI Lab Admin')

@push('css')
<style>
    .contact-filters {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        align-items: center;
    }
    .filter-btn {
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 0.85rem;
        background: #1a1a1a;
        color: #888;
        border: 1px solid var(--dashboard-border);
        transition: all 0.2s;
    }
    .filter-btn.active {
        background: var(--dashboard-primary);
        color: #fff;
        border-color: var(--dashboard-primary);
    }
    .contact-list-card {
        background: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        overflow: hidden;
    }
    .contact-table {
        width: 100%;
        border-collapse: collapse;
    }
    .contact-table th, .contact-table td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid var(--dashboard-border);
    }
    .contact-table th {
        background: rgba(255,255,255,0.02);
        color: var(--dashboard-text-muted);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .contact-row {
        transition: background 0.2s;
        cursor: pointer;
    }
    .contact-row:hover {
        background: rgba(255,255,255,0.03);
    }
    .contact-row.unread {
        background: rgba(0,123,255,0.02);
    }
    .contact-row.unread td {
        font-weight: 600;
        color: #fff;
    }
    .export-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #28a745;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .export-btn:hover {
        background: #218838;
    }
    .pagination-container {
        padding: 20px;
        display: flex;
        justify-content: center;
    }
</style>
@endpush

@section('admin_title', 'Conversations')
@section('admin_subtitle', 'Manage and respond to all site inquiries')

@section('admin_actions')
<a href="{{ route('admin.contacts.export') }}" class="export-btn">
    <i class="fas fa-file-excel"></i>
    <span>Export Excel</span>
</a>
@endsection

@section('admin_content')
<div class="contact-filters">
    <a href="{{ route('admin.contacts.index') }}" class="filter-btn {{ !$status ? 'active' : '' }}">All ({{ $counts['all'] }})</a>
    <a href="?status=new" class="filter-btn {{ $status == 'new' ? 'active' : '' }}">New ({{ $counts['new'] }})</a>
    <a href="?status=read" class="filter-btn {{ $status == 'read' ? 'active' : '' }}">Read ({{ $counts['read'] }})</a>
    <a href="?status=replied" class="filter-btn {{ $status == 'replied' ? 'active' : '' }}">Replied ({{ $counts['replied'] }})</a>
</div>

<div class="contact-list-card">
    <table class="contact-table">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Origin</th>
                <th>Form Type</th>
                <th>Message Preview</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $contact)
            <tr class="contact-row {{ $contact->status == 'new' ? 'unread' : '' }}" onclick="window.location='{{ route('admin.contacts.show', $contact) }}'">
                <td>
                    <div style="font-weight: 600;">{{ $contact->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--dashboard-text-muted);">{{ $contact->email }}</div>
                </td>
                <td>
                    <div style="font-size: 0.8rem; color: var(--dashboard-text-muted); max-width: 150px; overflow: hidden; text-overflow: ellipsis;" title="{{ $contact->metadata['source_page'] ?? 'Unknown' }}">
                        {{ $contact->metadata['source_page'] ?? 'Unknown' }}
                    </div>
                </td>
                <td>
                    <span style="font-size: 0.75rem; padding: 3px 8px; background: rgba(255,255,255,0.05); border-radius: 4px; text-transform: uppercase;">
                        {{ $contact->form_type }}
                    </span>
                </td>
                <td>
                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.9rem;">
                        {{ $contact->message }}
                    </div>
                </td>
                <td>
                    <div style="font-size: 0.85rem;">{{ $contact->created_at->format('M d, Y') }}</div>
                    <div style="font-size: 0.75rem; color: var(--dashboard-text-muted);">{{ $contact->created_at->diffForHumans() }}</div>
                </td>
                <td>
                    <span class="status-badge {{ $contact->status }}">
                        {{ ucfirst($contact->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 50px; color: var(--dashboard-text-muted);">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px; display: block; opacity: 0.2;"></i>
                    <p>No messages found in this category.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="pagination-container">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
