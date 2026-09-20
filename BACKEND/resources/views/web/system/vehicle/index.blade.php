@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-vehicle-modal"
                        class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;">
                        <i class="uil-plus"></i> Create Vehicle
                    </a>
                </div>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Library</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">{{ $sub_title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-content">
                    <div class="card-body">
                        <table id="fixed-header-datatable"
                            class="table table-sm dt-responsive nowrap table-hover w-100">
                            <thead style="background-color: #e9ecef;">
                                <tr>
                                    <th>Cover</th>
                                    <th>Name</th>
                                    <th>Capacity</th>
                                    <th>Media</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehicles as $vehicle)
                                    @php $cover = $vehicle->primaryCover(); $counts = $vehicle->mediaCounts(); @endphp
                                    <tr>
                                        <td>
                                            @if ($cover)
                                                <img src="{{ $cover->url }}" alt=""
                                                    style="width:48px;height:36px;object-fit:cover;border-radius:4px;">
                                            @else
                                                <div class="text-muted small">–</div>
                                            @endif
                                        </td>
                                        <td><strong>{{ $vehicle->name }}</strong></td>
                                        <td>{{ $vehicle->capacity ?: '–' }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $counts['gallery'] }} img</span>
                                            <span class="badge bg-info">{{ $counts['covers'] }} cov</span>
                                            <span class="badge bg-warning">{{ $counts['videos'] }} vid</span>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                style="background-color: {{ $vehicle->is_active ? 'green' : 'orangered' }}">
                                                {{ $vehicle->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $vehicle->created_at }}</td>
                                        <td class="table-action">
                                            <a href="{{ route('vehicles.show', $vehicle->uuid) }}"
                                                class="action-icon text-info"><i class="uil uil-eye"></i>
                                                Show</a>
                                            @if ($vehicle->is_active)
                                                <a href="javascript:void(0);"
                                                    onclick='openLockModal("vehicle-toggle-{{ $vehicle->uuid }}", @json($vehicle->name))'
                                                    class="action-icon text-warning"><i class="uil uil-lock"></i>
                                                    Lock</a>
                                            @else
                                                <a href="javascript:void(0);"
                                                    onclick='openUnlockModal("vehicle-toggle-{{ $vehicle->uuid }}", @json($vehicle->name))'
                                                    class="action-icon text-success"><i class="uil uil-unlock"></i>
                                                    Unlock</a>
                                            @endif
                                            <a href="javascript:void(0);"
                                                onclick='openDeleteModal("vehicle-delete-{{ $vehicle->uuid }}", @json($vehicle->name))'
                                                class="action-icon text-danger"><i class="uil uil-trash"></i>
                                                Delete</a>
                                            <form id="vehicle-toggle-{{ $vehicle->uuid }}" method="POST"
                                                action="{{ route('vehicles.change_status', $vehicle->uuid) }}"
                                                class="d-none">@csrf</form>
                                            <form id="vehicle-delete-{{ $vehicle->uuid }}" method="POST"
                                                action="{{ route('vehicles.destroy', $vehicle->uuid) }}"
                                                class="d-none">@csrf @method('DELETE')</form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lock / Unlock / Delete confirmation modal (shared) --}}
    <div class="modal fade" id="status-action-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="status-action-title">Confirm action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i id="status-action-icon" class="d-block mx-auto mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1 fw-semibold" id="status-action-heading"></p>
                    <p class="text-muted mb-0" id="status-action-body"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="status-action-confirm" onclick="submitPendingForm()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Create modal --}}
    <div class="modal fade" id="create-vehicle-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('vehicles.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">New Vehicle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" required maxlength="150"
                                placeholder="e.g. 4x4 Safari Land Cruiser">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="text" name="capacity" class="form-control" maxlength="80"
                                placeholder="e.g. Up to 6 guests">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"
                                placeholder="What makes this vehicle great for safari…"></textarea>
                        </div>
                        <small class="text-muted">
                            You'll add images, covers and videos on the next page.
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Shared confirm-modal driver for Lock / Unlock / Delete actions.
        let pendingFormId = null;

        function showStatusActionModal(opts) {
            pendingFormId = opts.formId;
            document.getElementById('status-action-title').textContent = opts.title;
            document.getElementById('status-action-heading').textContent = opts.heading;
            document.getElementById('status-action-body').textContent = opts.body;

            const icon = document.getElementById('status-action-icon');
            icon.className = 'd-block mx-auto mb-3 uil ' + opts.iconClass;
            icon.style.fontSize = '3rem';
            icon.style.color = opts.iconColor;

            const confirmBtn = document.getElementById('status-action-confirm');
            confirmBtn.className = 'btn ' + opts.confirmClass;
            confirmBtn.textContent = opts.confirmLabel;

            new bootstrap.Modal(document.getElementById('status-action-modal')).show();
        }

        function openLockModal(formId, name) {
            showStatusActionModal({
                formId,
                title: 'Lock vehicle',
                heading: `Lock “${name}”?`,
                body: 'Locking sets this vehicle to inactive. It stops appearing in quote-builder pickers until you unlock it again.',
                iconClass: 'uil-lock',
                iconColor: '#f59e0b',
                confirmClass: 'btn-warning text-dark',
                confirmLabel: 'Lock',
            });
        }

        function openUnlockModal(formId, name) {
            showStatusActionModal({
                formId,
                title: 'Unlock vehicle',
                heading: `Unlock “${name}”?`,
                body: 'Unlocking re-activates this vehicle so it can be selected on new quotations and trip itineraries.',
                iconClass: 'uil-unlock',
                iconColor: '#10b981',
                confirmClass: 'btn-success',
                confirmLabel: 'Unlock',
            });
        }

        function openDeleteModal(formId, name) {
            showStatusActionModal({
                formId,
                title: 'Delete vehicle',
                heading: `Delete “${name}”?`,
                body: 'This permanently removes the vehicle from the library. Existing quotations that reference it will keep their data, but you will not be able to pick it again.',
                iconClass: 'uil-trash',
                iconColor: '#ef4444',
                confirmClass: 'btn-danger',
                confirmLabel: 'Delete',
            });
        }

        function submitPendingForm() {
            if (!pendingFormId) return;
            const form = document.getElementById(pendingFormId);
            if (form) form.submit();
        }
    </script>
@endsection
