@extends('layouts.app')

@section('title', isset($article) ? "Modifier l'article" : 'Nouvel article')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <a href="{{ route('admin.blog.index') }}" class="text-sm text-gray-500 dark:text-dark-muted hover:text-indigo-600 dark:hover:text-indigo-400 transition">&larr; Retour</a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-dark-text">{{ isset($article) ? "Modifier l'article" : 'Nouvel article' }}</h1>

        <form method="POST" action="{{ isset($article) ? route('admin.blog.update', $article) : route('admin.blog.store') }}" class="mt-8 space-y-5">
            @csrf
            @if(isset($article)) @method('PUT') @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Titre</label>
                <input type="text" name="title" id="title" value="{{ old('title', $article->title ?? '') }}" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('title') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Extrait</label>
                <textarea name="excerpt" id="excerpt" rows="2"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                @error('excerpt') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div x-data="markdownEditor()">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Contenu (Markdown)</label>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="tab = 'write'"
                            class="text-xs px-3 py-1 rounded transition"
                            :class="tab === 'write' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-400 dark:text-dark-muted hover:text-gray-600 dark:hover:text-dark-text'">
                            Écrire
                        </button>
                        <button type="button" @click="preview()"
                            class="text-xs px-3 py-1 rounded transition"
                            :class="tab === 'preview' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-400 dark:text-dark-muted hover:text-gray-600 dark:hover:text-dark-text'">
                            Aperçu
                        </button>
                    </div>
                </div>

                <div x-show="tab === 'write'" class="mt-2">
                    <div class="flex flex-wrap gap-1 mb-2 p-1.5 bg-gray-50 dark:bg-dark-border/50 rounded-lg border border-gray-200 dark:border-dark-border">
                        <button type="button" @click="insert('**', '**')" class="px-2 py-1 text-xs font-bold text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Gras">B</button>
                        <button type="button" @click="insert('*', '*')" class="px-2 py-1 text-xs italic text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Italique">I</button>
                        <span class="w-px bg-gray-200 dark:bg-dark-border mx-1"></span>
                        <button type="button" @click="insert('### ', '')" class="px-2 py-1 text-xs text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Titre">H</button>
                        <button type="button" @click="insert('- ', '')" class="px-2 py-1 text-xs text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Liste">List</button>
                        <span class="w-px bg-gray-200 dark:bg-dark-border mx-1"></span>
                        <button type="button" @click="insert('[texte](url)', '')" class="px-2 py-1 text-xs text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Lien">&#128279;</button>
                        <button type="button" @click="uploadImage()" class="px-2 py-1 text-xs text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Image">&#128247;</button>
                        <button type="button" @click="insert('```\n', '\n```')" class="px-2 py-1 text-xs text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Code">&lt;/&gt;</button>
                        <button type="button" @click="insert('> ', '')" class="px-2 py-1 text-xs text-gray-600 dark:text-dark-muted hover:bg-gray-200 dark:hover:bg-dark-border rounded" title="Citation">Quote</button>
                    </div>
                    <textarea name="content" id="content" rows="20" required x-ref="textarea"
                        x-on:input="previewHtml = null"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono leading-relaxed">{{ old('content', $article->content ?? '') }}</textarea>
                </div>

                <div x-show="tab === 'preview'" class="mt-2">
                    <div class="min-h-[300px] p-4 rounded-lg border border-gray-200 dark:border-dark-border bg-white dark:bg-dark-card prose prose-gray dark:prose-invert max-w-none text-sm">
                        <template x-if="previewLoading">
                            <p class="text-gray-400 dark:text-dark-muted">Chargement de l'aperçu...</p>
                        </template>
                        <template x-if="!previewLoading && previewHtml">
                            <div x-html="previewHtml"></div>
                        </template>
                        <template x-if="!previewLoading && !previewHtml">
                            <p class="text-gray-400 dark:text-dark-muted">Cliquez sur "Aperçu" pour voir le rendu.</p>
                        </template>
                    </div>
                </div>

                <input type="file" id="image-upload" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" @change="handleImageUpload($event)">
            </div>

            <div>
                <label for="image_url" class="block text-sm font-medium text-gray-700 dark:text-dark-text">Image URL (couverture)</label>
                <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $article->image_url ?? '') }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-dark-border dark:bg-dark-card dark:text-dark-text px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                @error('image_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" value="1" id="is_published"
                    {{ old('is_published', $article->is_published ?? false) ? 'checked' : '' }}
                    class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                <label for="is_published" class="text-sm text-gray-700 dark:text-dark-text">Publier</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-dark-text">Tags</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-1.5 text-sm text-gray-600 dark:text-dark-muted">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ isset($article) && $article->tags->contains($tag->id) ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-dark-border text-indigo-600 focus:ring-indigo-500">
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <x-button type="submit" variant="primary" size="lg" loading-text="Enregistrement...">
                {{ isset($article) ? 'Mettre à jour' : "Créer l'article" }}
            </x-button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function markdownEditor() {
        return {
            tab: 'write',
            previewHtml: null,
            previewLoading: false,
            insert(before, after) {
                const ta = this.$refs.textarea;
                const start = ta.selectionStart;
                const end = ta.selectionEnd;
                const text = ta.value;
                const selected = text.substring(start, end);
                ta.value = text.substring(0, start) + before + selected + after + text.substring(end);
                ta.selectionStart = ta.selectionEnd = start + before.length + selected.length + after.length;
                ta.dispatchEvent(new Event('input'));
                ta.focus();
            },
            async preview() {
                this.tab = 'preview';
                this.previewLoading = true;
                const content = this.$refs.textarea.value;
                try {
                    const res = await fetch('{{ route("admin.blog.preview") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ content }),
                    });
                    const data = await res.json();
                    this.previewHtml = data.html;
                } catch {
                    this.previewHtml = '<p class="text-red-500">Erreur de prévisualisation.</p>';
                }
                this.previewLoading = false;
            },
            uploadImage() {
                document.getElementById('image-upload').click();
            },
            async handleImageUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                const formData = new FormData();
                formData.append('image', file);
                try {
                    const res = await fetch('{{ route("admin.blog.images.upload") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });
                    const data = await res.json();
                    this.insert('![](' + data.url + ')', '');
                } catch {
                    alert("Erreur lors de l'upload de l'image.");
                }
                event.target.value = '';
            }
        };
    }
</script>
@endpush
