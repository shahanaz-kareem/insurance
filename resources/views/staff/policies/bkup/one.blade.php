<div class="container-fluid printhead" style="display: none;">

            <div class="col-xs-4">
                <h4>User</h4> @foreach($branch_manger as $bm)
                           <?php if($bm->id==$policy->branch_manager){echo $bm->first_name.''.$bm->last_name;}?>
                            @endforeach
            </div>
            <div class="col-xs-4">
                <h4>StaffCode</h4>@foreach($branch_manger as $bm)
                           <?php if($bm->id==$policy->branch_manager){echo $bm->staff_code;}?>
                            @endforeach
            </div>
             <div class="col-xs-4">
                <h4>Division</h4>
                @foreach($branches as $branch)
                    <?php if($branch->id==$policy->branch_id){echo $branch->branch_name;}?>
                @endforeach
            </div>
        </div>
@extends('templates.policies.one')

@section('client_action')
                <div class="m-t-25">
                    <a class="ui button positive" href="{{ route('clients.one', array($policy->client->id)) }}"><i class="user outline icon"></i> {{ trans('policies.button.profile') }} </a>
                </div>
@endsection

@section('policy_actions')
                    <div class="ui right floated successish button top m-w-140 right pointing dropdown" data-inverted="" data-tooltip="{{ trans('policies.menu.header.tooltip') }}" data-position="left center">
                        <i class="ion-more icon"></i>
                        <span class="text">{{ trans('policies.menu.header.button') }}</span>
                        <div class="menu">
                            <div class="header">
                                {{ trans('policies.menu.header.text') }}
                            </div>
                            <div class="divider"></div>
                            <div class="item">
                                <a href="#" data-target="#editPolicyModal" data-toggle="modal">
                                    <i class="write icon"></i> {{ trans('policies.menu.item.edit_policy') }}
                                </a>
                            </div>
                            <form action="{{ route('policies.delete', array($policy->id)) }}" class="item negative" method="POST" style="margin-bottom: 0px;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <a href="#" class="delete">
                                    <i class="trash icon"></i> {{ trans('policies.menu.item.delete') }}
                                </a>
                            </form>
                            <div class="item">
                                <a href="#" onclick="printPolicyModal();"= data-target="#printPolicyModal" data-toggle="modal">
                                    <i class="print icon"></i> {{ trans('policies.menu.item.print') }}
                                </a>
                            </div>
                        </div>
                    </div>
@endsection

@section('payments_button')
                    <button class="ui right floated button successish m-w-140" data-target="#newPaymentModal" data-toggle="modal">{{ trans('payments.button.new') }}</button>
@endsection

@section('policy_modals')
    <!-- new payment modal -->
    <div class="ui tiny modal" id="newPaymentModal">
        <div class="header">{{ trans('payments.modal.header.new') }}</div>
        <div class="content">
            <p>{{ trans('payments.modal.instruction.new', array(
                'name'  => $policy->client->first_name
            )) }}</p>
            <form action="{{ route('payments.add') }}"method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="client" value="{{ $policy->client->id }}"/>
                <input type="hidden" name="policy" value="{{ $policy->id }}"/>
                <div class="ui form">
                    <div class="field required">
                        <label>{{ trans('payments.input.label.method') }}</label>
                        <div class="ui selection dropdown">
                            <input type="hidden" name="method">
                            <div class="default text">{{ trans('payments.input.placeholder.method') }}</div>
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <div class="item{{ old('method') === 'card' ? ' selected' : null }}" data-value="card">
                                    <i class="credit card alternative icon"></i>
                                    {{ trans('payments.input.option.method.card') }}
                                </div>
                                <div class="item{{ old('method') === 'cash' ? ' selected' : null }}" data-value="cash">
                                    <i class="money icon"></i>
                                    {{ trans('payments.input.option.method.cash') }}
                                </div>
                                <div class="item{{ old('method') === 'paypal' ? ' selected' : null }}" data-value="paypal">
                                    <i class="paypal card icon"></i>
                                    {{ trans('payments.input.option.method.paypal') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field required">
                            <label>{{ trans('payments.input.label.date') }}</label>
                            <div class="ui labeled input">
                                <label for="paymentDate" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" class="datepicker" id="paymentDate" name="date" placeholder="{{ trans('payments.input.placeholder.date') }}" required value="{{ old('date') }}"/>
                            </div>
                        </div>
                        <div class="field required">
                            <label>{{ trans('payments.input.label.amount') }}</label>
                            <div class="ui labeled input">
                                <label for="amount" class="ui label">{{ $policy->currency_symbol }}</label>
                                <input type="number" id="amount" max="{{ $policy->due }}" name="amount" placeholder="{{ trans('payments.input.placeholder.amount') }}" required value="{{ old('amount') }}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui buttons">
                <button class="ui cancel button">{{ trans('payments.modal.button.cancel.new') }}</button>
                <div class="or" data-text="{{ trans('payments.modal.button.or') }}"></div>
                <button class="ui positive primary button">{{ trans('payments.modal.button.confirm.new') }}</button>
            </div>
        </div>
    </div>

    <!-- edit policy modal -->
    <div class="ui tiny modal" id="editPolicyModal" style="width: 60%;">
        <div class="header">{{ trans('policies.modal.header.edit') }}</div>
        <div class="scrolling content">
            <p>{{ trans('policies.modal.instruction.edit') }}</p>
            <form action="{{ route('policies.edit', array($policy->id)) }}" method="POST">
                {{ csrf_field() }}
                <div class="ui form">
                    <div class="col-md-6">
                        <p>ACK Details</p>
                        <hr style=" border-top: 1px solid #000000;">
                        <div class="field required">
                            <label>{{ trans('policies.input.label.branch') }}</label>
                            <select class="ui fluid search dropdown" name="branch">
                            @foreach($branches as $branch)
                                @if(Auth::user()->branch_id == $branch->id)
                                <option value="{{ $branch->id }}" @if($branch->id == $policy->branch_id) selected @endif>{{ $branch->branch_name }}</option>
                                @endif
                            @endforeach
                            </select>
                        </div>
                        <div class="field required">
                            <label>ACK Date</label>
                            <div class="ui labeled input">
                                <label for="ack_date" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" id="ack_date" class="datepicker" name="ack_date" placeholder="Ack Date" required value="{{ old('ack_date') ?: $policy->ack_date }}"/>
                            </div>
                        </div>
                        <div class="field required">
                            <label>ACK No.</label>
                            <div class="ui labeled input">
                                <label for="ack_number" class="ui label"></label>
                                <input type="text" id="ack_number"  name="ack_number" placeholder="Ack Number" required value="{{ old('ack_number') ?: $policy->ack_number }}"/>
                            </div>
                        </div>
                        <p>Policy Details</p>
                        <hr style=" border-top: 1px solid #000000;">
                        <div class="field required">
                            <label>Application No.</label>
                            <input type="text" name="application_no" maxlength="64" placeholder="Application No" required value="{{ old('application_no') ?: $policy->application_no }}"/>
                        </div>

                        <div class="field required">
                            <label>Nominee</label>
                            <input type="text" name="beneficiaries" placeholder="Nominee" required value="{{ old('beneficiaries') ?: $policy->beneficiaries }}"/>
                        </div>
                        <div class="field">
                            <label>Policy No.</label>
                            <input type="text" name="policy_no" placeholder="Policy No." value="{{ old('policy_no') ?: $policy->policy_no }}"/>
                        </div>
                        <div class="field required">
                            <label>Point of Login</label>
                            <input type="text" name="point_login" id="point_login" placeholder="Point of Login" value="{{ old('point_login') ?: $policy->point_login}}" required/>
                        </div>
                        <div class="field required">
                            <label>Policy Date</label>
                            <div class="ui labeled input">
                                <label for="policy_date" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" id="policy_date" class="datepicker" name="policy_date" placeholder="policy date" required value="{{ old('policy_date') ?: $policy->policy_date }}"/>
                            </div>
                        </div>
                        <div class="field required">
                            <label>{{ trans('policies.input.label.product') }}</label>
                            <select class="ui fluid search dropdown" name="product" required value="{{ old('product') ?: $policy->product->id }}">
                                <option value="">{{ trans('policies.input.placeholder.product') }}</option>
                                @forelse ($policy->client->company->products as $product)
                                <option{{ old('product') === $product->id || $policy->product->id === $product->id ? ' selected' : '' }} value="{{ $product->id }}">{{ $product->name }}</option>
                                @empty
                                <option disabled value="">{{ trans('policies.input.option.empty.product') }}</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="field">
                            <label>{{ trans('policies.input.label.expiry') }}</label>
                            <div class="ui labeled input">
                                <label for="expiry" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" id="expiry" class="datepicker" name="expiry" placeholder="{{ trans('policies.input.placeholder.expiry') }}" value="{{ old('expiry') ?: $policy->expiry }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>{{ trans('policies.input.label.renewal') }}</label>
                            <div class="ui labeled input">
                                <label for="renewal" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" id="renewal" class="datepicker" name="renewal" placeholder="{{ trans('policies.input.placeholder.renewal') }}" value="{{ old('renewal') ?: $policy->renewal }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>Sum Assured Amount</label>
                            <input type="text" name="sum_assured" id="sum_assured" placeholder="Sum Assured Amount" value="{{ old('sum_assured') ?: $policy->sum_assured }}" />
                        </div>
                        <div class="field required" data-input-name="premium">
                            <label>Policy Amount</label>
                            <div class="ui labeled input">
                                <label for="premium" class="ui label">{{ $policy->currency_symbol }}</label>
                                <input type="number" id="premium" min="0" name="premium" placeholder="Policy Amount" required value="{{ old('premium') ?: $policy->premium }}" class="policy_amount" readonly/>
                            </div>
                        </div>
                        <div class="field required" data-input-name="premium">
                            <label>Premium Amount</label>
                            <div class="ui labeled input">
                                <label for="premium" class="ui label">{{ $policy->currency_symbol }}</label>
                                <input type="number" id="premium_amount" min="0" name="premium_amount" placeholder="Premium Amount" required value="{{ old('premium_amount') ?: $policy->premium_amount }}" class="p_due" onkeyup="calculate();"/>
                            </div>
                        </div>

                        <div class="field required" data-input-name="premium">
                            <label>Payment Term</label>
                            <input type="text" name="payment_term" placeholder="Payment Term" value="{{ old('payment_term') ?: $policy->payment_term }}" onkeyup="calculate();" required/>
                        </div>
                        <div class="field required">
                            <label>Mode Payment</label>
                            <select class="ui fluid dropdown selection" name="type" required value="{{ old('type') ?: $policy->type }}" onChange="calculate();" id="mode_payment">
                                <option value="">{{ trans('policies.input.placeholder.type') }}</option>
                                <option{{ old('type') === 'annually' || $policy->type === 'annually' ? ' selected' : '' }} value="annually">{{ trans('policies.input.option.type.annually') }}</option>
                                <option{{ old('type') === 'monthly' || $policy->type === 'monthly' ? ' selected' : '' }} value="monthly">{{ trans('policies.input.option.type.monthly') }}</option>
                                <!-- <option{{ old('type') === 'weekly' || $policy->type === 'weekly' ? ' selected' : '' }} value="weekly">{{ trans('policies.input.option.type.weekly') }}</option> -->

                                <option{{ old('type') === 'quarterly' || $policy->type === 'quarterly' ? ' selected' : '' }} value="quarterly">Quarterly</option>

                                <option{{ old('type') === 'half yearly' || $policy->type === 'half yearly' ? ' selected' : '' }} value="half yearly">Half yearly</option>
                            </select>
                        </div>
                        <div class="field required">
                            <label>Policy Term(In Years)</label>
                            <input type="text" name="policy_term" placeholder="Policy Term" value="{{ old('policy_term') ?: $policy->policy_term }}"/>
                        </div>
                        <p>Payment Details</p>
                        <hr style=" border-top: 1px solid #000000;">
                        <div class="field required" >
                            <label>Premium Chq. Amount</label>
                            <div class="ui labeled input">
                                <label for="premium_chq_amount" class="ui label"></label>
                                <input type="number" id="premium_chq_amount" min="0" name="premium_chq_amount" placeholder="Premium Chq. Amount" required value="{{ old('premium_chq_amount') ?: $policy->premium_chq_amount }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>Premium Chq. Date</label>
                            <div class="ui labeled input">
                                <label for="premium_chq_date" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" id="premium_chq_date" class="datepicker" name="premium_chq_date" placeholder="Premium Chq. Date" required value="{{ old('premium_chq_date') ?: $policy->premium_chq_date }}"/>
                            </div>
                        </div>
                        <div class="field required" >
                            <label>Premium Chq. No</label>
                            <div class="ui labeled input">
                                <label for="premium_chq_no" class="ui label"></label>
                                <input type="text" id="premium_chq_no" min="0" name="premium_chq_no" placeholder="Premium Chq. No" required value="{{ old('premium_chq_no') ?: $policy->premium_chq_no }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <p>Staff Details</p>
                        <hr style=" border-top: 1px solid #000000;">
                        <div class="field">
                            <label>Staff</label>
                            <select class="ui fluid search dropdown" name="staff">

                                    @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}" @if($staff->id == $policy->staff_id) selected @endif>{{ $staff->staff_code }} - {{ $staff->first_name }} {{ $staff->last_name }}</option>
                                    @endforeach

                            </select>
                        </div>
                        <div class="field required">
                            <label>Branch Manager</label>
                            <select class="ui fluid search dropdown" name="branch_manager">
                                    @foreach($branch_manger as $bm)
                                        <option value="{{ $bm->id }}" @if($bm->id == $policy->staff_id) selected @endif>{{ $bm->staff_code }} - {{ $bm->first_name }} {{ $bm->last_name }}</option>
                                    @endforeach

                            </select>
                        </div>
                        <div class="field">
                            <label>Branch Assist</label>
                            <select class="ui fluid search dropdown" name="branch_assist">

                                    @foreach($branch_assist as $ba)
                                        <option value="{{ $ba->id }}" @if($ba->id == $policy->staff_id) selected @endif>{{ $ba->staff_code }} - {{ $ba->first_name }} {{ $ba->last_name }}</option>
                                    @endforeach

                            </select>
                        </div>
                        <div class="field" style="display: none;">
                            <label>{{ trans('policies.input.label.payer') }}</label>
                            <input type="text" maxlength="64" name="payer" placeholder="{{ trans('policies.input.placeholder.payer') }}" value="{{ old('payer') ?: $policy->payer }}"/>
                        </div>
                        <p>Depositor Details</p>
                        <hr style=" border-top: 1px solid #000000;">

                        <div class="field required">
                            <label>ECS Mandate</label>
                            <select class="dropdown fluid search ui" name="ecs_mandate" >
                                <option value="yes" <?php if($policy->ecs_mandate=='yes'){echo 'selected';}?>>Yes</option>
                                <option value="no" <?php if($policy->ecs_mandate=='no'){echo 'selected';}?>>No</option>

                            </select>
                        </div>
                        <div class="field required">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" maxlength="64" placeholder="Bank Name"  value="{{ old('bank_name') ?: $policy->bank_name }}"/>
                        </div>
                        <div class="field " style="display: none;">
                            <label>PIN</label>
                            <input type="text" name="pin" maxlength="64" placeholder="PIN"  value="{{ old('pin') ?: $policy->pin }}"/>
                        </div>
                        <div class="field required">
                            <label>Bank Branch</label>
                            <input type="text" name="bank_branch" maxlength="64" placeholder="Bank Branch" required value="{{ old('bank_branch') ?: $policy->bank_branch }}"/>
                        </div>
                        <b><p>Life Assured Details</p></b>
                        <hr style=" border-top: 1px solid #000000;">

                        <div class="field">
                            <label>Life Assured Name</label>
                            <input type="text" name="deposit_name"  placeholder="Life Assured Name"  value="{{ old('deposit_name') ?: $policy->deposit_name }}"/>
                        </div>
                        <div class="field">
                            <label>DOB</label>
                            <div class="ui labeled input">
                                <label for="ldob" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" id="ldob" class="datepicker" name="ldob" placeholder="DOB"  value="{{ old('ldob') ?: $policy->ldob }}"/>
                            </div>
                        </div>
                        <div class="field ">
                            <label>Mobile Number</label>
                            <input type="text" name="lmnum" maxlength="10" placeholder="Mobile Number"  value="{{ old('lmnum') ?: $policy->lmnum }}"/>
                        </div>

                        <div class="field ">
                            <label>Email</label>
                            <input type="email" name="lemail" placeholder="{{ trans('staff.input.placeholder.email') }}"  value="{{ old('lemail') ?: $policy->lemail }}"/>
                        </div>

                        <b><p>Nominee Details</p></b>
                        <hr style=" border-top: 1px solid #000000;">

                        <div class="field required">
                            <label>Nominee Name</label>
                            <input type="text" name="beneficiaries" placeholder="Nominee" required value="{{ old('beneficiaries') ?: $policy->beneficiaries }}"/>
                        </div>
                        <div class="field">
                            <label>DOB</label>
                            <div class="ui labeled input">
                                <label for="ndob" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" id="ndob" class="datepicker" name="ndob" placeholder="DOB"  value="{{ old('ndob') ?: $policy->ndob }}"/>
                            </div>
                        </div>
                        <div class="field ">
                            <label>Mobile Number</label>
                            <input type="text" name="nmnum" maxlength="10" placeholder="Mobile Number"  value="{{ old('nmnum') ?: $policy->nmnum }}"/>
                        </div>

                        <div class="field ">
                            <label>Email</label>
                            <input type="email" name="nemail" placeholder="{{ trans('staff.input.placeholder.email') }}"  value="{{ old('nemail') ?: $policy->nemail }}"/>
                        </div>
                    <div class="divider"></div>
                    @foreach ($policy->customFields->all() as $custom_field)
                    <input type="hidden" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][label]" value="{{ $custom_field->label }}"/>
                    <input type="hidden" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][type]" value="{{ $custom_field->type }}"/>
                    <input type="hidden" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][uuid]" value="{{ $custom_field->uuid }}"/>
                        @if ($custom_field->type === 'checkbox')
                    <div class="field{{ isset($custom_field->required) ? ' required' : '' }}">
                        <div class="ui checkbox">
                            <input type="checkbox"{{ isset($custom_field->value) ? ' checked' : '' }} name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]"{{ isset($custom_field->required) ? ' required' : '' }}>
                            <label>{{ $custom_field->label }}</label>
                        </div>
                    </div>
                        @elseif ($custom_field->type === 'date')
                    <div class="field{{ isset($custom_field->required) ? ' required' : '' }}">
                        <label>{{ $custom_field->label }}</label>
                        <input type="text" class="datepicker" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]""{{ isset($custom_field->required) ? ' required' : '' }} value="{{ $custom_field->value }}">
                    </div>
                        @elseif ($custom_field->type === 'email')
                    <div class="field{{ isset($custom_field->required) ? ' required' : '' }}">
                        <label>{{ $custom_field->label }}</label>
                        <input type="email" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]"{{ isset($custom_field->required) ? ' required' : '' }} value="{{ $custom_field->value }}">
                    </div>
                        @elseif ($custom_field->type === 'hidden')
                    <input type="hidden" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]" value="{{ $custom_field->value }}">
                        @elseif ($custom_field->type === 'number')
                    <div class="field{{ isset($custom_field->required) ? ' required' : '' }}">
                        <label>{{ $custom_field->label }}</label>
                        <input type="number" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]""{{ isset($custom_field->required) ? ' required' : '' }} value="{{ $custom_field->value }}">
                    </div>
                        @elseif ($custom_field->type === 'select')
                            @foreach ($custom_field->value->choices as $option)
                    <input type="hidden" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value][choices][]" value="{{ $option }}"/>
                            @endforeach
                            @if (count($custom_field->value->choices) > 2)
                    <div class="field{{ isset($custom_field->required) ? ' required' : '' }}">
                        <label>{{ $custom_field->label }}</label>
                        <select class="ui fluid search dropdown" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value][choice]">
                            <option value="">{{ $custom_field->label }}</option>
                                @foreach ($custom_field->value->choices as $option)
                            <option{{ $option === $custom_field->value->choice ? ' selected' : '' }} value="{{ trim($option) }}">{{ trim($option) }}</option>
                                @endforeach
                        </select>
                    </div>
                            @else
                    <div class="inline fields{{ isset($custom_field->required) ? ' required' : '' }}">
                        <label>{{ $custom_field->label }}</label>
                                @foreach ($custom_field->value->choices as $option)
                        <div class="field">
                            <div class="ui radio checkbox">
                                <input type="radio"{{ $option === $custom_field->value->choice ? ' checked' : '' }} name="custom_fields[C{{ $company->id }}F{{ $custom_field->id }}][value][choice]"{{ isset($custom_field->required) ? ' required' : '' }} value="{{ trim($option) }}">
                                <label>{{ trim($option) }}</label>
                            </div>
                        </div>
                                @endforeach
                    </div>
                            @endif
                        @elseif ($custom_field->type === 'tel')
                    <div class="field{{ isset($custom_field->required) ? ' required' : '' }}">
                        <label>{{ $custom_field->label }}</label>
                        <input type="tel" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]"{{ isset($custom_field->required) ? ' required' : '' }} value="{{ $custom_field->default }}">
                    </div>
                        @elseif ($custom_field->type === 'text')
                    <div class="field">
                        <label>{{ $custom_field->label }}</label>
                        <input type="text" name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]"{{ isset($custom_field->required) ? ' required' : '' }} value="{{ $custom_field->default }}">
                    </div>
                        @else
                    <div class="field">
                        <label>{{ $custom_field->label }}</label>
                        <textarea name="custom_fields[C{{ $policy->client->company->id }}F{{ $custom_field->id }}][value]"{{ isset($custom_field->required) ? ' required' : '' }} rows="2">{{ $custom_field->default }}</textarea>
                    </div>
                        @endif
                    @endforeach
                </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui buttons">
                <button class="ui cancel button">{{ trans('policies.modal.button.cancel.edit') }}</button>
                <div class="or" data-text="{{ trans('policies.modal.button.or') }}"></div>
                <button class="ui positive primary button">{{ trans('policies.modal.button.confirm.edit') }}</button>
            </div>
        </div>
    </div>
@endsection

@section('extra_scripts')
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                $insura.helpers.initModal('div#newPaymentModal', true);
                $insura.helpers.initModal('div#editPolicyModal', false);
                $insura.helpers.initSwal('form a.delete', {
                    confirmButtonText: '{{ trans('policies.swal.warning.delete.confirm') }}',
                    text: '{{ trans('policies.swal.warning.delete.text') }}',
                    title: '{{ trans('policies.swal.warning.delete.title') }}'
                });
            });
        })(window.insura, window.jQuery);
    </script>
    <script type="text/javascript">
        function calculate(){

            var mode_payment   =    $('#mode_payment').val();
            var payment_term   =    $('#payment_term').val();
            var amount         =    $('.p_due').val();
            //alert(mode_payment);
            if(payment_term ==''){
                // alert('Please enter paymnet term');
                // return false;
                payment_term = 0;
            } else if(amount ==''){
                // alert('Please enter premium amount');
                // return false;
                amount = 0;
            } else {
                if(mode_payment=='annually'){
                    var p_amount        = parseInt(payment_term) * 1;
                    var policy_amount   = parseInt(p_amount) * parseInt(amount) ;
                    $('.policy_amount').val(policy_amount);
                }

                else if(mode_payment=='monthly'){
                    var p_amount        = parseInt(payment_term )* 12;
                    var policy_amount   = parseInt(p_amount) * parseInt(amount);
                    $('.policy_amount').val(policy_amount);
                }
                else if(mode_payment=='quarterly'){
                    var p_amount        = parseInt(payment_term) * 3;
                    var policy_amount   = parseInt(p_amount) * parseInt(amount) ;
                    $('.policy_amount').val(policy_amount);
                }
                else if(mode_payment=='half yearly'){
                    var p_amount        = parseInt(payment_term) * 6;
                    var policy_amount   = parseInt(p_amount) * parseInt(amount) ;
                    $('.policy_amount').val(policy_amount);
                } else {
                    $('.policy_amount').val('');
                }
            }
        }
        function  printPolicyModal()
        {
              $(".right-bar-profile").hide();
            $(".aside-menu").hide();
            $(".page-content").hide();
             $(".humbager").hide();
             $(".header-avatar").hide();
             $(".branding").hide();
             $(".successish").hide();
             $(".printhead").show();
             $("#printpolicy").show();
            $(".print:last-child").css('page-break-after', 'auto');

               $(".page-title .with-desc").show();
            window.print();

             $(".right-bar-profile").show();
            $(".aside-menu").show();
            $(".segment-header").show();
            $(".page-content").show();
            $("#printpolicy").hide();
            $(".humbager").show();
             $(".header-avatar").show();
             $(".branding").show();
             $(".successish").show();
             $(".printhead").hide();
        }
    </script>
@endsection
