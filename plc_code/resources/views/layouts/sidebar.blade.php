<aside class="sidebar sidebar-dark">
  <div class="sidebar-header d-flex align-items-center justify-content-between">
    <div>
      <img src="{{ asset('images/energy-logo.png') }}" alt="" class="py-3">
    </div>
  </div>

  <ul class="sidebar-nav">
    @if (auth()->user()->hasRole('Super Admin'))
      <li class="nav-item">
        <a href="{{ route('projects') }}" @class(['nav-link', 'active' => isSbLinkActive(['projects'])])>
          <span><i class="fas fa-retweet"></i> {{ __('texts.switch_project') }}</span>
        </a>
      </li>
    @endif

    @can('View Dashboard')
      <li class="nav-item">
        <a href="{{ route('projects.dashboard', ['project' => config('project_id')]) }}" @class([
            'nav-link',
            'active' => isSbLinkActive(['projects.dashboard']),
        ])>
          <span><i class="fas fa-location-dot"></i> {{ __('texts.dashboard') }}</span>
        </a>
      </li>
    @endcan

    @canany([
      'View Brands',
      'View Luminary Types',
      'View Zones',
      'View Roads',
      'View Groups',
      'View Sub-Groups',
      'View
      DCUs',
      'View RTUs',
      'View Poles',
      'View Luminaries',
      ])
      @canany(['View Brands', 'View Luminary Types', 'View Zones', 'View Roads', 'View Groups', 'View Sub-Groups'])
        <li class="nav-item">
          <a href="#" @class([
              'nav-link',
              'active' => isSbLinkActive(['luminaries-config', 'zone-management']),
          ])>
            <span><i class="fas fa-money-check"></i> {{ __('texts.asset_management') }}</span>
          </a>
        </li>
        <li class="nav-item has-submenu">
          <a href="javascript:void(0)" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive([
                  'luminaries-config',
                  'zone-management',
                  'grouping',
              ]),
          ])>
            <span>{{ __('texts.configuration') }}</span>
            <i class="fa-solid fa-angle-down right"></i>
          </a>

          <ul class="sidebar-submenu">
            @canany(['View Brands', 'View Luminary Types'])
              <li class="nav-item">
                <a href="{{ route('luminaries-config', ['project' => config('project_id')]) }}" @class([
                    'nav-link no-icon',
                    'active' => isSbLinkActive(['luminaries-config']),
                ])>
                  <span>{{ __('texts.luminaries_config') }}</span>
                </a>
              </li>
            @endcanany

            @canany(['View Zones', 'View Roads'])
              <li class="nav-item">
                <a href="{{ route('zone-management', ['project' => config('project_id')]) }}" @class([
                    'nav-link no-icon',
                    'active' => isSbLinkActive(['zone-management']),
                ])>
                  <span>{{ __('texts.zone_management') }}</span>
                </a>
              </li>
            @endcanany

            @canany(['View Groups', 'View Sub-Groups'])
              <li class="nav-item">
                <a href="{{ route('grouping', ['project' => config('project_id')]) }}" @class(['nav-link no-icon', 'active' => isSbLinkActive(['grouping'])])>
                  <span>{{ __('texts.grouping') }}</span>
                </a>
              </li>
            @endcanany
          </ul>
        </li>
      @endcanany

      @canany(['View DCUs', 'View RTUs', 'View Poles', 'View Luminaries'])
        <li class="nav-item has-submenu">
          <a href="javascript:void(0)" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive([
                  'concentrators',
                  'rtus',
                  'poles',
                  'luminaries',
              ]),
          ])>
            <span>{{ __('texts.lighting_management') }}</span>
            <i class="fa-solid fa-angle-down right"></i>
          </a>

          <ul class="sidebar-submenu">
            @can('View DCUs')
              <li class="nav-item">
                <a href="{{ route('concentrators', ['project' => config('project_id')]) }}" @class([
                    'nav-link no-icon',
                    'active' => isSbLinkActive(['concentrators']),
                ])>
                  <span>{{ __('texts.dcu') }}</span>
                </a>
              </li>
            @endcan

            @can('View Poles')
              <li class="nav-item">
                <a href="{{ route('poles', ['project' => config('project_id')]) }}" @class(['nav-link no-icon', 'active' => isSbLinkActive(['poles'])])>
                  <span>{{ __('texts.pole') }}</span>
                </a>
              </li>
            @endcan

            @can('View RTUs')
              <li class="nav-item">
                <a href="{{ route('rtus', ['project' => config('project_id')]) }}" @class(['nav-link no-icon', 'active' => isSbLinkActive(['rtus'])])>
                  <span>{{ __('texts.rtu') }}</span>
                </a>
              </li>
            @endcan

            {{-- @can('View Luminaries')
              <li class="nav-item">
                <a href="{{ route('luminaries', ['project' => config('project_id')]) }}" @class([
                    'nav-link no-icon',
                    'active' => isSbLinkActive(['luminaries']),
                ])>
                  <span>{{ __('texts.luminaries') }}</span>
                </a>
              </li>
            @endcan --}}
          </ul>
        </li>
      @endcanany
    @endcanany

    @canany(['View Schedule Presets', 'View Monitor Log', 'View Lamp Data'])
      <li class="nav-item">
        <a href="#" class="nav-link">
          <span><i class="fas fa-display"></i> {{ __('texts.monitoring') }}</span>
        </a>
      </li>

      @can('View Schedule Presets')
        <li class="nav-item has-submenu">
          <a href="javascript:void(0)" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive([
                  'dimming-task',
                  'dimming-schedule',
                  'schedule-presets',
                  'dimming-task.add-rtus',
              ]),
          ])>
            <span>{{ __('texts.schedule_management') }}</span>
            <i class="fa-solid fa-angle-down right"></i>
          </a>

          <ul class="sidebar-submenu">
            <li class="nav-item">
              <a href="{{ route('dimming-task', ['project' => config('project_id')]) }}" @class([
                  'nav-link no-icon',
                  'active' => isSbLinkActive(['dimming-task', 'dimming-task.add-rtus']),
              ])>
                <span>{{ __('texts.dimming_task') }}</span>
              </a>
            </li>
            {{-- <li class="nav-item">
              <a href="{{ route('dimming-schedule', ['project' => config('project_id')]) }}" @class([
                  'nav-link no-icon',
                  'active' => isSbLinkActive(['dimming-schedule']),
              ])>
                <span>{{ __('texts.dimming_schedule') }}</span>
              </a>
            </li> --}}
            <li class="nav-item">
              <a href="{{ route('schedule-presets', ['project' => config('project_id')]) }}" @class([
                  'nav-link no-icon',
                  'active' => isSbLinkActive(['schedule-presets']),
              ])>
                <span>{{ __('texts.schedule_presets') }}</span>
              </a>
            </li>
          </ul>
        </li>
      @endcan

      @canany(['View Monitor Log', 'View Lamp Data'])
        <li class="nav-item has-submenu">
          <a href="javascript:void(0)" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive(['monitor-log', 'lamp-data']),
          ])>
            <span>{{ __('texts.real_time_monitor') }}</span>
            <i class="fa-solid fa-angle-down right"></i>
          </a>

          <ul class="sidebar-submenu">
            @can('View Monitor Log')
              <li class="nav-item">
                <a href="{{ route('monitor-log', ['project' => config('project_id')]) }}" @class([
                    'nav-link no-icon',
                    'active' => isSbLinkActive(['monitor-log']),
                ])>
                  <span>{{ __('texts.monitor_log') }}</span>
                </a>
              </li>
            @endcan

            @can('View Lamp Data')
              <li class="nav-item">
                <a href="{{ route('lamp-data', ['project' => config('project_id')]) }}" @class([
                    'nav-link no-icon',
                    'active' => isSbLinkActive(['lamp-data']),
                ])>
                  <span>{{ __('texts.lamp_data') }}</span>
                </a>
              </li>
            @endcan
          </ul>
        </li>
      @endcanany
    @endcanany

    @canany([
      'View Alerts',
      'View Luminary Points',
      'View Power Consumption',
      'View Equipment Alarms',
      'View SMS
      Alerts',
      'View Luminaries Lifespan',
      ])
      <li class="nav-item">
        <a href="#" class="nav-link">
          <span><i class="fas fa-chart-column"></i> {{ __('texts.reports') }}</span>
        </a>
      </li>
      @can('View Alerts')
        <li class="nav-item">
          <a href="{{ route('alerts', ['project' => config('project_id')]) }}" @class(['nav-link no-icon', 'active' => isSbLinkActive(['alerts'])])>
            <span>{{ __('texts.alerts') }}</span>
          </a>
        </li>
      @endcan
      {{-- @can('View Luminary Points')
        <li class="nav-item">
          <a href="{{ route('luminaries-point', ['project' => config('project_id')]) }}" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive(['luminaries-point']),
          ])>
            <span>{{ __('texts.luminaries_points') }}</span>
          </a>
        </li>
      @endcan --}}
      @can('View Power Consumption')
        <li class="nav-item">
          <a href="{{ route('power-consumption', ['project' => config('project_id')]) }}" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive(['power-consumption']),
          ])>
            <span>{{ __('texts.power_consumption') }}</span>
          </a>
        </li>
      @endcan
      @can('View Equipment Alarms')
        <li class="nav-item">
          <a href="{{ route('equipment-alarms', ['project' => config('project_id')]) }}" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive(['equipment-alarms']),
          ])>
            <span>Equipment Alarms</span>
          </a>
        </li>
      @endcan
      @can('View SMS Alerts')
        <li class="nav-item">
          <a href="{{ route('sms-alerts', ['project' => config('project_id')]) }}" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive(['sms-alerts']),
          ])>
            <span>SMS Alerts</span>
          </a>
        </li>
      @endcan
      @can('View Luminaries Lifespan')
        <li class="nav-item">
          <a href="{{ route('luminaries-lifespan', ['project' => config('project_id')]) }}" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive(['luminaries-lifespan']),
          ])>
            <span>Luminaries Lifespan</span>
          </a>
        </li>
      @endcan
    @endcanany

    @canany(['View Users', 'View Roles'])
      <li class="nav-item">
        <a href="#" class="nav-link">
          <span><i class="fas fa-users"></i> {{ __('texts.user_management') }}</span>
        </a>
      </li>
      @can('View Users')
        <li class="nav-item">
          <a href="{{ route('users', ['project' => config('project_id')]) }}" @class(['nav-link no-icon', 'active' => isSbLinkActive(['users'])])>
            <span>{{ __('texts.users') }}</span>
          </a>
        </li>
      @endcan
      @can('View Roles')
        <li class="nav-item">
          <a href="{{ route('roles', ['project' => config('project_id')]) }}" @class([
              'nav-link no-icon',
              'active' => isSbLinkActive(['roles', 'roles.permissions']),
          ])>
            <span>{{ __('texts.roles') }}</span>
          </a>
        </li>
      @endcan
      @can('View Logs')
        <li class="nav-item">
          <a href="{{ route('logs.index', ['project' => config('project_id')]) }}" @class(['nav-link no-icon', 'active' => isSbLinkActive(['logs'])])>
            <span>Logs</span>
          </a>
        </li>
      @endcan
    @endcanany
  </ul>

  {{-- <div class="sidebar-footer px-1 pt-3 pb-2">
    <p class="d-flex align-items-center mb-1 justify-content-center">
      <span class="me-1">Developed By</span>
      <img src="{{ asset('images/energy-logo.png') }}" style="width: 72px" alt="">
    </p>
    <p class="d-flex align-items-center mb-1 justify-content-center">
      <span class="me-1">In Collaboration With</span>
      <a href="https://bondstein.com" class="d-flex" target="_blank">
        <img src="{{ asset('images/bond-logo.png') }}" style="width: 72px" alt="">
      </a>
    </p>
  </div> --}}
</aside>
