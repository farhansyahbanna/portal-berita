@extends('layout')

@section('title', $post->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <article class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
            
            <div class="flex items-center text-sm text-gray-600 mb-6">
                <span>{{ $post->published_at->format('d M Y, H:i') }}</span>
                <span class="mx-2">•</span>
                <span>Oleh: {{ $post->user->name }}</span>
            </div>

            <div class="prose prose-lg max-w-none text-gray-800">
                {!! $post->content !!}
            </div>
        </div>
    </article>

    <section class="bg-white rounded-lg shadow-lg p-6">
        <div id="app">
            <comment-list :post-id="{{ $post->id }}"></comment-list>
            <comment-form 
                :post-id="{{ $post->id }}" 
                @comment-added="refreshComments"
            ></comment-form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
// Function untuk refresh comments
function refreshComments() {
    // Trigger event untuk Vue component
    if (typeof window !== 'undefined' && window.Vue) {
        window.Vue.emit('refresh-comments');
    }
}
</script>
@vite('resources/js/app.js')
@endpush