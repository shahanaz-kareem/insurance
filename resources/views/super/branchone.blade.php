


@extends('global.app')

@section('title', 'Branch Details')

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/datepicker/datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/dropify/css/dropify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection

@section('extra_stylesheets')
    <link href="{{ asset('assets1/css/split-page.css') }}" rel="stylesheet"/>
@endsection

@section('profile_bar')
        <!-- client profile -->
        <div class="ui segment white right-bar-profile right-bar-profile-bottom">
            <div class="user-profile m-b-15">

                <div class="text-avatar" style="background-color:{{ collect(config('insura.colors'))->random() }};">{{ $branch[0]->branch_code[0] }}</div>

                <h3>{{ $branch[0]->branch_code }}</h3>
                <span>

                    <i class="ion-ios-circle-filled text-success"></i> {{ trans('clients.status.active') }}

                </span>
                @yield('client_action')
            </div>
            <div class="scrollbar">
                <div class="user-more-data">
                    <div class="divider m-t-0"></div>
                    <!-- client details -->
                    <div class="user-contact">
                        <div>
                            <span>Branch Code</span>
                            <p>{{ $branch[0]->branch_code }}</p>
                        </div>
                        <div>
                            <span>Branch Name</span>
                            <p>{{ $branch[0]->branch_name }}</p>
                        </div>
                        <div>
                            <span>Branch Email</span>
                            <p>{{ $branch[0]->branch_email }}</p>
                        </div>
                        <div>
                            <span>Branch Phone</span>
                            <p>{{ $branch[0]->branch_phone }}</p>
                        </div>

                        <div>
                            <span>Address</span>
                            <p>{{ $branch[0]->branch_address }}</p>
                        </div>
                    </div>
                    <!-- end client details -->
                </div>
            </div>
        </div>
        <!-- end client profile -->
@endsection

@section('content')
        @parent
        <!-- half page content -->
        <div class="half-page-content">
            @include('global.status')
            <!-- Policy details -->
            <div class="ui segment white fs-16">
                <div class="segment-header">
                    <h3>{{ trans('policies.label.details') }}</h3>
                    @yield('policy_actions')
                </div>


            <div class="row m-t-15 widgets">
            <!-- Column -->
                <div class="col-md-6 col-lg-2 col-xlg-2">
                    <div class="card card-inverse bg-primary">
                        <div class="box bg-info text-center">
                            <h1 class="font-light text-white">{{count($clients)}}</h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.clients') }}</h6>
                        </div>
                    </div>
                </div>
                <!-- Column -->
                <div class="col-md-6 col-lg-2 col-xlg-2">
                    <div class="card card-primary bg-purple">
                        <div class="box text-center">
                            <h1 class="font-light text-white">{{count($policies)}}</h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.policies') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2 col-xlg-2">
                <div class="card card-inverse bg-success">
                    <div class="box text-center">
                        <h1 class="font-light text-white">0</h1>
                        <h6 class="text-white">Due</h6>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <div class="col-md-6 col-lg-2 col-xlg-2">
                <div class="card card-inverse bg-primary">
                    <div class="box text-center">
                        <h1 class="font-light text-white">{{$amount[0]->total_sales}}</h1>
                        <h6 class="text-white">Sales</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2 col-xlg-2">
                <div class="card card-inverse bg-primary">
                    <div class="box text-center">
                        <h1 class="font-light text-white">{{count($branches)}}</h1>
                        <h6 class="text-white">Branches</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2 col-xlg-2">
                <div class="card card-inverse bg-primary">
                    <div class="box text-center">
                        <h1 class="font-light text-white">{{count($products)}}</h1>
                        <h6 class="text-white">Products</h6>
                    </div>
                </div>
            </div>
            </div>







            </div>


        </div>
        <!-- end half page content -->
@endsection




@section('page_scripts')
    <script src="{{ asset('assets1/libs/datepicker/datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/dropify/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                if ($(window).width() > 992) {
                    getVisible();
                }else{
                    $('div.right-bar-profile div.scrollbar').removeAttr("style");
                }

                $(window).resize(function(){
                    if ($(window).width() > 992) {
                        getVisible();
                    }else {
                        $('.scrollbar').removeAttr("style");
                    }
                });

                $insura.helpers.initDatepicker('input.datepicker');
                $insura.helpers.initDropify('input.file-upload');
                $insura.helpers.initDropdown('div.dropdown, select.dropdown');
                $insura.helpers.initModal('div#newAttachmentModal', true);
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.initSwal('form button.delete', {
                    confirmButtonText: '{{ trans('attachments.swal.warning.delete.confirm') }}',
                    text: '{{ trans('attachments.swal.warning.delete.text') }}',
                    title: '{{ trans('attachments.swal.warning.delete.title') }}'
                });
                $insura.helpers.listenForChats();
                $insura.helpers.requireDropdownFields('div.required select, div.required div.dropdown input[type="hidden"]');
            });
        })(window.insura, window.jQuery);
    </script>
@endsection
