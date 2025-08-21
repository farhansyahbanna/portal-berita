@extends('admin.layout')

@section('title', 'Manajemen Komentar')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Komentar</h1>
        <p class="text-gray-600">Kelola semua komentar dari pengguna</p>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Daftar Komentar</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($comments as $comment)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-md">
                                {{ Str::limit($comment->content, 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <a href="{{ route('posts.show', $comment->post_id) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    {{ Str::limit($comment->post->title, 50) }}
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $comment->name }}</div>
                            <div class="text-sm text-gray-500">{{ $comment->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $comment->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button 
                                onclick="confirmDelete({{ $comment->id }})" 
                                class="text-red-600 hover:text-red-900"
                            >
                                Hapus
                            </button>
                            <form id="delete-form-{{ $comment->id }}" 
                                  action="{{ route('admin.comments.destroy', $comment) }}" 
                                  method="POST" 
                                  class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada komentar yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $comments->firstItem() }} hingga {{ $comments->lastItem() }} dari {{ $comments->total() }} hasil
                </div>
                <div class="flex space-x-2">
                    @if($comments->onFirstPage())
                    <span class="px-3 py-1 rounded-md bg-gray-200 text-gray-400 cursor-not-allowed">Sebelumnya</span>
                    @else
                    <a href="{{ $comments->previousPageUrl() }}" class="px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700">Sebelumnya</a>
                    @endif

                    @if($comments->hasMorePages())
                    <a href="{{ $comments->nextPageUrl() }}" class="px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-700">Selanjutnya</a>
                    @else
                    <span class="px-3 py-1 rounded-md bg-gray-200 text-gray-400 cursor-not-allowed">Selanjutnya</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function confirmDelete(commentId) {
    if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
        document.getElementById('delete-form-' + commentId).submit();
    }
}
</script>
@endsection