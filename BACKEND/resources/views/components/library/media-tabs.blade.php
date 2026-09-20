@props([
    'entity',
    'type',
    'showHeader' => true,
])

@php
    $counts = $entity->mediaCounts();
    $tabId = 'lib-' . $type . '-' . $entity->uuid;
    $description = $entity->description ?? '';
@endphp

<div class="library-media-tabs" data-library-type="{{ $type }}" data-library-uuid="{{ $entity->uuid }}">
    @if ($showHeader)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0">{{ $entity->name ?? 'Untitled' }}</h5>
                <small class="text-muted">{{ ucfirst(rtrim($type, 's')) }}</small>
            </div>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#{{ $tabId }}-description">
                <i class="mdi mdi-text-box-outline me-1"></i> Description
                <span class="badge bg-secondary ms-1">{{ $description ? 1 : 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#{{ $tabId }}-gallery">
                <i class="mdi mdi-image-multiple-outline me-1"></i> Images
                <span class="badge bg-success ms-1" data-count="gallery">{{ $counts['gallery'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#{{ $tabId }}-cover">
                <i class="mdi mdi-image-frame me-1"></i> Covers
                <span class="badge bg-info ms-1" data-count="cover">{{ $counts['covers'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#{{ $tabId }}-video">
                <i class="mdi mdi-video-outline me-1"></i> Videos
                <span class="badge bg-warning ms-1" data-count="video">{{ $counts['videos'] }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- DESCRIPTION TAB --}}
        <div class="tab-pane show active" id="{{ $tabId }}-description">
            <textarea class="form-control mb-2" rows="6" data-library-description
                placeholder="Describe this {{ rtrim($type, 's') }} once — it will auto-fill on every quote that uses it.">{{ $description }}</textarea>
            <button type="button" class="btn btn-primary btn-sm" data-library-save-description>
                <i class="mdi mdi-content-save"></i> Save Description
            </button>
            <span class="ms-2 text-muted small" data-library-description-status></span>
        </div>

        {{-- IMAGES / COVERS / VIDEOS TABS --}}
        @foreach (['gallery' => 'Images', 'cover' => 'Covers', 'video' => 'Videos'] as $role => $label)
            @php
                $items = $role === 'gallery'
                    ? $entity->gallery
                    : ($role === 'cover' ? $entity->covers : $entity->videos);
                $accept = $role === 'video' ? 'video/*' : 'image/*';
            @endphp
            <div class="tab-pane" id="{{ $tabId }}-{{ $role }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">{{ count($items) }} {{ strtolower($label) }} uploaded</small>
                    <label class="btn btn-success btn-sm mb-0">
                        <i class="mdi mdi-upload"></i> Upload {{ $label }}
                        <input type="file" multiple accept="{{ $accept }}" class="d-none"
                            data-library-upload data-role="{{ $role }}">
                    </label>
                </div>

                <div class="row g-3" data-library-grid="{{ $role }}">
                    @forelse ($items as $item)
                        <div class="col-md-3 col-sm-4 col-6" data-library-item="{{ $item->id }}">
                            <div class="card mb-0">
                                @if ($role === 'video')
                                    <video class="card-img-top" controls preload="metadata"
                                        style="height: 160px; object-fit: cover; background:#000;">
                                        <source src="{{ $item->url }}">
                                    </video>
                                @else
                                    <img src="{{ $item->url }}" class="card-img-top"
                                        style="height: 160px; object-fit: cover;" alt="">
                                @endif
                                <div class="card-body p-2">
                                    <input type="text" class="form-control form-control-sm mb-1"
                                        value="{{ $item->title }}"
                                        placeholder="Title (optional)" data-library-title>
                                    <div class="d-flex gap-1">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm flex-fill"
                                            data-library-rename>Save</button>
                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            data-library-delete>
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light border text-center mb-0">
                                No {{ strtolower($label) }} yet. Upload some to enrich every quote that uses this {{ rtrim($type, 's') }}.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

@once
    <script>
            (function () {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

                function jsonHeaders() {
                    return { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' };
                }

                function getRoot(el) { return el.closest('.library-media-tabs'); }

                function urls(root) {
                    const type = root.dataset.libraryType;
                    const uuid = root.dataset.libraryUuid;
                    return {
                        upload: `/library/${type}/${uuid}/media`,
                        description: `/library/${type}/${uuid}/description`,
                        item: (id) => `/library/media/${id}`,
                    };
                }

                document.addEventListener('change', async function (e) {
                    if (!e.target.matches('[data-library-upload]')) return;
                    const root = getRoot(e.target);
                    if (!root || !e.target.files.length) return;

                    const role = e.target.dataset.role;
                    const fd = new FormData();
                    fd.append('role', role);
                    for (const f of e.target.files) fd.append('files[]', f);

                    e.target.disabled = true;
                    try {
                        const res = await fetch(urls(root).upload, {
                            method: 'POST',
                            headers: jsonHeaders(),
                            body: fd,
                        });
                        const out = await res.json();
                        if (!res.ok || !out.status) throw new Error(out.message || 'Upload failed');
                        window.location.reload();
                    } catch (err) {
                        alert(err.message);
                    } finally {
                        e.target.disabled = false;
                        e.target.value = '';
                    }
                });

                document.addEventListener('click', async function (e) {
                    const root = getRoot(e.target);
                    if (!root) return;

                    if (e.target.closest('[data-library-save-description]')) {
                        const ta = root.querySelector('[data-library-description]');
                        const status = root.querySelector('[data-library-description-status]');
                        status.textContent = 'Saving…';
                        try {
                            const res = await fetch(urls(root).description, {
                                method: 'PATCH',
                                headers: { ...jsonHeaders(), 'Content-Type': 'application/json' },
                                body: JSON.stringify({ description: ta.value }),
                            });
                            const out = await res.json();
                            if (!res.ok || !out.status) throw new Error(out.message || 'Save failed');
                            status.textContent = 'Saved';
                            setTimeout(() => (status.textContent = ''), 2000);
                        } catch (err) {
                            status.textContent = err.message;
                        }
                        return;
                    }

                    const renameBtn = e.target.closest('[data-library-rename]');
                    if (renameBtn) {
                        const item = renameBtn.closest('[data-library-item]');
                        const id = item.dataset.libraryItem;
                        const title = item.querySelector('[data-library-title]').value;
                        try {
                            const res = await fetch(urls(root).item(id), {
                                method: 'PATCH',
                                headers: { ...jsonHeaders(), 'Content-Type': 'application/json' },
                                body: JSON.stringify({ title }),
                            });
                            const out = await res.json();
                            if (!res.ok || !out.status) throw new Error(out.message || 'Save failed');
                            renameBtn.textContent = 'Saved';
                            setTimeout(() => (renameBtn.textContent = 'Save'), 1500);
                        } catch (err) {
                            alert(err.message);
                        }
                        return;
                    }

                    const delBtn = e.target.closest('[data-library-delete]');
                    if (delBtn) {
                        if (!confirm('Delete this file? It will be removed from the library.')) return;
                        const item = delBtn.closest('[data-library-item]');
                        const id = item.dataset.libraryItem;
                        try {
                            const res = await fetch(urls(root).item(id), {
                                method: 'DELETE',
                                headers: jsonHeaders(),
                            });
                            const out = await res.json();
                            if (!res.ok || !out.status) throw new Error(out.message || 'Delete failed');
                            item.remove();
                        } catch (err) {
                            alert(err.message);
                        }
                    }
                });
            })();
        </script>
@endonce
