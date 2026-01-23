

@extends('global.app')

@section('title', 'Mutual Fund Products')

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection
@if(Auth::user()->role == 'super')
@section('action_buttons')


                <div class="ui right floated segment transparent page-actions">
                    <button class="ui labeled icon button primary open-modal" data-target="#newProductModal" data-toggle="modal">
                        <i class="ion-ios-plus-outline icon"></i>
                        Add New Mutual Fund Products
                    </button>
                </div>

            <!-- <div class="ui right floated segment transparent page-actions">
                <button class="ui icon button positive" data-target="#policyFilter" data-toggle="slide">
                    <i class="filter icon"></i>
                </button>

            </div> -->
@endsection
@endif
@section('content')
        @parent
        @include('global.status')
        <div class="ui segment white" id="policyFilter"{!! $filter ? '' : ' style="display:none;"' !!}>
            <form action="{{ route('mproducts.all') }}" method="GET">
                <div class="ui form">
                    <div class="five fields">
                        <div class="field">
                            <label>Product Name</label>
                            <input type="text" name="product_name" placeholder="Product Name" value="{{ $filters['product_name'] ?? null }}"/>
                        </div>


                        <div class="field">
                            <label>Status</label>
                            <select class="dropdown fluid search ui" name="status" required>
                                <option value="S">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-right">
                        <button class="ui button" type="reset"> {{ trans('policies.button.clear') }} </button>
                        <button class="ui labeled icon button black" name="filter" type="submit"> <i class="search icon"></i> {{ trans('policies.button.filter') }} </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="ui segment white">
            <table class="ui celled striped table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Scheme Name</th>

                        <th>Status</th>
                        @if(Auth::user()->role == 'super')
                        <th class="center aligned">{{ trans('products.table.header.actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mproducts as $key => $mproduct)
                    <tr>
                        <td>{{ $mproducts->lastOnPreviousPage + $key + 1 }}</td>
                        <td>{{ $mproduct->company_name }}</td>
                        <td>{{ $mproduct->product_name }}</td>
                        <td><?php if($mproduct->status=='I'){echo 'Inactive';}else{echo 'Active';}?> </td>
                        @if(Auth::user()->role == 'super')
                        <td class="center aligned">
                            <a href="#" class="green label mini ui" data-target="#editProduct{{ $mproduct->id }}Modal" data-toggle="modal"> {{ trans('products.button.edit') }} </a>
                            <!-- <a href="{{ action('MproductController@getOne', array($mproduct->id)) }}" class="ui mini grey label"> {{ trans('policies.table.data.action.view') }} </a> -->

                            <form action="{{ route('mproducts.delete', array($mproduct->id)) }}" method="POST" style="display:inline;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button class="delete label mini red ui" style="cursor:pointer;" type="submit">{{ trans('products.button.delete') }}</button>
                            </form>

                        </td>
                        @endif
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
                                'start' => $mproducts->total() > 0 ? $mproducts->lastOnPreviousPage + 1 : 0,
                                'stop'  => $mproducts->lastOnPreviousPage + $mproducts->count(),
                                'total' => $mproducts->total()
                            )) }}
                        </th>
                        <th class="center aligned ui" colspan="5">
                            {!! $mproducts->links('vendor.pagination.semantic-ui') !!}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets1/libs/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                $insura.helpers.initDropdown('div.dropdown, select.dropdown');
                $insura.helpers.initModal('div#newProductModal', true);
                $insura.helpers.initModal('div[id^="editProduct"]', true);
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.initSwal('form button.delete', {
                    confirmButtonText: '{{ trans('products.swal.warning.delete.confirm') }}',
                    text: '{{ trans('products.swal.warning.delete.text') }}',
                    title: '{{ trans('products.swal.warning.delete.title') }}'
                });
                $insura.helpers.listenForChats();
                $insura.helpers.requireDropdownFields('form div.required select');
            });
        })(window.insura, window.jQuery);
    </script>
@endsection
