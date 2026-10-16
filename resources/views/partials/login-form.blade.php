<div x-show="view != '#register'">
    <p class="text-xl lg:text-2xl font-bold mb-5 text-[#3D3935]">Entrar</p>

    @if ( isset($_GET['login_error']) )
        <p class="mb-5 text-red-500">Usuario o contraseña incorrectos. Inténtalo de nuevo.</p>
    @endif

    <form action="{{admin_url('admin-post.php')}}" method="post">
        <input type="hidden" name="action" value="gamma_tileplanner_login">
        {!!wp_nonce_field( 'gamma_tileplanner_login', 'gamma_tileplanner_login_nonce' )!!}
        <input type="hidden" name="redirect_to" value="{{home_url( '/' . trim( $tileplanner_route, '/' ) )}}">

        <div class="mb-5">
            <label for="user_login">Usuario o email</label>
            <input class="block w-full border-b border-primary p-2" type="text" name="user_login" id="user_login" autocomplete="username" required>
        </div>

        <div class="mb-5">
            <label for="login_password">Contraseña</label>
            <input class="block w-full border-b border-primary p-2" type="password" name="user_password" id="login_password" autocomplete="current-password" required>
        </div>

        <div class="mb-5">
            <label>
                <input type="checkbox" name="rememberme" value="forever" checked>
                Recordarme
            </label>
        </div>

        <div class="mb-5">
            <button class="bg-primary text-white p-2 px-5 rounded" type="submit">Entrar</button>
        </div>
    </form>

    <p class="mb-2">
        <a class="text-primary" href="{{add_query_arg( \GammaTileplanner\Functionality\Routes::AUTH_QUERY_VAR, 'forgot', home_url( '/' . trim( $tileplanner_route, '/' ) ))}}">¿Has olvidado la contraseña?</a>
    </p>

    <p class="mb-2">
        <a class="text-primary" href="#register">Registrar nuevo usuario</a>
    </p>
</div>
