@extends('layouts.app')

@section('content')

    @if (!$is_user_logged_id)

        @include('partials.login')

    @else

        @include('partials.iframe')

    @endif

@endsection
