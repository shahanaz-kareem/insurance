@extends('templates.funds')

@section('modals')
    <!-- New product modal -->
    <div class="ui tiny modal" id="newProductModal">
        <div class="header">New Fund</div>
        <div class="scrolling content">
            <p>New Fund</p>
            <form action="{{ route('mutualfunds.add') }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>Amount</label>
                        <input type="text" maxlength="64" minlength="3" name="amount" placeholder="Amount" required value="{{ old('name') }}">
                    </div>

                    <div class="field required" >
                        <label>Branch</label>
                        <select class="ui fluid search dropdown" name="branch">
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field required">
                        <label>Notes</label>
                        <textarea name="notes" placeholder="notes" rows="4">{{ old('special_remarks') }}</textarea>
                    </div>
                    <div class="field required">
                        <label>Date</label>
                        <div class="ui labeled input">

                            <input type="date"  class="datepicker" name="date" placeholder="Date" required value=""/>
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
    <!-- Edit product modals -->
    @foreach ($funds as $fund)

    <div class="ui tiny modal" id="editProduct{{ $fund->mid }}Modal">
        <div class="header">{{ trans('products.modal.header.edit') }}</div>
        <div class="scrolling content">
            <p>Edit Mutual Fund</p>
            <form action="{{ route('mutualfunds.edit', $fund->mid) }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="field required">
                        <label>ACK Number</label>
                        <input type="text"   name="mack_no" placeholder="ACK Number" required value="{{ $fund->mack_no }}">
                    </div>
                    <div class="field required">
                        <?php $mack_date = date("d-m-Y", strtotime($fund->mack_date)); ?>
                        <label>ACK Date</label>
                        <div class="ui labeled input">
                            <label for="mackdate" class="ui label"><i class="calendar icon"></i></label>
                            <input type="text" id="mackdate" class="datepicker" name="mack_date" placeholder="ACK Date" required value="{{ $mack_date }}"/>
                        </div>
                    </div>
                    <div class="field required">
                        <label>Branches</label>
                        <select class="ui fluid search dropdown" name="branch" required>
                            @foreach($branches as $branch)
                                <option {{ $branch->id == $fund->branch_id ? ' selected' : '' }} value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field required" >
                        <label>Client Name</label>
                        <select class="ui fluid search dropdown" name="client">
                            @foreach($clients as $client)
                                <option {{ $client->id == $fund->client_id ? ' selected' : '' }} value="{{ $client->id }}">{{ $client->first_name.''.$client->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field required" >
                        <label>Product</label>
                        <select class="ui fluid search dropdown" name="product">
                            @foreach($products as $product)
                                <option {{ $product->id == $fund->m_product ? ' selected' : '' }} value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field required">
                        <label>Amount</label>
                        <input type="text" maxlength="64" minlength="3" name="amount" placeholder="amount" required value="{{ $fund->amount }}">
                    </div>



                    <div class="field required">
                        
                        <label>Date</label>
                        <?php $date = date("d-m-Y", strtotime($fund->date)); ?>
                            <input type="text" class="datepicker" name="date" required placeholder="Date" value="{{ old('date') ?: $date}}"/>
                    </div>

                    <div class="field required">
                        <label>Notes</label>
                        <textarea name="notes" placeholder="notes" rows="4">{{ $fund->notes }}</textarea>
                    </div>

                    <input type="hidden" name="fund_id" value="{{ $fund->mid}}">



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

