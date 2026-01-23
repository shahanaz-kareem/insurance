@extends('global.app')

@section('title', trans('policies.title.all') )

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/datepicker/datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('action_buttons')
        <div class="ui right floated segment transparent page-actions">
            <button class="ui icon button positive" data-target="#policyFilter" data-toggle="slide">
                <i class="filter icon"></i>
            </button>
            @yield('new_payment_button')
        </div>
@endsection

@section('content')
        @parent
        @include('global.status')
        <div class="ui segment white" id="policyFilter"{!! $filter ? '' : ' style="display:none;"' !!}>
            <form action="{{ route('policies.all') }}" method="GET">
                <div class="ui form">
                    <div class="five fields">
                        <div class="field">
                            <label>Policy number</label>
                            <input type="text" name="policy_ref" placeholder="{{ trans('policies.input.placeholder.ref_no') }}" value="{{ $filters['policy_ref'] ?? null }}"/>
                        </div>
                        <div class="field ">
                            <label>{{ trans('policies.input.label.branch') }}</label>
                            <select class="ui fluid search dropdown" name="branch">
                                <option></option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="field">
                            <label>{{ trans('policies.input.label.expiry') . ' ' . trans('policies.input.label.from') }}</label>
                            <div class="ui labeled input">
                                <label for="expiryFrom" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" class="datepicker" id="expiryFrom" name="expiry_from" placeholder="{{ trans('policies.input.placeholder.expiry') }}" value="{{ $filters['expiry_from'] ?? null }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>{{ trans('policies.input.label.expiry') . ' ' . trans('policies.input.label.to') }}</label>
                            <div class="ui labeled input">
                                <label for="expiryTo" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" class="datepicker" id="expiryTo" name="expiry_to" placeholder="{{ trans('policies.input.placeholder.expiry') }}" value="{{ $filters['expiry_to'] ?? null }}"/>
                            </div>
                        </div>-->
                        <div class="field">
                            <label>{{ trans('policies.input.label.renewal') . ' ' . trans('policies.input.label.from') }}</label>
                            <div class="ui labeled input">
                                <label for="renewalFrom" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" class="datepicker" id="renewalFrom" name="renewal_from" placeholder="{{ trans('policies.input.placeholder.renewal') }}" value="{{ $filters['renewal_from'] ?? null }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>{{ trans('policies.input.label.renewal') . ' ' . trans('policies.input.label.to') }}</label>
                            <div class="ui labeled input">
                                <label for="renewalTo" class="ui label"><i class="calendar icon"></i></label>
                                <input type="text" class="datepicker" id="renewalTo" name="renewal_to" placeholder="{{ trans('policies.input.placeholder.renewal') }}" value="{{ $filters['renewal_to'] ?? null }}"/>
                            </div>
                        </div>
                        <div class="field ">
                            <label>Status</label>
                            <select class="ui fluid search dropdown" name="status">
                                <option value="Al">All</option>

                                <option value="A">Active</option>
                                <option value="I">InActive</option>

                            </select>
                        </div>
                    </div>
                    <div class="five fields">
                        <div class="field">
                            <label>{{ trans('policies.input.label.product') }}</label>
                            <div class="ui selection dropdown">
                                <input type="hidden" name="product"/>
                                <div class="default text text-ellipsis">{{ trans('policies.input.placeholder.product') }}</div>
                                <i class="dropdown icon"></i>
                                <div class="menu">
                                    @forelse($user->company->products as $product)
                                    <div class="item{{ ($filters['product'] ?? null) == $product->id ? ' selected' : '' }}" data-value="{{ $product->id }}">
                                        <i class="angle right icon"></i>
                                        {{ $product->name }}
                                    </div>
                                    @empty
                                    <div class="item disabled" data-value="">
                                        <i class="angle right icon"></i>
                                        {{ trans('policies.input.option.empty.product')}}
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="field " >
                        <label>Staff</label>
                        <select class="ui fluid search dropdown" name="staff">
                            <option></option>
                            @if(Auth::user()->role == 'super')
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->first_name }} {{ $staff->last_name }}</option>
                                @endforeach
                            @else
                                <option value="{{ Auth::user()->id }}">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="field " >
                        <label>Client</label>
                        <select class="ui fluid search dropdown" name="client">
                            <option></option>

                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }}</option>
                                @endforeach

                        </select>
                    </div>
                     <div class="field">
                            <label>Phone number</label>
                            <input type="text" name="phone" placeholder="Phone number" value="{{ $filters['phone'] ?? null }}"/>
                        </div>
                        <!-- <div class="field">
                            <label>{{ trans('policies.input.label.premium') . ' ' . trans('policies.input.label.max') }}</label>
                            <div class="ui labeled input">
                                <label for="premiumMax" class="ui label">{{ $policies->currency_symbol }}</label>
                                <input type="text" id="premiumMax" name="premium_max" placeholder="{{ trans('policies.input.placeholder.premium') }}" value="{{ $filters['premium_max'] ?? null }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>{{ trans('policies.input.label.premium') . ' ' . trans('policies.input.label.min') }}</label>
                            <div class="ui labeled input">
                                <label for="premiumMin" class="ui label">{{ $policies->currency_symbol }}</label>
                                <input type="text" id="premiumMin" name="premium_min" placeholder="{{ trans('policies.input.placeholder.premium') }}" value="{{ $filters['premium_min'] ?? null }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>{{ trans('policies.input.label.due') . ' ' . trans('policies.input.label.max') }}</label>
                            <div class="ui labeled input">
                                <label for="dueMax" class="ui label">{{ $policies->currency_symbol }}</label>
                                <input type="text" id="dueMax" name="due_max" placeholder="{{ trans('policies.input.placeholder.due') }}" value="{{ $filters['due_max'] ?? null }}"/>
                            </div>
                        </div>
                        <div class="field">
                            <label>{{ trans('policies.input.label.due') . ' ' . trans('policies.input.label.min') }}</label>
                            <div class="ui labeled input">
                                <label for="dueMin" class="ui label">{{ $policies->currency_symbol }}</label>
                                <input type="text" id="dueMin" name="due_min" placeholder="{{ trans('policies.input.placeholder.due') }}" value="{{ $filters['due_min'] ?? null }}"//>
                            </div>
                        </div> -->
                    </div>
                    <div class="text-right">
                        <button class="ui button" type="reset"> {{ trans('policies.button.clear') }} </button>
                        <button class="ui labeled icon button black" name="filter" type="submit"> <i class="search icon"></i> {{ trans('policies.button.filter') }} </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="ui segment white" style="overflow-x:scroll">
            <table class="ui table">
                <thead>
                    <tr>
                        <th>{{ trans('policies.table.header.number') }}</th>
                        <th>Policy No</th>
                        <!-- <th>Ref No</th> -->
                        <th>{{ trans('policies.table.header.client') }}</th>
                        <th>{{ trans('policies.table.header.branch') }}</th>
                        <th>{{ trans('policies.table.header.product') }}</th>
                        <!-- <th>{{ trans('policies.table.header.insurer') }}</th> -->

                        <th>Policy Amount</th>
                        <th>Status</th>
                        <th>Total Due</th>
                        <th>Premium Due </th>
                        <th class="center aligned">{{ trans('policies.table.header.status') }}</th>
                        <th class="center aligned">{{ trans('policies.table.header.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $key => $policy)
                    @if(Auth::user()->role == 'staff')
                    @if(Auth::user()->branch_id == $policy->branch_id)
                    <tr class="{{ $policy->statusClass }}">
                        <td>{{ $policies->lastOnPreviousPage + $key + 1 }}</td>
                        <td>{{ $policy->policy_no }}</td>
                        <!-- <td>{{ $policy->ref_no }}</td> -->
                        <td>{{ $policy->client->first_name }} {{ $policy->client->last_name }}</td>
                        <td>{{ $policy['branch']['branch_name'] }}</td>
                        <td >{{ $policy->product->name }} <br> insurer : {{ $policy->product->insurer }}</td>

                        <td>{{ $policies->currency_symbol }}{{ $policy->premium }}<br> Renewal Date: <br>{{ $policy->renewal}}</td>
                        <td><?php if($policy->policy_no!=''){echo '<b style="color:green">Active</b>';}else{echo '<b style="color:red">Inactive</b>';}?></td>
                        <td>{{ $policies->currency_symbol }}{{ $policy->due }} </td>
                        <td>{{ $policy->premium_amount }}</td>
                        <td class="center aligned">
                            @if ($policy->premium <= $policy->paid && $policy->paid > 0)
                            <div class="ui green mini label"> {{ trans('clients.table.data.status.paid') }}</div>
                            @elseif ($policy->premium > $policy->paid && $policy->paid > 0)
                            <div class="ui orange mini label"> {{ trans('clients.table.data.status.partial') }}</div>
                            @elseif ($policy->premium == $policy->paid && $policy->paid === 0)
                            <div class="ui yellow mini label"> {{ trans('clients.table.data.status.free') }}</div>
                            @else ($policy->premium > 0 && $policy->paid === 0)
                            <div class="ui red mini label"> {{ trans('clients.table.data.status.unpaid') }}</div>
                            @endif
                        </td>
                        <td class="center aligned">
                            <a href="{{ route('policies.one', array($policy->id)) }}" class="ui mini grey label"> {{ trans('policies.table.data.action.view') }} </a>
                        </td>
                    </tr>
                    @endif
                    @else
                    <tr class="{{ $policy->statusClass }}">
                        <td>{{ $policies->lastOnPreviousPage + $key + 1 }}</td>
                        <td>{{ $policy->policy_no }}</td>
                        <!-- <td>{{ $policy->ref_no }}</td> -->
                        <td>{{ $policy->client->first_name }} {{ $policy->client->last_name }}</td>
                        <td>{{ $policy['branch']['branch_name'] }}</td>
                        <td >{{ $policy->product->name }} <br> insurer : {{ $policy->product->insurer }}</td>
                        
                        <?php 
                        $curdate = strtotime(date('Y-m-d'));
                        $mydate  = strtotime($policy->renewal);
                        if($curdate > $mydate){
                            $bg_color= 'red';
                        } else {
                            $bg_color= '';
                        }
                        ?>

                        <td>{{ $policies->currency_symbol }}{{ $policy->premium }}<br> Renewal Date: <br><b style="color:<?php echo $bg_color;?>">{{ $policy->renewal}}</b><br>Policy TYpe: <b>{{ $policy->type}}</b></td>
                        <td><?php if($policy->policy_no!=''){echo '<b style="color:green">Active</b>';}else{echo '<b style="color:red">Inactive</b>';}?></td>
                        <td>{{ $policies->currency_symbol }}{{ $policy->due }}</td>
                        <td>{{ $policy->premium_amount }}</td>
                        <td class="center aligned">
                            @if ($policy->premium <= $policy->paid && $policy->paid > 0)
                            <div class="ui green mini label"> {{ trans('clients.table.data.status.paid') }}</div>
                            @elseif ($policy->premium > $policy->paid && $policy->paid > 0)
                            <div class="ui orange mini label"> {{ trans('clients.table.data.status.partial') }}</div>
                            @elseif ($policy->premium == $policy->paid && $policy->paid === 0)
                            <div class="ui yellow mini label"> {{ trans('clients.table.data.status.free') }}</div>
                            @else ($policy->premium > 0 && $policy->paid === 0)
                            <div class="ui red mini label"> {{ trans('clients.table.data.status.unpaid') }}</div>
                            @endif
                        </td>
                        <td class="center aligned">
                            <a href="{{ route('policies.one', array($policy->id)) }}" class="ui mini grey label"> {{ trans('policies.table.data.action.view') }} </a>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="9" class="center aligned">{{ trans('policies.message.empty') }}</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="center aligned ui" colspan="3">
                            {{ trans('policies.table.data.pagination', array(
                                'start' => $policies->total() > 0 ? $policies->lastOnPreviousPage + 1 : 0,
                                'stop'  => $policies->lastOnPreviousPage + $policies->count(),
                                'total' => $policies->total()
                            )) }}
                        </th>
                        <th class="center aligned ui" colspan="6">
                            {!! $policies->links('vendor.pagination.semantic-ui') !!}
                            
                           
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
@endsection

@section('page_scripts')
    <!-- <script src="{{ asset('assets1/libs/datepicker/datepicker.min.js') }}" type="text/javascript"></script> -->
    <script src="{{ asset('assets1/libs/datepicker/datepicker.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                $insura.helpers.initDatepicker('input.datepicker');
                $insura.helpers.initDropdown('div.dropdown, select.dropdown');
                $insura.helpers.initModal('div.modal', true);
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.listenForChats();
                $insura.helpers.requireDropdownFields('div.required select, div.required div.dropdown input[type="hidden"]');

                $('select[name="owners[]"]').change(function() {
                    $('h5.client').fadeOut(200);
                    $('div.client').fadeOut(200).find('input[name^="premium["]').attr('required', false);
                    $.each($(this).val(), function(i, value) {
                        $('h5.client' + value).fadeIn(200);
                        $('div.client' + value).fadeIn(200).find('input[name^="premium["]').attr('required', true);
                    });
                }).change();
            });
        })(window.insura, window.jQuery);
    </script>
@endsection
