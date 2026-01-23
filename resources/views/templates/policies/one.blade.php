
@extends('global.app')

@section('title', trans('policies.title.one'))

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/datepicker/datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/dropify/css/dropify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets1/libs/sweetalert/sweetalert.css') }}" rel="stylesheet">
   <style type="text/css">
       @media print {
    html, body {
        height: 99%!important;

    }
    p
    {
        margin: 0px!important;
    line-height: 0.805em!important;
}
hr
{
    margin-top: 5px!important;
    margin-bottom: : 5px!important;


}
header
{
    height: 0px!important;
}
}
   </style>


@endsection

@section('extra_stylesheets')
    <link href="{{ asset('assets1/css/split-page.css') }}" rel="stylesheet"/>
@endsection

@section('profile_bar')
        <!-- client profile -->



        <div class="ui segment white right-bar-profile right-bar-profile-bottom">
            <div class="user-profile m-b-15">
                @if ($policy->client->profile_image_filename === 'default-profile.jpg')
                <div class="text-avatar" style="background-color:{{ collect(config('insura.colors'))->random() }};">{{ strtoupper($policy->client->first_name[0] . $policy->client->last_name[0]) }}</div>
                @else
                <img src="{{ asset('uploads/images/users/' . $policy->client->profile_image_filename) }}" alt="{{ $policy->client->first_name . ' ' . $policy->client->last_name }}"/>
                @endif
                <h3>{{ $policy->client->first_name . ' ' . $policy->client->last_name }}</h3>
                <span>
                    @if ($policy->client->password === 'InsuraPasswordsAreLongButNeedToBeSetByInvitedUsersSuchAsThis')
                    <i class="ion-ios-circle-filled text-danger"></i> {{ trans('clients.status.inactive') }}
                    @else
                    <i class="ion-ios-circle-filled text-success"></i> {{ trans('clients.status.active') }}
                    @endif
                </span>
                @yield('client_action')
            </div>
            <div class="scrollbar">
                <div class="user-more-data">
                    <div class="divider m-t-0"></div>
                    <!-- client details -->
                    <div class="user-contact">
                        <div>
                            <span>{{ trans('clients.label.email') }}</span>
                            <p>{{ $policy->client->email }}</p>
                        </div>
                        <div>
                            <span>{{ trans('clients.label.phone') }}</span>
                            <p>{{ $policy->client->phone }}</p>
                        </div>
                        <div>
                            <span>{{ trans('clients.label.birthday') }}</span>
                            <p>{{ is_null($policy->client->birthday) ? '---------- --, ----' : date('jS F Y', strtotime($policy->client->birthday)) }}</p>
                        </div>
                        <div>
                            <span>{{ trans('clients.label.address') }}</span>
                            <p>{{ $policy->client->address ?? '. . .' }}</p>
                        </div>
                    </div>
                    <!-- end client details -->
                </div>
            </div>
        </div>
        <!-- end client profile -->
@endsection

@section('content')
        @parent
        <!-- half page content -->

        <div class="half-page-content" >
            @include('global.status')
            <!-- Policy details -->
            <div class="ui segment white fs-16">
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
                            <?php if($branch->id==$policy->branch_id){echo $branch->branch_name;}?>
                            @endforeach
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <span>ACK Date</span>
                           <?php $ack_date = date("d-m-Y", strtotime($policy->ack_date)); ?>
                            <p>{{ $ack_date }}</p>
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
                            <span>Client Name</span>
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
                              <?php
                              if($policy->policy_date =='0000-00-00'){
                                $policy_date = '00-00-0000';
                              } else {
                                $policy_date = date("d-m-Y", strtotime($policy->policy_date));
                              }

                              ?>
                            <p>{{ $policy_date }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Policy Amount</span>
                            <p>{{ $policy->premium }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Premium Amount</span>
                            <p>{{ $policy->premium_amount }}</p>
                        </div>



                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <span>Payment Term(Years)</span>
                            <p>{{ $policy->payment_term }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Mode of Payment</span>
                            <p>{{ $policy->type }} </p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Policy Term</span>
                            <p>{{ $policy->policy_term }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <span>Sum Assured Amount</span>
                            <p>{{ $policy->sum_assured }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Point of Login</span>
                            <p>{{ $policy->point_login }} </p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Next Renewal Date</span>
                            <?php $next_renewal_date = date("d-m-Y", strtotime($policy->renewal_date)); ?>
                            <p>{{ $next_renewal_date }}</p>
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
                             <?php $premium_chq_date = date("d-m-Y", strtotime($policy->premium_chq_date)); ?>
                            <p>{{ $premium_chq_date }}</p>
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
                                    <p></p>
                            @foreach($staffs as $staff)
                           <?php if($staff->id==$policy->staff_id){echo $staff->staff_code.'-'.$staff->first_name.''.$staff->last_name;}?>
                            @endforeach

                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Branch Manager</span>
                                    <p></p>
                            @foreach($branch_manger as $bm)
                           <?php if($bm->id==$policy->branch_manager){echo $bm->staff_code.'-'.$bm->first_name.''.$bm->last_name;}?>
                            @endforeach

                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span>Branch Assist</span>
                                    <p></p>
                            @foreach($branch_assist as $ba)
                           <?php if($ba->id==$policy->branch_assist){echo $ba->staff_code.'-'.$ba->first_name.''.$ba->last_name;}?>
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
                    <p>Life Assured Details</p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">

                        <div class="col-sm-3 col-md-4">
                            <span>Life Assured Name</span>
                            <p>{{ $policy->deposit_name }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>DOB</span>
                            <?php if($policy->ldob =='0000-00-00'){
                                $ldob = '00-00-0000';
                              } else {
                                $ldob = date("d-m-Y", strtotime($policy->ldob));
                              }
                             ?>
                            <p>{{ $ldob }}</p>

                        <!--     <p>{{ $policy->ldob }}</p> -->
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Mobile Number</span>
                            <p>{{ $policy->lmnum }}</p>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-md-3">
                            <span>Email</span>
                            <p>{{ $policy->lemail }}</p>
                        </div>
                    </div>
                    <p>Nominee Details</p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">

                        <div class="col-sm-3 col-md-4">
                            <span>Nominee Name</span>
                            <p>{{ $policy->beneficiaries }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>DOB</span>
                            <?php if($policy->ndob =='0000-00-00'){
                                $ndob = '00-00-0000';
                              } else {
                                $ndob = date("d-m-Y", strtotime($policy->ndob));
                              }
                             ?>

                            <p>{{ $ndob }}</p>
                       <!--      <p>{{ $policy->ndob }}</p> -->
                        </div>
                        <div class="col-sm-3 col-md-4">
                            <span>Mobile Number</span>
                            <p>{{ $policy->nmnum }}</p>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-md-3">
                            <span>Email</span>
                            <p>{{ $policy->nemail }}</p>
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

            <!-- attachments -->
            <div class="ui segment white fs-16">
                <div class="segment-header">
                    <h3>{{ trans('policies.table.title.attachments') }}</h3>
                    <button class="ui right floated button successish m-w-140" data-target="#newAttachmentModal" data-toggle="modal">{{ trans('attachments.button.new') }}</button>
                </div>
                <table class="ui celled striped table">
                    <thead>
                        <tr>
                            <th> {{ trans('policies.table.header.file') }} </th>
                            <th> {{ trans('policies.table.header.date') }} </th>
                            <th> {{ trans('policies.table.header.uploader') }} </th>
                            <th class="center aligned"> {{ trans('policies.table.header.actions') }} </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($policy->attachments as $attachment)
                        <tr>
                            <td>
                                <i class="file {{ array(
                                    'bmp'   => 'image',
                                    'doc'   => 'word',
                                    'docx'  => 'word',
                                    'gif'   => 'image',
                                    'jpeg'  => 'image',
                                    'jpg'   => 'image',
                                    'png'   => 'image',
                                    'ppt'   => 'powerpoint',
                                    'pptx'  => 'powerpoint',
                                    'pdf'   => 'pdf',
                                    'svg'   => 'image',
                                    'xls'   => 'excel',
                                    'xlsx'  => 'excel'
                                )[pathinfo(storage_path('app/attachments/' . $attachment->filename), PATHINFO_EXTENSION)] }} outline icon"></i> {{ $attachment->name }}
                            </td>
                            <td>{{ date('F d, Y', strtotime($attachment->created_at)) }}</td>
                            <td>{{ $attachment->uploader->first_name . ' ' . $attachment->uploader->last_name }}</td>
                            <td class="center aligned">
                                <a class="ui tiny grey label" href="{{ 'http://iworksync.com/ebs/insurance/storage/app/attachments/' . $attachment->filename}}" target="_blank"> {{ trans('policies.table.data.action.view') }} </a>
                                <form action="{{ route('attachments.delete', array($attachment->id)) }}" method="POST" style="display:inline;">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button class="delete label tiny red ui" style="cursor:pointer;" type="submit">{{ trans('policies.table.data.action.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="center aligned" colspan="4">{{ trans('policies.table.message.empty.attachments', array(
                                'policy' => $policy->ref_no
                            )) }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- end attachments -->

            <!-- payments -->
            <div class="ui segment white">
                <div class="segment-header">
                    <h3>{{ trans('policies.table.title.payments') }}</h3>
                    @if($policy->due > 0)
                        @yield('payments_button')
                    @endif
                </div>
                <table class="ui striped table">
                    <thead>
                        <tr>
                            <th>{{ trans('policies.table.header.number') }}</th>
                            <th>{{ trans('policies.table.header.amount') }}</th>
                            <th>{{ trans('policies.table.header.date') }}</th>
                            <th>{{ trans('policies.table.header.method') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($policy->payments as $key => $payment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $policy->client->currency_symbol }}{{ $payment->amount }}</td>
                            <td>{{ date('F d, Y', strtotime($payment->date)) }}</td>
                            <td>
                                <i class="{{ array(
                                    'card'      => 'credit card alternative',
                                    'cash'      => 'money',
                                    'paypal'    => 'paypal card',
                                    'cheque'    => 'cheque',
                                    'Online Banking'    => 'Online Banking',
                                    'ECS'    => 'ECS'
                                )[$payment->method] }} icon"></i> {{ array(
                                    'card'      => trans('clients.table.data.method.card'),
                                    'cash'      => trans('clients.table.data.method.cash'),
                                    'paypal'    => trans('clients.table.data.method.paypal'),
                                    'cheque'    => 'Cheque',
                                    'Online Banking'    => 'Online Banking',
                                    'ECS'    => 'ECS'
                                )[$payment->method] }}
                            </td>
                            <td>
                                <?php
                                if(isset($user_role) && $user_role=='super'){?>
                                <a href="#" class="green label mini ui editPaymentModalBtn"  data-target="#editPaymentModal" data-toggle="modal" data-id="{{$payment->id}}" data-amount="{{$payment->amount}}" data-date="{{$payment->date}}" data-method="{{$payment->method}}" > Edit </a>
                                <?php } ?>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="center aligned" colspan="4">{{ trans('policies.table.message.empty.payments', array(
                                'name'  => $policy->client->first_name
                            )) }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- end payments -->
        </div>
        <!-- end half page content -->
@endsection

@section('modals')
    <!-- new attachment modal -->
    <div class="ui tiny modal" id="newAttachmentModal">
        <div class="header">{{ trans('attachments.modal.header.new') }}</div>
        <div class="content">
            <form action="{{ route('attachments.add') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="attachee" value="{{ $policy->id }}"/>
                <input type="hidden" name="attachee_type" value="policy"/>
                <div class="ui form">
                    <div class="field required">
                        <label>{{ trans('attachments.input.label.name') }}</label>
                        <input type="text" name="name" placeholder="{{ trans('attachments.input.placeholder.name') }}" required value="{{ old('name') }}"/>
                    </div>
                    <div class="field required">
                        <label>{{ trans('attachments.input.label.attachment') }}</label>
                        <input type="file" accept="image/*, application/pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx" class="file-upload" data-allowed-file-extensions="bmp doc docx gif jpeg jpg pdf png ppt pptx svg xls xlsx" name="attachment" required/>
                    </div>
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui buttons">
                <button class="ui cancel button">{{ trans('attachments.modal.button.cancel.new') }}</button>
                <div class="or" data-text="{{ trans('attachments.modal.button.or') }}"></div>
                <button class="ui positive primary button">{{ trans('attachments.modal.button.confirm.new') }}</button>
            </div>
        </div>
    </div>







<!---printing-->


    <div id="printpolicy" class="ui segment white fs-16" style="display: none;">

             <div class="ui segment white fs-16">
               <!--  <div class="segment-header">
                    <h3>{{ trans('policies.label.details') }}</h3>
                    @yield('policy_actions')
                </div> -->
                <div class="policy-details">
                    <p><b>ACK Details</b></p>
                    <hr style=" border-top: 1px solid #000000;">
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-xs-4">
                            <span>Branch</span>
                            @foreach($branches as $branch)
                            <?php if($branch->id==$policy->branch_id){echo $branch->branch_name;}?>
                            @endforeach
                        </div>
                        <div class="col-sm-6 col-md-3 col-xs-4">
                            <span>ACK Date</span>
                           <?php $ack_date = date("d-m-Y", strtotime($policy->ack_date)); ?>
                            <p>{{ $ack_date }}</p>
                        </div>
                        <div class="col-sm-6 col-md-3 col-xs-4">
                            <span>ACK No</span>
                            <p>{{ $policy->ack_number }}</p>
                        </div>
                        <!-- <div class="col-sm-3 col-md-3 col-xs-3">
                            <span class="ui blue right ribbon huge label">{{$policy->type}}</span>
                        </div> -->
                    </div>
                    <p><b>Policy Details</b></p>
                    <hr style=" border-top: 1px solid #000000;">
                    <div class="row">
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Application No</span>
                            <p>{{ $policy->application_no }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Life Assured</span>
                            <p>{{ $policy->payer }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Nominee</span>
                            <p>{{ $policy->beneficiaries }}</p>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Policy No</span>
                            <p>{{ $policy->policy_no }}</p>
                        </div>

                        <div class="col-sm-6 col-md-8 col-xs-8">
                            <span>Policy Name</span>
                            <p>{{ $policy->product->name }} </p>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Policy Date</span>

                            <?php if($policy->policy_date =='0000-00-00'){
                                $policy_date = '00-00-0000';
                              } else {
                                $policy_date = date("d-m-Y", strtotime($policy->policy_date));
                              }
                            ?>
                            <p>{{ $policy_date }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Policy Amount</span>
                            <p>{{ $policy->premium }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Premium Amount</span>
                            <p>{{ $policy->premium_amount }}</p>
                        </div>



                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Payment Term(Years)</span>
                            <p>{{ $policy->payment_term }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Mode of Payment</span>
                            <p>{{ $policy->type }} </p>
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Policy Term</span>
                            <p>{{ $policy->policy_term }}</p>
                        </div>
                    </div>

                    <p><b>Payment Details</b></p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Premium Cheque Amount</span>
                            <p>{{ $policy->premium_chq_amount }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Premium Cheque Date</span>
                             <?php $premium_chq_date = date("d-m-Y", strtotime($policy->premium_chq_date)); ?>
                            <p>{{ $premium_chq_date }}</p>
                        </div>
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Premium Cheque No</span>
                            <p>{{ $policy->premium_chq_no }} </p>
                        </div>

                    </div>

                    <p><b>Staff Details</b></p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Staff Code</span>
                                    <p></p>
                            @foreach($staffs as $staff)
                           <?php if($staff->id==$policy->staff_id){echo $staff->staff_code.'-'.$staff->first_name.''.$staff->last_name;}?>
                            @endforeach

                        </div>
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Branch Manager</span>
                                    <p></p>
                            @foreach($branch_manger as $bm)
                           <?php if($bm->id==$policy->branch_manager){echo $bm->staff_code.'-'.$bm->first_name.''.$bm->last_name;}?>
                            @endforeach

                        </div>
                        <div class="col-sm-6 col-md-4 col-xs-4">
                            <span>Branch Assist</span>
                                    <p></p>
                            @foreach($branch_assist as $ba)
                           <?php if($ba->id==$policy->branch_assist){echo $ba->staff_code.'-'.$ba->first_name.''.$ba->last_name;}?>
                            @endforeach

                        </div>

                    </div>

                    <p><b>Depositor Details</b></p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">
                        <!-- <div class="col-sm-3 col-md-3">
                            <span>PIN</span>
                            <p>{{ $policy->pin }}</p>
                        </div> -->
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>ECS Mandate</span>
                            <p>{{ $policy->ecs_mandate }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Bank Name</span>
                            <p>{{ $policy->bank_name }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Bank Branch</span>
                            <p>{{ $policy->bank_branch }}</p>
                        </div>


                    </div>
                    <p><b>Life Assured Details</b></p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">

                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Life Assured Name</span>
                            <p>{{ $policy->deposit_name }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>DOB</span>

                            <?php if($policy->ldob =='0000-00-00'){
                                $ldob = '00-00-0000';
                              } else {
                                $ldob = date("d-m-Y", strtotime($policy->ldob));
                              }
                            ?>

                            <p>{{ $ldob }}</p>

                        <!--     <p>{{ $policy->ldob }}</p> -->
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Mobile Number</span>
                            <p>{{ $policy->lmnum }}</p>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-xs-4">
                            <span>Email</span>
                            <p>{{ $policy->lemail }}</p>
                        </div>
                    </div>
                    <p><b>Nominee Details</b></p>
                    <hr style=" border-top: 1px solid #000000;">

                    <div class="row">

                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Nominee Name</span>
                            <p>{{ $policy->beneficiaries }}</p>
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>DOB</span>

                             <?php if($policy->ndob =='0000-00-00'){
                                $ndob = '00-00-0000';
                              } else {
                                $ndob = date("d-m-Y", strtotime($policy->ndob));
                              }
                            ?>
                            <p>{{ $ndob }}</p>
                       <!--      <p>{{ $policy->ndob }}</p> -->
                        </div>
                        <div class="col-sm-3 col-md-4 col-xs-4">
                            <span>Mobile Number</span>
                            <p>{{ $policy->nmnum }}</p>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-xs-3">
                            <span>Email</span>
                            <p>{{ $policy->nemail }}</p>
                        </div>
                    </div>







                </div>
            </div>

     </div>


<!--printing end-->

    @yield('policy_modals')
@endsection

@section('page_scripts')
    <!-- <script src="{{ asset('assets1/libs/datepicker/datepicker.min.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('assets1/libs/datepicker/datepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/dropify/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">

    </script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                if ($(window).width() > 992) {
                    getVisible();
                }else{
                    $('div.right-bar-profile div.scrollbar').removeAttr("style");
                }

                $(window).resize(function(){
                    if ($(window).width() > 992) {
                        getVisible();
                    }else {
                        $('.scrollbar').removeAttr("style");
                    }
                });

                $insura.helpers.initDatepicker('input.datepicker');
                $insura.helpers.initDropify('input.file-upload');
                $insura.helpers.initDropdown('div.dropdown, select.dropdown');
                $insura.helpers.initModal('div#newAttachmentModal', true);
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.initSwal('form button.delete', {
                    confirmButtonText: '{{ trans('attachments.swal.warning.delete.confirm') }}',
                    text: '{{ trans('attachments.swal.warning.delete.text') }}',
                    title: '{{ trans('attachments.swal.warning.delete.title') }}'
                });
                $insura.helpers.listenForChats();
                $insura.helpers.requireDropdownFields('div.required select, div.required div.dropdown input[type="hidden"]');
            });
        })(window.insura, window.jQuery);
    </script>
@endsection
