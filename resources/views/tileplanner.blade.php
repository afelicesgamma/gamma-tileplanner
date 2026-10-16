@extends('layouts.app')

@section('content')

    @if (!$is_user_logged_id || in_array($auth_view, ['forgot', 'reset', 'reset_complete', 'check_email'], true))

        @include('partials.login')

    @else

        @include('partials.iframe')

    @endif

@endsection
