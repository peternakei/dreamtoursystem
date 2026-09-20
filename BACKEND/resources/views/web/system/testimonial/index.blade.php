@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
                <div class="breadcumbs">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">SBS </a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">{{ $sub_title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mt-2">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row mt-1">
                            <div class="col-12">
                                <table id="fixed-header-datatable"
                                    class="table table-sm dt-responsive nowrap table-hover w-100">
                                    <thead class="pt-2" style="background-color: #e9ecef;">
                                        <tr>
                                            <th>Tourist</th>
                                            <th>Phone</th>
                                            <th>Comment</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($testimonials as $testimonial)
                                            <tr>
                                                <td>{{ $testimonial->tourist->name }}</td>
                                                <td>{{ $testimonial->tourist->phone }}</td>
                                                <td>{{ Str::limit($testimonial->comments, 80, '...') }}</td>
                                                <td><span class="badge"
                                                        style="background-color: @if ($testimonial->is_approved) Green @else Orangered @endif">
                                                        @if ($testimonial->is_approved)
                                                            Approved
                                                        @else
                                                            Pending
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $testimonial->created_at }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('testimonials.show', $testimonial->uuid) }}"
                                                        class="action-icon text-info"><i class="uil uil-eye"></i>
                                                        Show</a>
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
        </div>
    </div>
@endsection
