@extends('layouts.app')
@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="page-title" style="font-size: 1.8em;">{{ $title }}</h4>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="dbtn">
                    @can('create-exchange-rates')
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create-exchange-rate-modal"
                            class="text-white rounded-2 mt-1" style="background-color: #FFA319; padding: 0.7em;"><i
                                class="uil-plus"></i> Create Exchange Rate</a>
                        </a>
                    @endcan
                </div>
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
            <div class="card mt-3">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row mt-1">
                            <div class="col-12">
                                <table id="fixed-header-datatable"
                                    class="table table-sm dt-responsive nowrap table-hover w-100">
                                    <thead class="pt-2" style="background-color: #e9ecef;">
                                        <tr>
                                            <th>Rate</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($exchangeRates as $rate)
                                            <tr>
                                                <td>{{ $rate->currency?->short_name ?? '—' }} {{ $rate->rate }}</td>
                                                <td><span class="badge"
                                                        style="background-color: @if ($rate->is_active) green @else orangered @endif">
                                                        @if ($rate->is_active)
                                                            Active
                                                        @else
                                                            Inactive
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $rate->created_at }}</td>
                                                <td>{{ $rate->createdBy?->userProfile?->name ?? '—' }}</td>
                                                <td class="table-action">
                                                    <a href="{{ route('exchange_rates.show', $rate->uuid) }}"
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
    @include('web.system.configuration.exchange_rate.includes.create_new_exchange_rate_details_modal')
@endsection
@section('script')
    @include('web.system.configuration.exchange_rate.includes.scripts.script')
@endsection
