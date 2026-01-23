@extends('templates.sidebar')

@section('clients')
            <a class="item" href="{{ route('clients.all') }}">
                <i class="ion-ios-people-outline icon"></i> <div class="content">{{ trans('sidebar.link.clients') }} </div>
            </a>
            @endsection

@section('brokers')
            <a class="item" href="{{ route('brokers.all') }}">
                <i class="ion-ios-briefcase-outline icon"></i> <div class="content">{{ trans('sidebar.link.brokers') }} </div>
            </a>
@endsection

@section('products')
            <a class="item" href="{{ route('products.all') }}">
                <i class="ion-ios-lightbulb-outline icon"></i> <div class="content">{{ trans('sidebar.link.products') }} </div>
            </a>
@endsection

 @section('staff')
            <a class="item" href="{{ route('staff.all') }}">
                <i class="ion-ios-person-outline icon"></i> <div class="content">{{ trans('sidebar.link.staff') }} </div>
            </a>
@endsection