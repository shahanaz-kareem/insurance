

@extends('global.app')

@section('title', 'Branches')

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection

@section('action_buttons')

            @if(Auth::user()->role == 'super')
                <div class="ui right floated segment transparent page-actions">
                    <button class="ui labeled icon button primary open-modal" data-target="#newProductModal" data-toggle="modal">
                        <i class="ion-ios-plus-outline icon"></i>
                        Add New Branches
                    </button>
                </div>
            @endif
            <!-- <div class="ui right floated segment transparent page-actions">
                <button class="ui icon button positive" data-target="#policyFilter" data-toggle="slide">
                    <i class="filter icon"></i>
                </button>

            </div> -->
@endsection

@section('content')
        @parent
        @include('global.status')
        <div class="ui segment white" id="policyFilter"{!! $filter ? '' : ' style="display:none;"' !!}>
            <form action="{{ route('branches.all') }}" method="GET">
                <div class="ui form">
                    <div class="five fields">
                        <div class="field">
                            <label>Branch Name</label>
                            <input type="text" name="branch_name" placeholder="Branch Name" value="{{ $filters['branch_name'] ?? null }}"/>
                        </div>

                        <div class="field">
                            <label>Branch Code</label>
                            <input type="text" name="branch_code" placeholder="Branch Code" value="{{ $filters['branch_code'] ?? null }}"/>
                        </div>
                        <div class="field">
                            <label>Mobile</label>
                            <input type="text" name="branch_phone" placeholder="Mobile" value="{{ $filters['branch_phone'] ?? null }}"/>
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
                        <th>Branch Code</th>
                        <th>Branch Name</th>


                        <th>Email</th>
                        <th>Phone</th>
                        <th>Location</th>
                        @if(Auth::user()->role == 'super')
                            <th class="center aligned">{{ trans('products.table.header.actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $key => $product)
                    <tr>
                        <td>{{ $products->lastOnPreviousPage + $key + 1 }}</td>
                        <td>{{ $product->branch_code }}</td>
                        <td>{{ $product->branch_name }}</td>


                        <td>{{ $product->branch_email }} </td>
                        <td>{{ $product->branch_phone }}</td>
                        <td>{{ $product->branch_location }}</td>
                        @if(Auth::user()->role == 'super')
                        <td class="center aligned">
                            <a href="#" class="green label mini ui" data-target="#editProduct{{ $product->id }}Modal" data-toggle="modal"> {{ trans('products.button.edit') }} </a>
                            <a href="{{ route('branches.one', array($product->id)) }}" class="ui mini grey label"> {{ trans('policies.table.data.action.view') }} </a>
                            <form action="{{ route('branches.delete', array($product->id)) }}" method="POST" style="display:inline;">
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
                        <th class="center aligned ui" colspan="2">
                            {{ trans('products.table.data.pagination', array(
                                'start' => $products->total() > 0 ? $products->lastOnPreviousPage + 1 : 0,
                                'stop'  => $products->lastOnPreviousPage + $products->count(),
                                'total' => $products->total()
                            )) }}
                        </th>
                        <th class="center aligned ui" colspan="5">
                          

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
