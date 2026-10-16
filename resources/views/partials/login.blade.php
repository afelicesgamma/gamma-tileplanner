<div class="font-sans min-h-screen flex items-center bg-primary" x-data="{view: location.hash}" @hashchange.window="view = location.hash" style="{!! $background_image ? 'background-image: url(\''. $background_image . '\'); background-size: cover; background-position: center;'  : '' !!}">
    <div class="max-w-screen-lg mx-auto md:grid md:grid-cols-2 md:gap-4 py-10 items-center">
        <h1 class="font-sans text-center text-white font-bold text-2xl lg:text-3xl">Poder ver tus proyectos antes de tenerlos es una realidad</h1>

        <div class="bg-white p-6 lg:p-12">
            @if ( $auth_view === 'forgot' )
                @include('partials.forgot-password')
            @elseif ( $auth_view === 'check_email' )
                <p class="text-xl lg:text-2xl font-bold mb-5 text-[#3D3935]">Revisa tu correo</p>
                <p class="mb-5">Si existe una cuenta con esos datos, recibirás un enlace para restablecer la contraseña.</p>
                <p class="mb-2">
                    <a class="text-primary" href="{{home_url( '/' . trim( $tileplanner_route, '/' ) )}}">Volver a iniciar sesión</a>
                </p>
            @elseif ( $auth_view === 'reset' )
                @if ( $password_reset_valid )
                    @include('partials.reset-password')
                @else
                    <p class="text-xl lg:text-2xl font-bold mb-5 text-[#3D3935]">Enlace no válido</p>
                    <p class="mb-5">El enlace ha caducado o no es válido. Solicita uno nuevo para continuar.</p>
                    <p class="mb-2">
                        <a class="text-primary" href="{{add_query_arg( \GammaTileplanner\Functionality\Routes::AUTH_QUERY_VAR, 'forgot', home_url( '/' . trim( $tileplanner_route, '/' ) ))}}">Solicitar otro enlace</a>
                    </p>
                @endif
            @elseif ( $auth_view === 'reset_complete' )
                <p class="text-xl lg:text-2xl font-bold mb-5 text-[#3D3935]">Contraseña actualizada</p>
                <p class="mb-5">Ya puedes iniciar sesión con tu nueva contraseña.</p>
                <p class="mb-2">
                    <a class="text-primary" href="{{home_url( '/' . trim( $tileplanner_route, '/' ) )}}">Volver a iniciar sesión</a>
                </p>
            @else
                @include('partials.login-form')
                @include('partials.register-form')
            @endif
        </div>
    </div>
</div>
