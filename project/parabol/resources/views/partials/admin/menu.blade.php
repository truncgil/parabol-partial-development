@stack('menu_start')
    <nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-default" id="sidenav-main">
        <div class="scrollbar-inner">
            <div class="sidenav-header d-flex align-items-center">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="avatar menu-avatar background-unset">
                                <img class="border-radius-none border-0 mr-3" alt="Truncgil Parabol" src="{{ asset('public/img/icon-dark.svg') }}">
                            </span>
                            <span class="nav-link-text long-texts pl-2 mwpx-100">{{ Str::limit(setting('company.name'), 22) }}</span>
                            @can('read-common-companies')
                                <i class="fas fa-sort-down pl-2"></i>
                            @endcan
                        </a>
                        @can('read-common-companies')
                            <div class="dropdown-menu dropdown-menu-right menu-dropdown menu-dropdown-width">
                                @foreach($companies as $com)
                                    <a href="{{ route('companies.switch', $com->id) }}" class="dropdown-item">
                                        <i class="fas fa-building"></i>
                                        <span>{{ Str::limit($com->name, 18) }}</span>
                                    </a>
                                @endforeach
                                @can('update-common-companies')
                                    <div class="dropdown-divider"></div>
                                    <a href="{{ route('companies.index') }}" class="dropdown-item">
                                        <i class="fas fa-cogs"></i>
                                        <span>{{ trans('general.title.manage', ['type' => trans_choice('general.companies', 2)]) }}</span>
                                    </a>
                                @endcan
                            </div>
                        @endcan
                    </li>
                    
                </ul>
                <div class="ml-auto left-menu-toggle-position overflow-hidden">
                    <div class="sidenav-toggler d-none d-xl-block left-menu-toggle" data-action="sidenav-unpin" data-target="#sidenav-main">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </div>
                </div>
                
            </div>
            {!! menu('admin') !!} 
            <div class="sidenav-header  align-items-center">
                <ul class="navbar-nav">
                    
                    <li class="nav-item"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/e-fatura">
                            <img style="margin-right: 15px;
                            background: white;
                            padding: 3px;
                            border-radius: 33px;" src="https://seeklogo.com/images/G/gelir-idaresi-baskanligi-logo-CE1857D0C5-seeklogo.com.png" width="32" alt="">
                            <span
                                class="nav-link-text">E-Arşiv <div class="badge badge-warning">Yeni</div></span> </a></li>
                    <li class="nav-item"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/stok"><i class="fa fa-box"></i> <span
                                class="nav-link-text">Stok Durumu <div class="badge badge-warning">Yeni</div></span> </a></li>
                    <li class="nav-item"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/plan-satin-al"><i class="fa fa-calendar-day"></i> <span
                                class="nav-link-text">Planınız  </a></li>
                    <li class="nav-item"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/sms-paketi-al"><i class="fa fa-envelope"></i> <span
                                class="nav-link-text">SMS Durumu </span> </a></li>
                    <li class="nav-item"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/odeme-yapin"><i class="fa fa-credit-card"></i> <span
                                class="nav-link-text">Destek Olun</span> </a></li>
                    <li class="nav-item"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/destek-merkezi"><i class="fa fa-life-ring"></i> <span
                                class="nav-link-text">Destek Merkezi</span> </a></li>
                    <li class="nav-item show-mobile"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/parabol-masaustu"><i class="fa fa-desktop"></i> <span
                                class="nav-link-text">Parabol Masaüstü</span> </a></li>
                    <li class="nav-item"> <a class="nav-link waves-effect"
                            href="{{company_id()}}/ext/duyurular"><i class="fa fa-bullhorn"></i> <span
                                class="nav-link-text">Duyurular ve Geliştirmeler</span> </a></li>
                                <?php if(company_id()==1)  { 
                                  ?>
                     <li class="nav-item"> <a class="nav-link waves-effect"
                             href="{{company_id()}}/ext/gelen-odemeler"><i class="fa fa-credit-card"></i> <span
                                 class="nav-link-text">Gelen Ödemeler</span> </a></li> 
                     <li class="nav-item"> <a class="nav-link waves-effect"
                             href="{{company_id()}}/ext/mail-sistemi"><i class="fa fa-envelope"></i> <span
                                 class="nav-link-text">Toplu Mail</span> </a></li> 
                                 <?php } ?>
                </ul>
            </div>
         
        </div>
    </nav>
@stack('menu_end')
