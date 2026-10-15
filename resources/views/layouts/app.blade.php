<!doctype html>
<html {!! get_language_attributes() !!}>
@include('partials.head')
<body @php body_class() @endphp>
    @include('partials.header')
    <div class="tileplanner" role="document">
        <main>
            @yield('content')
        </main>
    </div>
    @php wp_footer() @endphp
</body>
</html>
