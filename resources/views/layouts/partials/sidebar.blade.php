<div class="leftside-menu">
    <a href="#" class="logo text-center logo-light">
        <h3 class="text-white">Intelboard</h3>
    </a>
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <ul class="side-nav">
            <li class="side-nav-title side-nav-item">APPS</li>
            <li class="side-nav-item">
                <a href="{{ route('drivers') }}" class="side-nav-link">
                    <i class="mdi mdi-car"></i>
                    <span> {{ __('Drivers') }} </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('payments.index') }}" class="side-nav-link">
                    <i class="mdi mdi-receipt"></i>
                    <span> {{ __('Payments') }} </span>
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>

</div>
