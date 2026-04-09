@include('layouts.header')
<div class="switch-theme-mode">
    <label id="switch" class="switch">
        <input type="checkbox" onchange="toggleTheme()" id="slider">
        <span class="slider round"></span>
    </label>
</div>
<div class="content-wrapper">

    <div class="breadcrumb-wrap bg-spring">
        <img src="{{ asset('asset/img/breadcrumb/br-shape-1.png') }}" alt="Image" class="br-shape-one xs-none">
        <img src="{{ asset('asset/img/breadcrumb/br-shape-2.png') }}" alt="Image" class="br-shape-two xs-none">
        <img src="{{ asset('asset/img/breadcrumb/br-shape-3.png') }}" alt="Image"
            class="br-shape-three moveHorizontal sm-none">
        <img src="{{ asset('asset/img/breadcrumb/br-shape-4.png') }}" alt="Image"
            class="br-shape-four moveVertical sm-none">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-8 col-sm-8">
                    <div class="breadcrumb-title">
                        <h2>Access Denied</h2>
                        <ul class="breadcrumb-menu list-style">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li>403 Error</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-5 col-md-4 col-sm-4 xs-none">
                    <div class="breadcrumb-img">
                        <img src="{{ asset('asset/img/breadcrumb/br-shape-5.png') }}" alt="Image"
                            class="br-shape-five animationFramesTwo">
                        <img src="{{ asset('asset/img/breadcrumb/br-shape-6.png') }}" alt="Image"
                            class="br-shape-six bounce">
                        <img src="{{ asset('asset/img/breadcrumb/breadcrumb-1.png') }}" alt="Image">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ptb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 style="font-size: 120px; font-weight: 700; color: #4a57ca; line-height: 1;">403</h1>
                    <h3 class="mb-3">Access Forbidden</h3>
                    <p class="mb-4">{{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
                    </p>
                    <a href="{{ url('/') }}" class="btn style1">Back to Home</a>
                </div>
            </div>
        </div>
    </section>

</div>

@include('layouts.footer')