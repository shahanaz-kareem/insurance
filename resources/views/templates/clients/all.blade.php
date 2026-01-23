

@extends('global.app')

@section('title', trans('clients.title.all'))

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/datepicker/datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/dropify/css/dropify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/intl-tel-input/css/intlTelInput.css') }}" rel="stylesheet"/>
@endsection

@section('extra_stylesheets')
    <link href="{{ asset('assets1/css/split-page.css') }}" rel="stylesheet">
@endsection

@section('action_buttons')
            <div class="ui right floated segment transparent page-actions">
                <button class="ui icon button positive" data-target="#clientFilter" data-toggle="slide">
                    <i class="filter icon"></i>
                </button>
                <button class="ui labeled icon button primary open-modal" data-target="#newClientModal" data-toggle="modal">
                    <i class="ion-ios-plus-outline icon"></i>
                    {{ trans('clients.button.new') }}
                </button>
            </div>
@endsection

@section('content')
        @parent
        @include('global.status')
        <div class="ui segment white" id="clientFilter"{!! $filter ? '' : ' style="display:none;"' !!}>
            <form action="{{ route('clients.all') }}" method="GET">
                <div class="ui form">
                    <div class="five fields">
                        <div class="field">
                            <label>{{ trans('clients.input.label.client') }}</label>
                            <!--<div class="ui selection dropdown">-->
                            <!--    <input type="hidden" name="client"/>-->
                            <!--    <div class="default text text-ellipsis">{{ trans('clients.input.placeholder.client') }}</div>-->
                            <!--    <i class="dropdown icon"></i>-->
                            <!--    <div class="menu">-->
                            <!--        @foreach($allusers as $user)-->
                            <!--        <div class="item{{ ($filters['client'] ?? null) == $user->id ? ' selected' : '' }}" data-value="{{ $user->id }}">-->
                            <!--            <i class="angle right icon"></i>-->
                            <!--            {{ $user->first_name }} {{ $user->last_name }}-->
                            <!--        </div>-->
                            <!--        @endforeach-->
                            <!--    </div>-->
                            <!--</div>-->
                                <select class="ui fluid search dropdown baba" name="client">
                                    <option></option>
                                    @foreach($allusers as $user)
                                    <option value="{{ $user->id }}" {{ isset($filters['client']) && $filters['client'] == $user->id ? ' selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="field">
                            <label>{{ trans('clients.input.label.phone') }}</label>
                            <input type="text" name="phone" placeholder="{{ trans('clients.input.placeholder.phone') }}" value="{{ isset($filters['phone']) ? $filters['phone'] : '' }}"/>
                        </div>
                        <div class="field">
                            <label>{{ trans('clients.input.label.branch') }}</label>
                            <div class="ui selection dropdown">
                                <input type="hidden" name="branch"/>
                                <div class="default text text-ellipsis">{{ trans('clients.input.placeholder.branch') }}</div>
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    @forelse($user->company->branches as $branch)
                                    <div class="item{{ isset($filters['branch']) && $filters['branch'] == $branch->id ? ' selected' : '' }}" data-value="{{ $branch->id }}">
                                        <i class="angle right icon"></i>
                                        {{ $branch->branch_name }}
                                    </div>
                                    @empty
                                    <div class="item disabled" data-value="">
                                        <i class="angle right icon"></i>
                                        {{ trans('clients.input.option.empty.branch')}}
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label>{{ trans('clients.input.label.ref_no') }}</label>
                            <input type="text" name="ref_no" placeholder="{{ trans('clients.input.placeholder.ref_no') }}" value="{{ isset($filters['ref_no']) ? $filters['ref_no'] : '' }}"/>
                        </div>
                    </div>
                    <div class="text-right">
                        <button id="filter_all" class="ui button" type="reset"> {{ trans('clients.button.clear') }} </button>
                        <button class="ui labeled icon button black" name="filter" type="submit"> <i class="search icon"></i> {{ trans('clients.button.filter') }} </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row clients-list">
            
            <!-- start clients -->
            @forelse($clients as $client)
            @if(Auth::user()->role == 'staff')
            @if(Auth::user()->branch_id == $client->branch_id)
            <div class="col-md-6">
                <div class="ui segment white client-list-card">
                    <div class="client-list-about">
                        <div class="client-list-avatar">
                            @if ($client->profile_image_filename === 'default-profile.jpg')
                            <div class="text-avatar small w-h-70" style="background-color:{{ collect(config('insura.colors'))->random() }};">{{ strtoupper($client->first_name[0] . (isset($client->last_name) ? $client->last_name[0] : '')) }}</div>
                            @else
                            <!-- <img src="{{ asset('storage/app/images/users/' . $client->profile_image_filename) }}" alt="{{ $client->first_name }} {{ $client->last_name }}"/> -->
                            <img src="{{ 'http://integrityindia.co.in/insurance/storage/app/images/users/' . $client->profile_image_filename }}" alt="{{ $client->first_name }} {{ $client->last_name }}"/>
                            @endif
                        </div>
                        <div class="client-list-info">
                            <h3>{{ $client->first_name }} {{ $client->last_name }}</h3>
                            <span>
                                @if ($client->status)
                                <i class="ion-ios-circle-filled text-success"></i> {{ trans('clients.status.active') }}
                                @else
                                <i class="ion-ios-circle-filled text-danger"></i> {{ trans('clients.status.inactive') }}
                                @endif
                            </span>
                            <div class="client-list-contact">
                                <div class="col-xs-6 col-sm-6 col-md-12 b-r text-ellipsis p-0">
                                    <i class="ion-ios-email"></i> {{ $client->email }}
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-12 text-ellipsis">
                                    <i class="ion-ios-telephone"></i> {{ $client->phone ?? '(---) ---- --- ---' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row client-list-footer">
                        <div class="col-xs-3 col-sm-4 col-md-3">
                            <p>
                                <strong>{{ trans('clients.label.policies') }}:</strong> {{ $client->policies->count() }}
                            </p>
                        </div>
                        <div class="col-xs-3 col-sm-4 col-md-3">
                            <p>
                                <?php
                                    $orgDate = date('Y-m-d');
                                    $newDate = date("y-m", strtotime($orgDate));
                                    $branch_code = isset($branchMap[$client->branch_id]) ? $branchMap[$client->branch_id]->branch_code : '';
                                ?>
                                <strong> Code:</strong> {{ $branch_code.'/'.$newDate.'/'.$client->staff_code }}
                            </p>
                        </div>
                        <div class="col-xs-5 col-sm-4 col-md-3">
                            <p>
                                <strong>{{ trans('clients.label.due') }}:</strong>
                                @if ($client->premiums > $client->paid)
                                <span class="text-danger">
                                @elseif ($client->premiums === $client->paid)
                                <span class="text-success">{{ trans('clients.label.status.paid') }} -
                                @else
                                <span class="text-info">
                                @endif
                                    {{ $client->currency_symbol ?? $clients->currency_symbol }}{{ $client->due }}
                                </span>
                            </p>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-3 text-right client-list-more">
                            <a href="{{ route('clients.one', array($client->id)) }}" class="mini ui button"> {{ trans('clients.link.profile') }} </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @else
                        <div class="col-md-6">
                <div class="ui segment white client-list-card">
                    <div class="client-list-about">
                        <div class="client-list-avatar">
                            @if ($client->profile_image_filename === 'default-profile.jpg')
                            <div class="text-avatar small w-h-70" style="background-color:{{ collect(config('insura.colors'))->random() }};">{{ strtoupper($client->first_name[0] . (isset($client->last_name) ? $client->last_name[0] : '')) }}</div>
                            @else
                            <!-- <img src="{{ asset('storage/app/images/users/' . $client->profile_image_filename) }}" alt="{{ $client->first_name }} {{ $client->last_name }}"/> -->
                            <img src="{{ 'http://integrityindia.co.in/insurance/storage/app/images/users/' . $client->profile_image_filename }}" alt="{{ $client->first_name }} {{ $client->last_name }}"/>
                            @endif
                        </div>
                        <div class="client-list-info">
                            <h3>{{ $client->first_name }} {{ $client->last_name }}</h3>
                            <span>
                                @if ($client->status)
                                <i class="ion-ios-circle-filled text-success"></i> {{ trans('clients.status.active') }}
                                @else
                                <i class="ion-ios-circle-filled text-danger"></i> {{ trans('clients.status.inactive') }}
                                @endif
                            </span>
                            <div class="client-list-contact">
                                <div class="col-xs-6 col-sm-6 col-md-12 b-r text-ellipsis p-0">
                                    <i class="ion-ios-email"></i> {{ $client->email }}
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-12 text-ellipsis">
                                    <i class="ion-ios-telephone"></i> {{ $client->phone ?? '(---) ---- --- ---' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row client-list-footer">
                        <div class="col-xs-3 col-sm-4 col-md-3">
                            <p>
                                <strong>{{ trans('clients.label.policies') }}:</strong> {{ $client->policies->count() }}
                            </p>
                        </div>
                        <div class="col-xs-3 col-sm-4 col-md-3">
                            <p>
                                <?php
                                    $orgDate = date('Y-m-d');
                                    $newDate = date("y-m", strtotime($orgDate));
                                    $branch_code = isset($branchMap[$client->branch_id]) ? $branchMap[$client->branch_id]->branch_code : '';
                                ?>
                                <strong> Code:</strong> {{ $branch_code.'/'.$newDate.'/'.$client->staff_code }}
                            </p>
                        </div>
                        <div class="col-xs-5 col-sm-4 col-md-3">
                            <p>
                                <strong>{{ trans('clients.label.due') }}:</strong>
                                @if ($client->premiums > $client->paid)
                                <span class="text-danger">
                                @elseif ($client->premiums === $client->paid)
                                <span class="text-success">{{ trans('clients.label.status.paid') }} -
                                @else
                                <span class="text-info">
                                @endif
                                    {{ $client->currency_symbol ?? $clients->currency_symbol }}{{ $client->due }}
                                </span>
                            </p>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-3 text-right client-list-more">
                            <a href="{{ route('clients.one', array($client->id)) }}" class="mini ui button"> {{ trans('clients.link.profile') }} </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @empty
            <div class="col-md-4 col-md-offset-3">
                <div class="segment text-center ui white">
                    <i class="huge icon ion-android-alert"></i>
                    <p>{{ trans('clients.message.empty.clients') }}</p>
                </div>
            </div>
            @endforelse
            <!-- end clients -->
            <div class="col-md-12 text-center">
             
                {!! $clients->render() !!}
            </div>
        </div>
@endsection

@section('page_scripts')
    <!-- <script src="{{ asset('assets1/libs/datepicker/datepicker.min.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('assets1/libs/datepicker/datepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/dropify/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/intl-tel-input/js/intlTelInput.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                $insura.helpers.initDatepicker('input.datepicker');
                $insura.helpers.initDropdown('div.dropdown, select.dropdown');
                $insura.helpers.initDropify('input.file-upload');
                $insura.helpers.initModal('div.modal', true);
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.initTelInput('input[type="tel"]');
                $insura.helpers.listenForChats();
                $insura.helpers.requireDropdownFields('form div.required select, form div.required div.dropdown input[type="hidden"]');
            });
        })(window.insura, window.jQuery);
    </script>
        <script type="text/javascript">
        $("#filter_all").click(function() {
             window.location='http://integrityindia.co.in/insurance/public/clients';
        });
    </script>
@endsection
