<p class="text-xl lg:text-2xl font-bold mb-5 text-[#3D3935]">Recuperar contraseña</p>

@if ( $password_reset_error )
    <p class="mb-5 text-red-500">La solicitud ha caducado. Vuelve a intentarlo.</p>
@endif

<p class="mb-5">Introduce tu usuario o correo electrónico. Si la cuenta existe, recibirás un enlace para restablecer la contraseña.</p>

<form action="{{admin_url('admin-post.php')}}" method="post">
    <input type="hidden" name="action" value="gamma_tileplanner_request_password_reset">
    {!!wp_nonce_field( 'gamma_tileplanner_request_password_reset', 'gamma_tileplanner_request_password_reset_nonce' )!!}

    <div class="mb-5">
        <label for="reset_user_login">Usuario o email</label>
        <input class="border-b border-primary px-2 py-1 w-full" type="text" name="user_login" id="reset_user_login" autocomplete="username" required>
    </div>

    <div class="mb-5">
        <button class="bg-primary text-white p-2 px-5 rounded" type="submit">Enviar enlace</button>
    </div>
</form>

<p class="mb-2">
    <a class="text-primary" href="{{home_url( '/' . trim( $tileplanner_route, '/' ) )}}">Volver a iniciar sesión</a>
</p>
