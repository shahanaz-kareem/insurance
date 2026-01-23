<aside class="aside-menu">
        <div class="branding">
            <div class="sm-show close-aside">
                <i class="ion-ios-close-outline"></i>
            </div>
            <a href="">
                <img src="{{ 'http://integrityindia.co.in/insurance/storage/app/images/' . config('insura.logo') }}" class="ui image">
            </a>
        </div>
        <div class="scrollbar">
            <div class="ui link list">
                <a class="item" href="{{ route('dashboard') }}">
                    <i class="ion-ios-speedometer-outline icon"></i> <div class="content">{{ trans('sidebar.link.dashboard') }} </div>
                </a>

                @yield('clients')

                <a class="item" href="{{ route('policies.all') }}">
                    <i class="ion-ios-bookmarks-outline icon"></i> <div class="content">Client Policies </div>
                </a>

                <!-- @yield('brokers') -->

                {{-- <a class="item insura-chats" href="{{ route('inbox.all') }}">
                    @if ($unread_chats_count > 0)
                    <span class="ui red circular label pull-right">{{ $unread_chats_count }}</span>
                    @endif
                    <i class="ion-ios-chatbubble-outline icon"></i>
                </a> --}}
                <!-- <a class="item" href="{{ route('communication') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">{{ trans('sidebar.link.communication') }} </div>
                </a> -->
                @if(Auth::user()->role == 'super')
                <a class="item" href="{{ route('branches.all') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">Branches</div>
                </a>
                @endif
                <a class="item" href="{{ route('mutualfunds.all') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">Mutual Funds </div>
                </a>
                <a class="item" href="{{ route('mproducts.all') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">MF Products</div>
                </a>
                @if(Auth::user()->role == 'super')
                <a class="item" href="{{ route('reports.index') }}">
                    <i class="ion-ios-pulse-strong icon"></i> <div class="content">{{ trans('sidebar.link.reports') }} </div>
                </a>
                @endif

                <a class="item" href="{{ route('products.all') }}">
                    <i class="ion-ios-pulse-strong icon"></i> <div class="content">Policy Products</div>
                </a>

                @yield('companies')

                <!-- @yield('staff') -->

                <a class="item" href="{{ route('settings.index') }}">
                    <i class="ion-ios-gear-outline icon"></i> <div class="content">{{ trans('sidebar.link.settings') }} </div>
                </a>
                @if(Auth::user()->role == 'super')
                <a class="item" href="{{ route('staff.all') }}">
                    <i class="ion-ios-person-outline icon"></i> <div class="content">{{ trans('sidebar.link.staff') }} </div>
                </a>
                @endif

            </div>
        </div>
    </aside>
