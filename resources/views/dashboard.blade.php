@extends('layouts.app')
@section('title', 'Dashboard Helpdesk')

@section('content')
    {{-- DASHBOARD --}}
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('components.sidebar')
            <div class="layout-page">
                @include('components.navbar')
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl d-flex align-items-stretch flex-grow-1 p-0">
                        <div class="flex-shrink-1 flex-grow-0 w-px-150 border-end container-p-x container-p-y">
                            <!-- CONTENT -->
                            <div class="flex-shrink-1 flex-grow-1 container-p-x container-p-y">
                                
                            </div>
                        </div>
                        <!-- / Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <script src="{{ asset('assets/js/main.js') }}"></script>
@endpush
