<aside class="aside-menu">
        <div class="branding">
            <div class="sm-show close-aside">
                <i class="ion-ios-close-outline"></i>
            </div>
            <a href="">
                <img src="{{ 'http://iworksync.com/ebs/insurance/storage/app/images/' . config('insura.logo') }}" class="ui image">
            </a>
        </div>
        <div class="scrollbar">
            <div class="ui link list">
                <a class="item" href="{{ action('IndexController@getDashboard') }}">
                    <i class="ion-ios-speedometer-outline icon"></i> <div class="content">{{ trans('sidebar.link.dashboard') }} </div>
                </a>

                @yield('clients')

                <a class="item" href="{{ action('PolicyController@getAll') }}">
                    <i class="ion-ios-bookmarks-outline icon"></i> <div class="content">Client Policies </div>
                </a>

                <!-- @yield('brokers') -->

                <!-- <a class="item insura-chats" href="{{ action('InboxController@getAll') }}">
                    @if ($unread_chats_count > 0)
                    <span class="ui red circular label pull-right">{{ $unread_chats_count }}</span>
                    @endif
                    <i class="ion-ios-chatbubble-outline icon"></i>
                    <div class="content">{{ trans('sidebar.link.inbox') }}</div>
                </a> -->
                <!-- <a class="item" href="{{ action('CommunicationController@get') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">{{ trans('sidebar.link.communication') }} </div>
                </a> -->
                <a class="item" href="{{ action('BranchController@getAll') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">Branches</div>
                </a>
                <a class="item" href="{{ action('MutualfundController@getAll') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">Mutual Funds </div>
                </a>
                <a class="item" href="{{ action('MproductController@getAll') }}">
                    <i class="ion-ios-chatboxes-outline icon"></i> <div class="content">MF Products</div>
                </a>
                @if(Auth::user()->role == 'super')
                <a class="item" href="{{ action('ReportController@get') }}">
                    <i class="ion-ios-pulse-strong icon"></i> <div class="content">{{ trans('sidebar.link.reports') }} </div>
                </a>
                @endif

                <a class="item" href="{{ action('ProductController@getAll') }}">
                    <i class="ion-ios-pulse-strong icon"></i> <div class="content">Policy Products</div>
                </a>

                @yield('companies')

                <!-- @yield('staff') -->

                <a class="item" href="{{ action('SettingController@get') }}">
                    <i class="ion-ios-gear-outline icon"></i> <div class="content">{{ trans('sidebar.link.settings') }} </div>
                </a>
                @if(Auth::user()->role == 'super')
                <a class="item" href="{{ action('StaffController@getAll') }}">
                    <i class="ion-ios-person-outline icon"></i> <div class="content">{{ trans('sidebar.link.staff') }} </div>
                </a>
                @endif

            </div>
        </div>
    </aside>
