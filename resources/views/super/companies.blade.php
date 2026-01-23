

@extends('global.app')

@section('title', trans('companies.title'))

@section('sub_title', trans('companies.sub_title', array(
    'system'    => config('insura.name')
)))







@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/datepicker/datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/dropify/css/dropify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/intl-tel-input/css/intlTelInput.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection

@section('content')
        @parent
        @include('global.status')
<button class="ui labeled icon button primary" data-target="#newPolicyModal" data-toggle="modal" style="float: right;position: absolute;    top: 90px;right: 20px">
    <i class="ion-ios-plus-outline icon"></i>
    New Company
</button>
@section('modals')
    <!-- new policy modal -->
    <div class="ui tiny modal" id="newPolicyModal">
        <div class="header">New Company</div>
        <div class="scrolling content">
            <p>Add new Company</p>
            <form action="{{ route('companies.add') }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>Comapny Name</label>
                        <input type="text" name="comapanyname" maxlength="64" placeholder="Comapny Name" required value=""/>
                    </div>

                    <div class="field">
                        <label>{{ trans('settings.input.label.email') }}</label>
                        <input type="email" maxlength="64" name="email" placeholder="{{ trans('settings.input.placeholder.email') }}" value="">
                    </div>
                    <div class="field">
                        <label>{{ trans('settings.input.label.phone') }}</label>
                        <input type="tel" name="phone"  placeholder="{{ trans('settings.input.placeholder.phone') }}" value="">
                    </div>

                    <div class="field">
                        <label>Address</label>
                        <textarea name="address" placeholder="Address" rows="4"></textarea>
                    </div>
                    <div class="field" style="display: none">
                        <label>{{ trans('settings.input.label.product_categories') }}</label>
                        <textarea name="product_categories" placeholder="{{ trans('settings.input.placeholder.product_categories') }}" rows="2">{{ $companydata->product_categories }}</textarea>
                    </div>
                    <div class="field" style="display: none">
                        <label>{{ trans('settings.input.label.product_sub_categories') }}</label>
                        <textarea name="product_sub_categories" placeholder="{{ trans('settings.input.placeholder.product_sub_categories') }}" rows="2">{{ $companydata->product_sub_categories }}</textarea>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select class="ui fluid search dropdown" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="actions">
                        <div class="ui buttons pull-right">
                            <button type="reset"class="ui cancel button">{{ trans('policies.modal.button.cancel.edit') }}</button>
                            <div class="or" data-text="{{ trans('policies.modal.button.or') }}"></div>
                            <button class="ui right floated button primary m-w-140" type="submit">{{ trans('settings.button.save') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection
<div class="row m-t-30">

            <!-- start current company -->
            <!-- <div class="col-md-4" style="display: none">
                <div class="ui segment white company-item">
                    <p class="text-ellipsis company-name">{{ $user->company->name }}</p>


                    <div class="text-avatar" style="background-color:{{ collect(config('insura.colors'))->random() }};">{{ strtoupper($user->company->name[0] . collect(explode(' ', $user->company->name))->get(1, ' ')[0]) }}</div>
                    <h1>{{ collect(config('insura.currencies.list'))->keyBy('code')->get($user->company->currency_code)['symbol'] }} {{ $user->company->policies->sum('premium') }}</h1>
                    <span class="company-sales">Total Sales</span>
                    <div class="row company-info">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <p>{{ $user->company->clients->count() }}</p>
                            <span>{{ trans('companies.label.client') }}</span>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <p>{{ $user->company->policies->count() }}</p>
                            <span>{{ trans('companies.label.policy') }}</span>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <p>{{ $user->company->staff->count() }}</p>
                            <span>{{ trans('companies.label.staff') }}</span>
                        </div>
                    </div>
                </div>
            </div> -->
    @foreach($companies as $company)
             <!-- edit policy modal -->
    <div class="ui tiny modal" id="editPolicy{{ $company->id }}Modal">
        <div class="header">{{ trans('policies.modal.header.edit') }}</div>
        <div class="scrolling content">
            <p>{{ trans('policies.modal.instruction.edit') }}</p>
            <form action="{{ route('companies.editall') }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>Comapny Name</label>
                        <input type="text" name="comapanyname" maxlength="64" placeholder="Comapny Name" required value="{{$company->name}}"/>
                        <input type="hidden" name="comapanyid"  value="{{$company->id}}"/>
                    </div>

                    <div class="field">
                        <label>{{ trans('settings.input.label.email') }}</label>
                        <input type="email" maxlength="64" name="email" placeholder="{{ trans('settings.input.placeholder.email') }}" value="{{$company->email}}">
                    </div>
                    <div class="field">
                        <label>{{ trans('settings.input.label.phone') }}</label>
                        <input type="tel" maxlength="16" name="phone" placeholder="{{ trans('settings.input.placeholder.phone') }}" value="{{$company->phone}}">
                    </div>
                    <div class="field">
                        <label>Address</label>
                        <textarea name="address" placeholder="Address" rows="4">{{$company->address}}</textarea>
                    </div>
                    <div class="field" style="display: none">
                        <label>{{ trans('settings.input.label.product_categories') }}</label>
                        <textarea name="product_categories" placeholder="{{ trans('settings.input.placeholder.product_categories') }}" rows="2">{{ $company->product_categories }}</textarea>
                    </div>
                    <div class="field" style="display: none">
                        <label>{{ trans('settings.input.label.product_sub_categories') }}</label>
                        <textarea name="product_sub_categories" placeholder="{{ trans('settings.input.placeholder.product_sub_categories') }}" rows="2">{{ $company->product_sub_categories }}</textarea>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select class="ui fluid search dropdown" name="status">
                            <option value="1" {{ $company->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $company->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="actions">
                        <div class="ui buttons pull-right">
                            <button type="reset"class="ui cancel button">{{ trans('policies.modal.button.cancel.edit') }}</button>
                            <div class="or" data-text="{{ trans('policies.modal.button.or') }}"></div>
                            <button class="ui right floated button primary m-w-140" type="submit">{{ trans('settings.button.save') }}</button>
                        </div>
                    </div>
                    <!-- <div class="field">
                        <button class="ui cancel button">{{ trans('policies.modal.button.cancel.edit') }}</button>
                        <div class="or" data-text="{{ trans('policies.modal.button.or') }}"></div>

                    </div> -->
                </div>
            </form>
        </div>

    </div>
    @endforeach
            <!-- end current company -->

            <!-- start other companies -->
            @forelse($companies as $company)
            <div class="col-md-4">
                <div class="ui segment white company-item">
                    <p class="text-ellipsis company-name">{{ $company->name }}</p>
                   <form action="{{ route('companies.delete', array($company->id)) }}" class="delete-company" data-position="left center" data-inverted="" data-tooltip="{{ trans('companies.tooltip.delete') }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <i class="icon ion-ios-trash ui"></i>
                    </form>
                    <a href="#" data-target="#editPolicy{{$company->id}}Modal" data-toggle="modal">
                        <i class="write icon"></i> Edit
                    </a>


                    <div class="text-avatar" style="background-color:{{ collect(config('insura.colors'))->random() }};">{{ strtoupper($company->name[0] . collect(explode(' ', $company->name))->get(1, ' ')[0]) }}</div>
                    <!-- <h1>{{ collect(config('insura.currencies.list'))->keyBy('code')->get($company->currency_code)['symbol'] }} {{ $company->policies->sum('premium') }}</h1>
                    <span class="company-sales">Total Sales</span> -->
                    <!-- <div class="row company-info">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <p>{{ $company->clients->count() }}</p>
                            <span>{{ trans('companies.label.client') }}</span>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <p>{{ $company->policies->count() }}</p>
                            <span>{{ trans('companies.label.policy') }}</span>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <p>{{ $company->staff->count() }}</p>
                            <span>{{ trans('companies.label.staff') }}</span>
                        </div>
                    </div> -->
                </div>
            </div>
            @empty
            <div class="col-xs-4 col-md-offset-3">
                <div class="segment text-center ui white">
                    <i class="huge icon ion-android-alert"></i>
                    <p>{{ trans('companies.message.empty') }}</p>
                </div>
            </div>
            @endforelse
            <!-- end other companies -->
        </div>
@endsection

@section('extra_scripts')
    <script src="{{ asset('assets1/libs/datepicker/datepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/dropify/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/intl-tel-input/js/intlTelInput.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                $insura.helpers.initTelInput('input[type="tel"]');
                $insura.helpers.initDropdown('div.dropdown');
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.initSwal('form i.ion-ios-trash', {
                    confirmButtonText: '{{ trans('companies.swal.warning.delete.confirm') }}',
                    text: '{{ trans('companies.swal.warning.delete.text') }}',
                    title: '{{ trans('companies.swal.warning.delete.title') }}'
                });
                $insura.helpers.listenForChats();
            });
        })(window.insura, window.jQuery);

    </script>
@endsection
