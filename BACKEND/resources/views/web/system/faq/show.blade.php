@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="back-button">
                <a href="{{ route('faqs.index') }}" class="text-muted"><span style="font-size: 1.5em;"><i
                            class="uil uil-arrow-circle-left"></i></span></a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.5em;">Faq Profile</h4>
                <div class="d-flex justify-content-end align-items-center" style="column-gap: 7px;">
                    <div class="dropdown">
                        @can('change-faq-status')
                            <a href="#" data-bs-toggle="modal" data-bs-target="#change-faq-status-modal"
                                class="btn btn-success">Change Status</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-8 col-lg-8">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="nav nav-tabs nav-justified nav-bordered mb-3">
                                    <li class="nav-item">
                                        <a href="#question" data-bs-toggle="tab" aria-expanded="true"
                                            class="nav-link active">
                                            <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                            <span class="d-none d-md-block">Question</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="tourists">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12" data-simplebar style="max-height: 350px;">
                                                <table class="table table-bordered table-centered mb-0">
                                                    <thead style="background-color: #e9ecef;">
                                                        <tr>
                                                            <th>Question</th>
                                                            <th>Order</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $faq->question }}</td>
                                                            <td>{{ $faq->order }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card"
                        style="border-bottom: 5px @if ($faq->is_active) green @else red @endif solid;">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center" style="column-gap: 5px;">
                                <i class="uil uil-check-circle"
                                    style="font-size: 1.2em; color: @if ($faq->is_active) green @else red @endif"></i>
                                <h5 class="text-center"
                                    style="color: @if ($faq->is_active) green @else red @endif; font-size: 1.2em; font-weight: bold;">
                                    @if ($faq->is_active)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </h5>
                            </div>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <p>{{ $faq->answer }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-content">
                            <div class="card-body">
                                <h5 style="font-weight: 500; font-size: 1.2em;">Faq Details</h5>
                                <div class="row mt-1">
                                    <div class="col-md-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Category</h5>
                                        <h5 style="font-weight: 500;">{{ $faq->faqCategory->name }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Order</h5>
                                        <h5 style="font-weight: 500;">{{ $faq->order }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created At</h5>
                                        <h5 style="font-weight: 500;">{{ $faq->created_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Created By</h5>
                                        <h5 style="font-weight: 500;">
                                            {{ $faq->createdBy->userProfile->name }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated At</h5>
                                        <h5 style="font-weight: 500;">{{ $faq->updated_at }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h5 class="opacity-75" style="font-weight: 500;">Updated By</h5>
                                        <h5 style="font-weight: 500;">
                                            @if (isset($faq->updatedBy))
                                                {{ $faq->updatedBy->userProfile->name }}
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('web.system.faq.includes.change_faq_status_modal')
@endsection
