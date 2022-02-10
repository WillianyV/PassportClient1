@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="row">
                <img src="{{ asset('img/login.svg') }}">
            </div>
            <div class="d-grid gap-2 col-12 mx-auto">
                <a hidden href="{{ route("sso.redirect") }}" class="btn btn-danger btn-sm" id="id_login">Login</a>
            </div>            
        </div>
    </div>
</div>
@endsection
