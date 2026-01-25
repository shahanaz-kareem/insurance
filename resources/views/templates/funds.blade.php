

@extends('global.app')

@section('title', 'Mutual Fund')
@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/datepicker/datepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/dropify/css/dropify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/intl-tel-input/css/intlTelInput.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection
@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection

@section('action_buttons')
        <div class="ui right floated segment transparent page-actions">
            <button class="ui icon button positive" data-target="#clientFilter" data-toggle="slide">
                <i class="filter icon"></i>
            </button>
            @yield('new_payment_button')
        </div>
@endsection

@section('action_buttons')
            <div class="ui right floated segment transparent page-actions" style="display: none;">
                <button class="ui labeled icon button primary open-modal" data-target="#newProductModal" data-toggle="modal">
                    <i class="ion-ios-plus-outline icon"></i>
                    New Mutual Fund
                </button>
            </div>
@endsection



@section('content')
        @parent
        @include('global.status')
        <?php
        if(Auth::user()->role == 'staff'){
             $staff_branch = Auth::user()->branch_id;
        } else {
             $staff_branch = "";
        }

        ?>

        <div class="ui segment white" id="clientFilter"{!! $filter ? '' : ' style="display:none;"' !!}>
            <form action="{{ route('mutualfunds.all') }}" method="GET">
                <div class="ui form">

                    <div class="three fields">
                        <div class="field">
                            <label>Branches</label>
                            <select class="ui fluid search dropdown" name="branch" >
                                <option></option>
                                @foreach($branches as $branch)
                                    <option {{ ($branch->id == $staff_branch) || (isset($filters['branch']) && $filters['branch'] == $branch->id) ? ' selected' : '' }} value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Client</label>
                            <select class="ui fluid search dropdown" name="client" >
                                <option></option>
                                @foreach($clients as $client)
                                    <option {{ (isset($filters['client']) && $filters['client'] == $client->id) ? ' selected' : '' }} value="{{ $client->id }}">{{ $client->first_name.''.$client->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label>Product</label>
                            <select class="ui fluid search dropdown" name="product" >
                                <option></option>
                                @foreach($products as $product)
                                    <option {{ (isset($filters['product']) && $filters['product'] == $product->id) ? ' selected' : '' }} value="{{ $product->id }}">{{ $product->product_name }}</option>
                                @endforeach
                            </select>
                        </div>

                    <div class="text-right" style="display: contents;">
                        <button class="ui button" type="reset"> {{ trans('clients.button.clear') }} </button>
                        <button class="ui labeled icon button black" name="filter" type="submit"> <i class="search icon"></i> {{ trans('clients.button.filter') }} </button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div class="ui segment white">
            <table class="ui celled striped table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ACK No</th>
                        <th>ACK Date</th>
                        <th>Branch</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Product</th>
                        <!-- <th>Date</th> -->
                        <th>Notes</th>


                        <th class="center aligned">{{ trans('products.table.header.actions') }}</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($funds as $key => $fund)
                    <tr>
                        <td>{{ $funds->lastOnPreviousPage + $key + 1 }}</td>
                        <td>{{ $fund->mack_no }}</td>
                        <?php $fun_date = date("d-m-Y", strtotime($fund->mack_date)); ?>
                        <td>{{ $fun_date }}</td>
                        <td>{{ $fund->branch_name }}</td>
                        <td>{{ $fund->first_name.''.$fund->last_name }}</td>
                        <td>{{ $fund->amount }}</td>
                        <td>{{ $fund->product_name }}</td>
                        <?php $fun_date = date("d-m-Y", strtotime($fund->date)); ?>
                        <!-- <td>{{ $fun_date }}</td> -->
                        <td>{{ $fund->notes }} </td>


                        <td class="center aligned">
                            <a href="#" class="green label mini ui" data-target="#editProduct{{ $fund->mid }}Modal" data-toggle="modal"> {{ trans('products.button.edit') }} </a>
                            @if(Auth::user()->role == 'super')
                            <form action="{{ route('mutualfunds.delete', array($fund->mid)) }}" method="POST" style="display:inline;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button class="delete label mini red ui" style="cursor:pointer;" type="submit">{{ trans('products.button.delete') }}</button>
                            </form>
                             @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;">{{ trans('products.table.data.not_available') }}</td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="center aligned ui" colspan="5">
                            {{ trans('products.table.data.pagination', array(
                                'start' => $funds->total() > 0 ? $funds->lastOnPreviousPage + 1 : 0,
                                'stop'  => $funds->lastOnPreviousPage + $funds->count(),
                                'total' => $funds->total()
                            )) }}
                        </th>
                        <th class="center aligned ui" colspan="5">
                            {!! $presenter->render() !!}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
@endsection

@section('page_scripts')
   <!--  <script src="{{ asset('assets1/libs/datepicker/datepicker.min.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('assets1/libs/datepicker/datepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/dropify/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/intl-tel-input/js/intlTelInput.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                //alert('j');
                $insura.helpers.initDropdown('div.dropdown, select.dropdown');
                $insura.helpers.initModal('div#newProductModal', true);
                $insura.helpers.initModal('div[id^="editProduct"]', false);
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.initSwal('form button.delete', {
                    confirmButtonText: '{{ trans('products.swal.warning.delete.confirm') }}',
                    text: '{{ trans('products.swal.warning.delete.text') }}',
                    title: '{{ trans('products.swal.warning.delete.title') }}'
                });
                $insura.helpers.listenForChats();
                $insura.helpers.requireDropdownFields('form div.required select');
                $insura.helpers.initDatepicker('input.datepicker');



            });
        })(window.insura, window.jQuery);
    </script>
@endsection
