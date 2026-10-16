<p class="text-xl lg:text-2xl font-bold mb-5 text-[#3D3935]">Crear nueva contraseña</p>

@if ( $password_reset_error )
    <p class="mb-5 text-red-500">No se pudo cambiar la contraseña. Comprueba que ambas coincidan e inténtalo de nuevo.</p>
@endif

<form action="{{admin_url('admin-post.php')}}" method="post">
    <input type="hidden" name="action" value="gamma_tileplanner_reset_password">
    {!!wp_nonce_field( 'gamma_tileplanner_reset_password_' . $password_reset_login, 'gamma_tileplanner_reset_password_nonce' )!!}
    <input type="hidden" name="key" value="{{$password_reset_key}}">
    <input type="hidden" name="user_login" value="{{$password_reset_login}}">

    <div class="mb-5">
        <label for="new_password">Nueva contraseña</label>
        <input class="block w-full border-b border-primary p-2" type="password" name="password" id="new_password" autocomplete="new-password" required>
    </div>

    <div class="mb-5">
        <label for="new_password_confirmation">Repite la nueva contraseña</label>
        <input class="block w-full border-b border-primary p-2" type="password" name="password_confirmation" id="new_password_confirmation" autocomplete="new-password" required>
    </div>

    <div class="mb-5">
        <button class="bg-primary text-white p-2 px-5 rounded" type="submit">Guardar contraseña</button>
    </div>
</form>
