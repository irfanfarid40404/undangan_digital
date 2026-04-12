@extends('layouts.base')

@section('body')
    @include('partials.navbar-landing')
    <main>
        @yield('content')
    </main>
    @include('partials.footer-landing')
@endsection
