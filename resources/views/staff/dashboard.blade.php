@extends('templates.dashboard')

@section('extra_stylesheets')
    <style>
        h1.font-light.text-white {
            margin-left:10px;
            margin-right:10px;
        }
    </style>
@endsection

@section('content')
        @parent
        @include('global.status')
        <div class="row m-t-15 widgets">
            <!-- Column -->
            <div class="row">
                <div class="col-md-4 ">
                    <div class="card card-inverse bg-primary">
                        <div class="box bg-info text-center">
                            <h1 class="font-light text-white">
                                {{ $user->where('branch_id','=',Auth::user()->branch_id)->where('role','=','client')->count() }}
                            </h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.clients') }}</h6>
                        </div>
                    </div>
                </div>
                <!-- Column -->
                <div class="col-md-4 ">
                    <div class="card card-primary bg-purple">
                        <div class="box text-center">
                            <h1 class="font-light text-white">
                                {{ $latest_policies->count() }}
                            </h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.policies') }}</h6>
                        </div>
                    </div>
                </div>
                <!-- Column -->
                <div class="col-md-4 ">
                    <div class="card card-inverse bg-danger">
                        <div class="box text-center">
                            <h1 class="font-light text-white">
                                {{ $company->currency_symbol }}{{ $all_policies->sum('premium') + 0 }}
                            </h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.sales') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Column -->
                <div class="col-md-4 ">
                    <div class="card card-inverse bg-primary">
                        <div class="box text-center">
                            <h1 class="font-light text-white">
                                {{ $company->currency_symbol }}{{ $policy_payment + 0 }}
                            </h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.conversions') }}</h6>
                        </div>
                    </div>
                </div>
                <!-- Column -->
                <div class="col-md-4 ">
                    <div class="card card-inverse bg-success">
                        <div class="box text-center">
                            <h1 class="font-light text-white">
                                {{ $company->currency_symbol }}{{ $policy_payment + 0 }}
                            </h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.income') }}</h6>
                        </div>
                    </div>
                </div>
                <!-- Column -->
                <div class="col-md-4 ">
                    <div class="card card-inverse bg-warning">
                        <div class="box text-center">
                            <h1 class="font-light text-white">
                                {{ $policy_expiry }}
                                </h1>
                            <h6 class="text-white">{{ trans('dashboard.counter.expiring') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 m-b-15">
                <div class="ui segment white">
                    <div class="segment-header">
                        <h3>{{ trans('dashboard.graph.header.monthly') }}</h3>
                    </div>
                    <div id="monthlySales"></div>
                </div>
            </div>
            <div class="col-md-8 m-b-15">
                <div class="ui segment white">
                    <div class="segment-header">
                        <h3>{{ trans('dashboard.graph.header.annual') }}</h3>
                    </div>
                    <div id="annualSales"></div>
                </div>
            </div>
        </div>

        <div class="ui segment white">
            <div class="segment-header">
                <h3>{{ trans('dashboard.table.title') }}</h3>
            </div>
            <table class="ui striped table">
                <thead>
                    <tr>
                        <th>{{ trans('dashboard.table.header.number') }}</th>
                        <!-- <th>{{ trans('dashboard.table.header.ref_no') }}</th> -->
                        <th>{{ trans('dashboard.table.header.client') }}</th>
                        <th>{{ trans('dashboard.table.header.branch') }}</th>
                        <th>{{ trans('dashboard.table.header.product') }}</th>
                        <th>{{ trans('dashboard.table.header.premium') }}</th>
                        <!-- <th>{{ trans('dashboard.table.header.commission') }}</th> -->
                        <th class="center aligned">{{ trans('dashboard.table.header.status') }}</th>
                        <th class="center aligned">{{ trans('dashboard.table.header.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latest_policies as $key => $policy)
                    <tr class="{{ $policy->statusClass }}">
                        <td>{{ $key + 1 }}</td>
                        <!-- <td>{{ $policy->ref_no }}</td> -->
                        <td>{{ $policy->payer }}</td>
                        <td>{{ $policy['branch']['branch_name'] }}</td>
                        <td class="text-ellipsis">{{ $policy->product->name }}</td>
                        <td>{{ $company->currency_symbol }}{{ $policy->premium + 0 }}</td>
                        <!-- <td>{{ $company->currency_symbol }}{{ $policy->premium * $user->commission_rate / 100 }}</td> -->
                        <td class="center aligned">
                            @if ($policy->premium <= $policy->paid && $policy->paid > 0)
                            <div class="ui green mini label"> {{ trans('dashboard.table.data.status.paid') }}</div>
                            @elseif ($policy->premium > $policy->paid && $policy->paid > 0)
                            <div class="ui orange mini label"> {{ trans('dashboard.table.data.status.partial') }}</div>
                            @elseif ($policy->premium == $policy->paid && $policy->paid === 0)
                            <div class="ui yellow mini label"> {{ trans('dashboard.table.data.status.free') }}</div>
                            @else ($policy->premium > 0 && $policy->paid === 0)
                            <div class="ui red mini label"> {{ trans('dashboard.table.data.status.unpaid') }}</div>
                            @endif
                        </td>
                        <td class="center aligned"><a href="{{ route('policies.one', array($policy->id)) }}" class="ui mini grey label"> {{ trans('dashboard.table.data.action') }} </a></td>
                    </tr>
                    @empty

                    <tr>
                        <td colspan="8" style="text-align:center;">{{ trans('dashboard.table.data.not_available') }}</td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
@endsection

@section('extra_scripts')
    <script type="text/javascript">
        // this Month
        Morris.Donut({
            element: 'monthlySales',
            data: [
                {label: '{{ trans('dashboard.graph.label.monthly.paid') }}', value: {{ $total_payments_amount_this_month }}},
                {label: 'commission', value: {{ $total_commission_rate_this_month }}}
            ],
            colors: ['#21ba45','#ff0000'],
            formatter: function (x) { return '{{ $company->currency_symbol }}' + x }
        }).on('click', function(i, row){
            console.log(i, row);
        });

        // income
        Morris.Bar({
            element: 'annualSales',
            data: [
                {x: '{{ trans('dashboard.graph.label.annual.jan') }}', y: {{ $jan + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.feb') }}', y: {{ $feb + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.mar') }}', y: {{ $mar + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.apr') }}', y: {{ $apr + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.may') }}', y: {{ $may + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.jun') }}', y: {{ $jun + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.jul') }}', y: {{ $jul + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.aug') }}', y: {{ $aug + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.sep') }}', y: {{ $sep + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.oct') }}', y: {{ $oct + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.nov') }}', y: {{ $nov + 0 }}},
                {x: '{{ trans('dashboard.graph.label.annual.dec') }}', y: {{ $dec + 0 }}},
            ],
            xkey: 'x',
            ykeys: 'y',
            labels: ['{{ trans('dashboard.graph.pop_over.annual') . ' ' . $company->currency_symbol }}'],
            barColors: ['#4D7CFE'],
            resize: true
        });
    </script>
@endsection
