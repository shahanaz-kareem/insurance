@extends('admin.layout')
<style>
.wrapper.wrapper2{
	display: block;
}
.wrapper{
	display: none;
}
</style>
	 <div id="printPolicyModal" class="ui segment white fs-16">
                <div class="segment-header">
                    <h3>{{ trans('policies.label.details') }}</h3>
                    @yield('policy_actions')
                </div>
                <div class="policy-details">
                    <p>ACK Details</p>
                    <hr style=" border-top: 1px solid #000000;">
                    <div class="row">
                        <div class="col-sm-3 col-md-3">
                            <span>Branch</span>
                            @foreach($branches as $branch)
                            <p><?php if($branch->id==$policy->branch_id){echo $branch->branch_name;}?></p>
                            @endforeach
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <span>ACK Date</span>
                            <p>{{ $policy->ack_date }}</p>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <span>ACK No</span>
                            <p>{{ $policy->ack_number }}</p>
                        </div>
                        <div class="col-sm-3 col-md-3">
                            <span class="ui blue right ribbon huge label">{{$policy->type}}</span>
                        </div>
                    </div>
                    <p>Policy Details</p>
                    <hr style=" border-top: 1px solid #000000;">
                    <div class="row">
                        <div class="col-sm-3 col-md-4">
                            <span>Application No</span>
                            <p>{{ $policy->application_no }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Life Assured</span>
                            <p>{{ $policy->payer }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Nominee</span>
                            <p>{{ $policy->beneficiaries }}</p>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-3 col-md-4">
                            <span>Policy No</span>
                            <p>{{ $policy->policy_no }}</p>
                        </div>

                        <div class="col-sm-6 col-md-8">
                            <span>Policy Name</span>
                            <p>{{ $policy->product->name }} </p>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <span>Policy Date</span>
                            <p>{{ $policy->policy_date }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Policy Amount</span>
                            <p>{{ $policy->premium }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Payment Term(Years)</span>
                            <p>{{ $policy->payment_term }}</p>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <span>Mode of Payment</span>
                            <p>{{ $policy->type }} </p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Policy Term</span>
                            <p>{{ $policy->policy_term }}</p>
                        </div>
                    </div>

                    <p>Payment Details</p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">
                        <div class="col-sm-3 col-md-4">
                            <span>Premium Cheque Amount</span>
                            <p>{{ $policy->premium_chq_amount }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Premium Cheque Date</span>
                            <p>{{ $policy->premium_chq_date }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Premium Cheque No</span>
                            <p>{{ $policy->premium_chq_no }} </p>
                        </div>

                    </div>

                    <p>Staff Details</p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">
                        <div class="col-sm-3 col-md-4">
                            <span>Staff Code</span>
                            @foreach($staffs as $staff)
                            <p><?php if($staff->id==$policy->staff_id){echo $staff->staff_code.'-'.$staff->first_name.''.$staff->last_name;}?></p>
                            @endforeach

                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Branch Manager</span>
                            @foreach($branch_manger as $bm)
                            <p><?php if($bm->id==$policy->branch_manager){echo $bm->staff_code.'-'.$bm->first_name.''.$bm->last_name;}?></p>
                            @endforeach

                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Branch Assist</span>
                            @foreach($branch_assist as $ba)
                            <p><?php if($ba->id==$policy->branch_assist){echo $ba->staff_code.'-'.$ba->first_name.''.$ba->last_name;}?></p>
                            @endforeach

                        </div>

                    </div>

                    <p>Depositor Details</p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">
                        <!-- <div class="col-sm-3 col-md-3">
                            <span>PIN</span>
                            <p>{{ $policy->pin }}</p>
                        </div> -->
                        <div class="col-sm-3 col-md-4">
                            <span>ECS Mandate</span>
                            <p>{{ $policy->ecs_mandate }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Bank Name</span>
                            <p>{{ $policy->bank_name }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Bank Branch</span>
                            <p>{{ $policy->bank_branch }}</p>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4">
                            <span>{{ trans('policies.label.renewal') }}</span>
                            <p>{{ date('F d, Y', strtotime($policy->renewal)) }}</p>
                        </div>
                        <div class="col-sm-8 col-md-4">
                            <span>{{ trans('policies.label.expiry') }}</span>
                            <p>{{ date('F d, Y', strtotime($policy->expiry)) }}</p>
                        </div>
                    </div>
                    <div class="row">


                        <div class="col-sm-5 col-md-5">
                                @if ($policy->premium <= $policy->paid && $policy->paid > 0)
                                <span class="ui {{ $policy->statusClass }} large tag label"> <strong>{{ trans('policies.label.paid_in_full') }}</strong> </span>
                                @elseif ($policy->due > 0 && $policy->active)
                                <span class="ui {{ $policy->statusClass }} large tag label"> <strong>{{ trans('policies.label.due') }} -</strong> {{ $policy->currency_symbol }}{{ $policy->due }} </span>
                                @elseif ($policy->due > 0 && !$policy->active)
                                <span class="ui {{ $policy->statusClass }} large tag label"> <strong>{{ trans('policies.label.expired_and_due') }} -</strong> {{ $policy->currency_symbol }}{{ $policy->due }} </span>
                                @elseif ($policy->premium == $policy->paid && $policy->paid === 0)
                                <span class="ui yellow large tag label"> <strong>{{ trans('policies.label.free') }}</strong> </span>
                                @endif
                        </div>
                    </div>



                    <div class="divider"></div>

                    <div class="row">
                        @foreach ($policy->customFields->all() as $custom_field)
                        <div class="col-md-4">
                            <span>{{ $custom_field->label }}</span>
                            <p>{{ is_object($custom_field->value) ? $custom_field->value->choice : $custom_field->value }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
</body>