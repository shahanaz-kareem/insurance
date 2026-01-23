@extends('templates.sidebar')

@section('clients')
            <a class="item" href="{{ route('clients.all') }}">
                <i class="ion-ios-people-outline icon"></i> <div class="content">{{ trans('sidebar.link.clients') }} </div>
            </a>
@endsection

@section('products')
            <a class="item" href="{{ route('products.all') }}">
                <i class="ion-ios-lightbulb-outline icon"></i> <div class="content">{{ trans('sidebar.link.products') }} </div>
            </a>
@endsection
