@php
    $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
@endphp
<a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center">
    <img src="{{ asset('images/ac-chill-header.png') }}" alt="UX Serve" class="img-fluid light-logo" style="height: 50px; max-width: 220px; object-fit: contain;">
    <img src="{{ asset('images/ac-chill-header.png') }}" alt="UX Serve" class="img-fluid darkmode-logo d-none" style="height: 50px; max-width: 220px; object-fit: contain;">
</a>
