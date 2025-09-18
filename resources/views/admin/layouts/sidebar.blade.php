<a href="{{ URL::to('/admin/profile') }}" class="brand-link">
    <img src="{{ asset('public/dist/img/QFLogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
        style="opacity: .8">
    <span class="brand-text font-weight-light">QuickFluence</span>
</a>
@php($last_segment = last(request()->segments()))
<div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ Auth::user()->profile }}" class="img-circle elevation-2 profile_image" alt="User Image">
        </div>
        <div class="info">
            <a href="{{ URL::to('/admin/profile') }}" class="d-block" id="username">{{ Auth::user()->name }}</a>
        </div>
    </div>
    <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                </button>
            </div>
        </div>
        <div class="sidebar-search-results">
            <div class="list-group"><a href="#" class="list-group-item"></a></div>
        </div>
    </div>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            @foreach ($navItems as $key => $item)
                @if ($key == 'Static Pages')
                    @php($role = request()->segment(count(request()->segments()) - 1))
                    <li
                        class="nav-item {{ in_array($role, ['influencer', 'business']) ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ in_array($role, ['influencer', 'business']) ? 'active' : '' }}">
                            <i class="nav-icon {{ $item['iconClass'] }}"></i>
                            <p>
                                Static Pages
                                <i class="fas fa-angle-left right"></i>
                              </p>
                        </a>
                        @foreach ($item as $subitem => $pages)
                        @if ($subitem != "iconClass")
                            <ul class="nav nav-treeview">
                                <li
                                    class="nav-item {{ $role == $pages['segment'] ? 'menu-is-opening menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ $role == $pages['segment'] ? 'active' : '' }}">
                                      <i class="nav-icon {{$pages['iconClass']}}"></i>
                                        <p>
                                            {{ $subitem }}
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @foreach ($pages as $key => $page)
                                            @if ($key != 'segment' && $key != 'iconClass')
                                                <li class="nav-item">
                                                    <a href="{{ url($page['route']) }}"
                                                        class="nav-link {{ $page['segment'] == $role . '/' . $last_segment ? 'active' : '' }}">
                                                        <i class="nav-icon {{ $page['iconClass'] }}"></i>
                                                        <p>
                                                            {{ $key }}
                                                        </p>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        @endif
                        @endforeach
                    </li>
                @elseif ($key == 'User')
                    <li
                        class="nav-item {{ in_array($last_segment, ['influencer', 'business']) ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ in_array($last_segment, ['influencer', 'business']) ? 'active' : '' }}">
                            <i class="nav-icon {{ $item['iconClass'] }}"></i>
                            <p>
                                Users
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @foreach ($item as $page => $value)
                              <li class="nav-item">
                                @if($page != "iconClass")
                                    <a href="{{ url($value['route']) }}"
                                        class='nav-link {{ $last_segment == $value['segment'] ? 'active' : '' }}'>
                                        <i class="nav-icon {{ $value['iconClass'] }}"></i>
                                        <p>
                                            {{ $page }}
                                        </p>
                                    </a>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @elseif ($key == 'Influencer Networks')
                    <li
                        class="nav-item {{$last_segment}} {{ in_array($last_segment, ['youtube', 'instagram']) ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ in_array($last_segment, ['youtube', 'instagram']) ? 'active' : '' }}">
                            <i class="nav-icon {{ $item['iconClass'] }}"></i>
                            <p>
                              Influencer Networks
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @foreach ($item as $page => $value)
                              <li class="nav-item">
                                @if($page != "iconClass")
                                    <a href="{{ url($value['route']) }}"
                                        class='nav-link {{$last_segment. "--". $value['segment'] }} {{ $last_segment == $value['segment'] ? 'active' : '' }}'>
                                        <i class="nav-icon {{ $value['iconClass'] }}"></i>
                                        <p>
                                            {{ $page }}
                                        </p>
                                    </a>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ url($item['route']) }}"
                            class='nav-link {{ $last_segment == $item['segment'] ? 'active' : '' }}'>
                            <i class="nav-icon {{ $item['iconClass'] }}"></i>
                            <p>
                                {!! $key !!}
                            </p>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</div>
