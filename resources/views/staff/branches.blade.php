@extends('templates.branches')

@section('modals')
    <!-- New product modal -->
    <div class="ui tiny modal" id="newProductModal">
        <div class="header">Branches</div>
        <div class="scrolling content">
            <p>New Branch</p>
            <form action="{{ route('branches.add') }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>Branch Name</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_name" placeholder="Branch Name" required value="{{ old('branch_name') }}">
                    </div>
                    <div class="field required">
                        <label>Branch Code</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_code" placeholder="Branch code" required value="{{ old('branch_name') }}">
                    </div>

                    <div class="field required" >
                        <label>Location</label>
                             <input type="text" maxlength="64" minlength="3" name="branch_location" placeholder="Branch Name" required value="{{ old('branch_location') }}">
                       <!--  <select class="ui fluid search dropdown" name="branch">
                            @foreach($companies1 as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select> -->
                    </div>
                     <div class="field required">
                        <label>Phone</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_phone" placeholder="Phone Number" required value="{{ old('branch_phone') }}">
                    </div>
                      <div class="field required">
                        <label>Email</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_email" placeholder="Branch Name" required value="{{ old('branch_email') }}">
                    </div>
                    <div class="field required">
                        <label>Branch Address</label>
                        <textarea name="branch_address"></textarea>
                    </div>

                   <!--  <div class="field required">
                        <label>Notes</label>
                        <textarea name="notes" placeholder="notes" rows="4">{{ old('special_remarks') }}</textarea>
                    </div>
                    <div class="field required">
                        <label>Date</label>
                        <div class="ui labeled input">

                            <input type="date"  class="datepicker" name="date" placeholder="Date" required value=""/>
                        </div>
                    </div> -->

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
    <!-- Edit product modals -->
    @foreach ($products as $product)

    <div class="ui tiny modal" id="editProduct{{ $product->id }}Modal">
        <div class="header">{{ trans('branches.modal.header.edit') }}</div>
        <div class="scrolling content">
            <p>{{ trans('branches.modal.instruction.edit') }}</p>
            <form action="{{ route('branches.edit', $product->id) }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>{{ trans('branches.input.label.branch_name') }}</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_name" placeholder="{{ trans('branches.input.placeholder.branch_name') }}" required value="{{ $product->branch_name }}">
                    </div>
                    <div class="field required">
                        <label>Branch Code</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_code" placeholder="Branch Code" required value="{{ $product->branch_code }}">
                    </div>
                    <div class="field required">
                        <label>{{ trans('branches.input.label.branch_location') }}</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_location" placeholder="{{ trans('branches.input.placeholder.branch_location') }}" required value="{{ $product->branch_location }}">
                    </div>
                    <div class="field required">
                        <label>{{ trans('branches.input.label.branch_email') }}</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_email" placeholder="{{ trans('branches.input.placeholder.branch_email') }}" required value="{{ $product->branch_email }}">
                    </div>
                    <div class="field required">
                        <label>{{ trans('branches.input.label.branch_phone') }}</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_phone" placeholder="{{ trans('branches.input.placeholder.branch_phone') }}" required value="{{ $product->branch_phone }}">
                    </div>
                     <div class="field required" style="display: none">
                        <label>{{ trans('branches.input.label.branch_phone') }}</label>
                        <input type="text" maxlength="64" minlength="3" name="branch_id" placeholder="{{ trans('branches.input.placeholder.branch_phone') }}" required value="{{ $product->id}}">
                    </div>
                    <div class="field required">
                        <label>Branch Address</label>
                        <textarea name="branch_address">{{ $product->branch_address}}</textarea>
                    </div>
                <!--     <div class="field required">
                        <label>{{ trans('products.input.label.company') }}</label>
                        <select class="ui fluid search dropdown" name="insurer" required>
                            @foreach($companies1 as $company)
                            <option{{ $company->id === $product->branch_id ? ' selected' : '' }} value="{{ $company->name }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div> -->

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
    @endforeach
@endsection

@section('extra_scripts')
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                $('div.modal select[name="company_id"]').change(function() {
                    var element = $(this);
                    var parentModal = element.parents('div.modal:first');
                    var companyElementsHTML = parentModal.find('div#company' + element.val() + 'ProductCategoryTemplate').html();
                    parentModal.find('form div.company').remove();
                    parentModal.find('form div.field').last().after(companyElementsHTML);
                    $insura.helpers.initDropdown(parentModal.find('form div.company div.dropdown'));
                    $insura.helpers.requireDropdownFields(parentModal.find('form div.required div.dropdown input[type="hidden"]'));
                }).change();
            });
        })(window.insura, window.jQuery);
    </script>
@endsection
