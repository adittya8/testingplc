<nav class="navbar navbar-expand navbar-light fixed-top">
  <div class="container-fluid px-2 ps-lg-2 pe-lg-5">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto">
        {{-- <li class="nav-item d-md-none">
          <a href="{{ route('home') }}">
            <img src="{{ asset('images/energy-logo.png') }}" alt="" class="navbar-brand">
          </a>
        </li> --}}
        @if (!$noSidebar)
          <li class="nav-item sidebar-toggler">
            <a href="javascript:;" id="sidebarToggler" class="nav-link navbar-sidebar-toggler">
              <i class="fa-solid fa-chevron-right"></i>
            </a>
          </li>
        @endif

        @if (config('project') && auth()->user()->hasRole('Super Admin'))
          <li class="nav-item fw-medium d-none d-md-block">
            <span class="nav-link">In Project: {{ config('project')->name }}</span>
          </li>
        @endif
      </ul>

      <ul class="navbar-nav ms-auto navbar-collapsible collapsible-lg">
        @if (config('project_id'))
          @can('Dim Groups')
            <li class="nav-item d-flex align-items-center me-2">
              <a class="nav-link ps-2" href="javascript:;" onclick="openCustomModal('#groupCommandModal')">
                Group
              </a>
            </li>
          @endcan

          @can('Dim Sub-Groups')
            <li class="nav-item d-flex align-items-center me-2">
              <a class="nav-link ps-2" href="javascript:;" onclick="openCustomModal('#subGroupCommandModal')">
                Sub-Group
              </a>
            </li>
          @endcan

          @can('Dim RTUs')
            <li class="nav-item d-flex align-items-center me-2">
              <a class="nav-link ps-2" href="javascript:;" onclick="openCustomModal('#individualCommandModal')">
                Individual
              </a>
            </li>
          @endcan
        @endif
      </ul>

      <ul class="navbar-nav">
        <li class="d-lg-none nav-item d-flex align-items-center me-1 me-md-2">
          <a class="nav-link ps-2 navbar-toggle" href="javascript:;" data-open-navbar>
            <i class="fas fa-bars"></i>
          </a>
        </li>

        <li class="nav-item dropdown me-1 me-md-2">
          <a class="nav-link ps-2" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false" data-bs-auto-close="outside" data-bs-title="{{ __('texts.change_lang') }}"
            data-bs-placement="bottom">
            <img src="{{ asset('images/lang/' . app()->getLocale() . '.png') }}" class="nav-link-img" alt="">
            <span class="d-none d-md-inline">{{ locale_get_display_language(app()->getLocale()) }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end animate slide-in" aria-labelledby="langDropdown">
            <li>
              <a class="dropdown-item" href="{{ route('set-locale', ['locale' => 'en']) }}">
                <img src="{{ asset('images/lang/en.png') }}" class="nav-link-img" alt=""> English
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('set-locale', ['locale' => 'bn']) }}">
                <img src="{{ asset('images/lang/bn.png') }}" class="nav-link-img" alt=""> Bangla
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item me-1 me-md-2">
          <a class="nav-link ps-2" href="#">
            <img src="{{ asset('images/user.png') }}" class="nav-link-img" alt="">
            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
          </a>
        </li>

        <li class="nav-item d-flex align-items-center">
          <a class="nav-link ps-2" href="#" onclick="document.querySelector('#logOutFrom').submit()"
            data-bs-toggle="tooltip" data-bs-title="{{ __('texts.logout') }}" data-bs-placement="bottom">
            <i class="fas fa-right-from-bracket"></i>
          </a>
          <form action="{{ route('logout') }}" method="POST" class="d-none" id="logOutFrom">
            @csrf
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
