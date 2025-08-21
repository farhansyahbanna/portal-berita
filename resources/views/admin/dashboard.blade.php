@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div id="admin-app">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600">Selamat datang di panel admin Portal Berita</p>
    </div>

    <admin-dashboard
        :stats='@json($stats)'
        :recent_posts='@json($recent_posts)'
        :recent_comments='@json($recent_comments)'
    ></admin-dashboard>
</div>
@endsection

@push('scripts')
    @vite('resources/js/admin.js')
@endpush