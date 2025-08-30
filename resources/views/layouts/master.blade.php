@include('layouts.partials.header')
@include('layouts.partials.sidebar')

<div class="content-page">
    <div class="content">
        @include('layouts.partials.topbar')

        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    @include('layouts.partials.footer')
</div>
