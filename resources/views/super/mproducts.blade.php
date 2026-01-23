@extends('templates.mproducts')

@section('modals')
    <!-- New product modal -->
    <div class="ui tiny modal" id="newProductModal">
        <div class="header">Mutual Fund Products</div>
        <div class="scrolling content">
            <p>Mutual Fund Products</p>
            <form action="{{ route('mproducts.add') }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>Comapny Name</label>
                        <input type="text"  name="company_name" placeholder="Company Name" id="company_name" required value="{{ old('company_name') }}">
                        <div id="livesearch"></div>
                    </div>
                    <div class="field required">
                        <label>Scheme Name</label>
                        <input type="text" maxlength="64" minlength="3" name="product_name" placeholder="Scheme Name" required value="{{ old('product_name') }}">
                    </div>


                      <div class="field required">
                        <label>Status</label>
                        <select class="dropdown fluid search ui" name="status" required>
                            <option value="A">Active</option>
                            <option value="I">Inactive</option>

                        </select>
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
    <!-- Edit product modals -->
    @foreach ($mproducts as $mproduct)

    <div class="ui tiny modal" id="editProduct{{ $mproduct->id }}Modal">
        <div class="header">{{ trans('branches.modal.header.edit') }}</div>
        <div class="scrolling content">
            <p>{{ trans('branches.modal.instruction.edit') }}</p>
            <form action="{{ route('mproducts.edit', $mproduct->id) }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>Company Name</label>
                        <input type="text"  name="company_name" id="company_name{{ $mproduct->id }}"  placeholder="Company Name" required value="{{ $mproduct->company_name }}">
                        <div id="livesearch{{ $mproduct->id }}"></div>
                    </div>
                    <div class="field required">
                        <label>Product Name</label>
                        <input type="text" maxlength="64" minlength="3" name="product_name" placeholder="Product Name" required value="{{ $mproduct->product_name }}">
                    </div>
                    <div class="field required">
                        <label>Status</label>
                        <select class="dropdown fluid search ui" name="status" required>
                            <option value="A" {{$mproduct->status ==  'A' ?'selected' :''}}>Active</option>
                            <option value="I" {{$mproduct->status == 'I' ?'selected' :''}}>InActive</option>

                        </select>
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

    @endforeach
@endsection

@section('extra_scripts')
    <!-- <script type="text/javascript">
        function fun(){
            var name = $('#company_name').val();
            alert(name);

        };
    </script> -->
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
                $('#company_name').keyup(function() {
                    var name = $('#company_name').val();

                    $.ajax({
                        url: "http://integrityindia.co.in/insurance/public/suggest",
                        data: {
                            'name' : name
                        },
                        type:'POST',
                        success: function(result){
                            $("#livesearch").html(result);
                            $("#livesearch").show();
                            $("#livesearch select option").click(function(){
                                $('#company_name').val(this.value);
                                $("#livesearch").hide();
                            });
                        }
                    });
                });
            });
        })(window.insura, window.jQuery);
    </script>
@foreach ($mproducts as $mproduct)
<script type="text/javascript">
        (function($insura, $) {

                $("#company_name<?php echo $mproduct->id; ?>").keyup(function() {
                    var name = $("#company_name<?php echo $mproduct->id; ?>").val();

                    $.ajax({
                        url: "http://integrityindia.co.in/insurance/public/suggest",
                        data: {
                            'name' : name
                        },
                        type:'POST',
                        success: function(result){
                            $("#livesearch<?php echo $mproduct->id; ?>").html(result);
                            $("#livesearch<?php echo $mproduct->id; ?>").show();
                            $("#livesearch<?php echo $mproduct->id; ?> select option").click(function(){
                                $("#company_name<?php echo $mproduct->id; ?>").val(this.value);
                                $("#livesearch<?php echo $mproduct->id; ?>").hide();
                            });
                        }
                    });
                });

        })(window.insura, window.jQuery);
    </script>
@endforeach

@endsection
