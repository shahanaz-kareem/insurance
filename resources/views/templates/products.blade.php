@extends('global.app')

@section('title', trans('products.title'))

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
@endsection
@if(Auth::user()->role == 'super')
@section('action_buttons')
            <div class="ui right floated segment transparent page-actions">
                <button class="ui labeled icon button primary open-modal" data-target="#newProductModal" data-toggle="modal">
                    <i class="ion-ios-plus-outline icon"></i>
                    {{ trans('products.button.new') }}
                </button>
            </div>
@endsection
@endif

@section('content')
        @parent
        @include('global.status')
        <div class="ui segment white">
            <table class="ui celled striped table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('products.table.header.name') }}</th>
                        <th>{{ trans('products.table.header.insurer') }}</th>
                        <th>{{ trans('products.table.header.category') }}</th>
                        <th>{{ trans('products.table.header.sub_category') }}</th>
                        <th class="center aligned">{{ trans('products.table.header.policies') }}</th>
                        @if(Auth::user()->role == 'super')
                        <th class="center aligned">{{ trans('products.table.header.actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $key => $product)
                    <tr>
                        <td>{{ $products->lastOnPreviousPage + $key + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->insurer }}</td>
                        <td>{{ $product->category }} </td>
                        <td>{{ $product->sub_category }}</td>
                        <td class="center aligned">{{ $product->policies->count() }}</td>
                        @if(Auth::user()->role == 'super')
                        <td class="center aligned">
                            <a href="#" class="green label mini ui" data-target="#editProduct{{ $product->id }}Modal" data-toggle="modal"> {{ trans('products.button.edit') }} </a>
                            <form action="{{ route('products.delete', array($product->id)) }}" method="POST" style="display:inline;">
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
                            {!! $presenter->render() !!}
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
                $insura.helpers.initModal('div[id^="editProduct"]', false);
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

@section('modals')
    <!-- new product modal -->
    <div class="ui tiny modal" id="newProductModal">
        <div class="header">{{ trans('products.modal.header.new') }}</div>
        <div class="content">
            <form action="{{ route('products.add') }}" class="ui form" method="POST">
                {{ csrf_field() }}
                @if(Auth::user()->role === 'super')
                    <div class="field required">
                        <label>{{ trans('products.input.label.company') }}</label>
                        <div class="ui selection dropdown">
                            <input name="company_id" type="hidden" value="{{ old('company_id') }}">
                            <i class="dropdown icon"></i>
                            <div class="default text">{{ trans('products.input.placeholder.company') }}</div>
                            <div class="menu">
                                @foreach($companies as $company)
                                    <div class="item" data-value="{{ $company->id }}">{{ $company->name }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="field required">
                    <label>{{ trans('products.input.label.name') }}</label>
                    <input name="name" placeholder="{{ trans('products.input.placeholder.name') }}" type="text" value="{{ old('name') }}">
                </div>
                <div class="field required">
                    <label>{{ trans('products.input.label.insurer') }}</label>
                    <input name="insurer" placeholder="{{ trans('products.input.placeholder.insurer') }}" type="text" value="{{ old('insurer') }}">
                </div>
                <div class="field required">
                    <label>{{ trans('products.input.label.category') }}</label>
                    <div class="ui selection dropdown">
                        <input name="category" type="hidden" value="{{ old('category') }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">{{ trans('products.input.placeholder.category') }}</div>
                        <div class="menu">
                            @if(Auth::user()->role === 'super')
                                @foreach($companies as $company)
                                    @foreach($company->product_categories as $category)
                                        <div class="item" data-value="{{ $category }}">{{ $category }}</div>
                                    @endforeach
                                @endforeach
                            @else
                                @foreach($company->product_categories as $category)
                                    <div class="item" data-value="{{ $category }}">{{ $category }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="field required">
                    <label>{{ trans('products.input.label.sub_category') }}</label>
                    <div class="ui selection dropdown">
                        <input name="sub_category" type="hidden" value="{{ old('sub_category') }}">
                        <i class="dropdown icon"></i>
                        <div class="default text">{{ trans('products.input.placeholder.sub_category') }}</div>
                        <div class="menu">
                            @if(Auth::user()->role === 'super')
                                @foreach($companies as $company)
                                    @foreach($company->product_sub_categories as $sub_category)
                                        <div class="item" data-value="{{ $sub_category }}">{{ $sub_category }}</div>
                                    @endforeach
                                @endforeach
                            @else
                                @foreach($company->product_sub_categories as $sub_category)
                                    <div class="item" data-value="{{ $sub_category }}">{{ $sub_category }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui buttons">
                <button class="ui cancel button">{{ trans('products.modal.button.cancel.new') }}</button>
                <div class="or" data-text="{{ trans('products.modal.button.or') }}"></div>
                <button class="ui positive primary button">{{ trans('products.modal.button.confirm.new') }}</button>
            </div>
        </div>
    </div>
    <!-- end new product modal -->

    @foreach ($products as $product)
        <!-- edit product modal -->
        <div class="ui tiny modal" id="editProduct{{ $product->id }}Modal">
            <div class="header">{{ trans('products.modal.header.edit', ['name' => $product->name]) }}</div>
            <div class="content">
                <form action="{{ route('products.edit', ['product' => $product->id]) }}" class="ui form" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('POST') }} {{-- Laravel uses POST for PUT/PATCH via _method field --}}
                    @if(Auth::user()->role === 'super')
                        <div class="field required">
                            <label>{{ trans('products.input.label.company') }}</label>
                            <div class="ui selection dropdown">
                                <input name="company_id" type="hidden" value="{{ $product->company->id }}">
                                <i class="dropdown icon"></i>
                                <div class="default text">{{ $product->company->name }}</div>
                                <div class="menu">
                                    @foreach($companies as $company)
                                        <div class="item" data-value="{{ $company->id }}">{{ $company->name }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="field required">
                        <label>{{ trans('products.input.label.name') }}</label>
                        <input name="name" placeholder="{{ trans('products.input.placeholder.name') }}" type="text" value="{{ $product->name }}">
                    </div>
                    <div class="field required">
                        <label>{{ trans('products.input.label.insurer') }}</label>
                        <input name="insurer" placeholder="{{ trans('products.input.placeholder.insurer') }}" type="text" value="{{ $product->insurer }}">
                    </div>
                    <div class="field required">
                        <label>{{ trans('products.input.label.category') }}</label>
                        <div class="ui selection dropdown">
                            <input name="category" type="hidden" value="{{ $product->category }}">
                            <i class="dropdown icon"></i>
                            <div class="default text">{{ $product->category }}</div>
                            <div class="menu">
                                @if(Auth::user()->role === 'super')
                                    @foreach($companies as $company)
                                        @foreach($company->product_categories as $category)
                                            <div class="item" data-value="{{ $category }}">{{ $category }}</div>
                                        @endforeach
                                    @endforeach
                                @else
                                    @foreach($company->product_categories as $category)
                                        <div class="item" data-value="{{ $category }}">{{ $category }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="field required">
                        <label>{{ trans('products.input.label.sub_category') }}</label>
                        <div class="ui selection dropdown">
                            <input name="sub_category" type="hidden" value="{{ $product->sub_category }}">
                            <i class="dropdown icon"></i>
                            <div class="default text">{{ $product->sub_category }}</div>
                            <div class="menu">
                                @if(Auth::user()->role === 'super')
                                    @foreach($companies as $company)
                                        @foreach($company->product_sub_categories as $sub_category)
                                            <div class="item" data-value="{{ $sub_category }}">{{ $sub_category }}</div>
                                        @endforeach
                                    @endforeach
                                @else
                                    @foreach($company->product_sub_categories as $sub_category)
                                        <div class="item" data-value="{{ $sub_category }}">{{ $sub_category }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="actions">
                <div class="ui buttons">
                    <button class="ui cancel button">{{ trans('products.modal.button.cancel.edit') }}</button>
                    <div class="or" data-text="{{ trans('products.modal.button.or') }}"></div>
                    <button class="ui positive primary button">{{ trans('products.modal.button.confirm.edit') }}</button>
                </div>
            </div>
        </div>
        <!-- end edit product modal -->
    @endforeach
@endsection
