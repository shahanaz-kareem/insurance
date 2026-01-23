@extends('global.app')

@section('title', trans('dashboard.title'))

@section('page_stylesheets')
    <link href="{{ asset('assets1/libs/morris/morris.css') }}" rel="stylesheet">
@endsection

@section('page_scripts')
    <script src="{{ asset('assets1/libs/morris/raphael.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets1/libs/morris/morris.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        (function($insura, $) {
            $(document).ready(function() {
                $insura.helpers.initDropdown('div.dropdown');
                $insura.helpers.initScrollbar('div.scrollbar');
                $insura.helpers.listenForChats();
            });
        })(window.insura, window.jQuery);
    </script>
@endsection