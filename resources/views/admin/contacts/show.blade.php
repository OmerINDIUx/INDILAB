@extends('layouts.admin')

@section('title', 'View Message | INDI Lab Admin')

@push('css')
<style>
    .message-container {
        background: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        padding: 40px;
        max-width: 800px;
        margin: 0 auto;
    }
    .message-header {
        border-bottom: 1px solid var(--dashboard-border);
        padding-bottom: 30px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .message-meta h2 {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    .message-meta p {
        color: var(--dashboard-text-muted);
        font-size: 0.9rem;
    }
    .message-body {
        line-height: 1.6;
        font-size: 1.1rem;
        white-space: pre-wrap;
    }
    .message-details {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid var(--dashboard-border);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .detail-item label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--dashboard-text-muted);
        margin-bottom: 5px;
        letter-spacing: 0.05em;
    }
    .detail-item span {
        font-weight: 500;
    }
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--dashboard-text-muted);
        text-decoration: none;
        margin-bottom: 20px;
        font-size: 0.9rem;
        transition: color 0.2s;
    }
    .back-btn:hover {
        color: #fff;
    }
</style>
@endpush

@section('admin_title', 'View Message')
@section('admin_subtitle', 'Message from ' . $contact->name)

@section('admin_content')
<a href="{{ route('admin.contacts.index') }}" class="back-btn">
    <i class="fas fa-arrow-left"></i>
    Back to Inbox
</a>

<div class="message-container">
    <div class="message-header">
        <div class="message-meta">
            <h2>{{ $contact->name }}</h2>
            <p>{{ $contact->email }}</p>
        </div>
        <div class="message-status">
            <span class="status-badge {{ $contact->status }}">
                {{ ucfirst($contact->status) }}
            </span>
        </div>
    </div>

    <div class="message-body">
        {{ $contact->message }}
    </div>

    <div class="message-details">
        <div class="detail-item">
            <label>Date Received</label>
            <span>{{ $contact->created_at->format('F d, Y \a\t H:i') }}</span>
        </div>
        <div class="detail-item">
            <label>Form Type</label>
            <span>{{ ucfirst($contact->form_type) }}</span>
        </div>
        <div class="detail-item">
            <label>Source Page</label>
            <span>{{ $contact->metadata['source_page'] ?? 'Direct / Unknown' }}</span>
        </div>
        @if($contact->phone)
        <div class="detail-item">
            <label>Phone</label>
            <span>{{ $contact->phone }}</span>
        </div>
        @endif
        <div class="detail-item">
            <label>IP Address</label>
            <span>{{ $contact->ip_address }}</span>
        </div>
    </div>

    <div style="margin-top: 40px; display: flex; gap: 15px;">
        <a href="mailto:{{ $contact->email }}" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; background: var(--dashboard-primary); color: #fff; padding: 12px 25px; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-reply"></i>
            Reply via Email
        </a>
    </div>
</div>
@endsection
