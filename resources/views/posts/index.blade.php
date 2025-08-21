@extends('layout')

@section('title', 'Berita Terbaru')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Daftar Artikel</h1>
        <p class="text-xl text-gray-600">Temukan berita dan artikel terbaru dari berbagai dunia</p>
    </div>

    <div id="app">
        <post-list :posts='@json($posts)'></post-list>
        
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('js/app.js') }}"></script>
@endpush