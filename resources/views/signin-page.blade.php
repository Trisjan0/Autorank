@extends('layouts.authentication-page-layout')

@section('title', 'Sign In | Autorank')

@section('content')
<div class="content-container">
    <a href="{{ route('auth.google.redirect') }}" class="signin-container">
        <div class="image-container">
            <img src="https://www.gstatic.com/marketing-cms/assets/images/d5/dc/cfe9ce8b4425b410b49b7f2dd3f3/g.webp=s96-fcrop64=1,00000000ffffffff-rw" alt="google logo">
        </div>
        <h1>Sign In with Google</h1>
    </a>
</div>
@endsection