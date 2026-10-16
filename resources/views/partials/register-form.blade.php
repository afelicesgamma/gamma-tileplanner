<div x-show="view == '#register'">
    <p class="text-xl lg:text-2xl font-bold mb-5 text-[#3D3935]">Nuevo usuario</p>

    @if ( isset($_GET['e']) && !empty($_GET['e']) )
        <p class="mb-5 text-red-500">{{$_GET['e']}}</p>
    @endif

    <form action="{{admin_url('admin-post.php')}}" method="post">
        <input type="hidden" name="action" value="gamma_tileplanner_new_user">
        {!!wp_nonce_field( 'gamma_tileplanner_new_user', 'gamma_tileplanner_new_user_nonce' )!!}

        <div class="mb-5">
            <label for="user_name">Nombre y apellidos</label>
            <input class="block w-full border-b border-primary p-2" type="text" name="user_name" id="user_name" value="" required>
        </div>

        <div class="mb-5">
            <label for="user_email">Email</label>
            <input class="block w-full border-b border-primary p-2" type="email" name="user_email" id="user_email" value="" required>
        </div>

        <div class="mb-5">
            <label for="cp">Código postal</label>
            <input class="block w-full border-b border-primary p-2" type="text" name="cp" id="cp" value="" required>
        </div>

        <div x-data="{customerType: false}">
            <div class="mb-5">
                <label>Tipo de cliente</label>
                <div class="flex">
                    <input class="mr-2" type="radio" name="customer_type" id="customer_type_particular" value="particular" x-model="customerType" required>
                    <label for="customer_type_particular">Particular</label>
                </div>
                <div class="flex">
                    <input class="mr-2" type="radio" name="customer_type" id="customer_type_profesional" value="profesional" x-model="customerType" required>
                    <label for="customer_type_profesional">Profesional</label>
                </div>
            </div>

            <div class="mb-5" x-show="customerType == 'profesional'">
                <label for="profesional_type">Tipo de profesional</label>
                <select class="block w-full bg-white border-b border-primary p-2" name="profesional_type" id="profesional_type" :required="customerType == 'profesional'">
                    <option value="">Seleccionar tipo de profesional</option>
                    <option value="decorador">Decorador</option>
                    <option value="interiorista">Interiorista</option>
                    <option value="reformista">Reformista</option>
                    <option value="cosntructor">Cosntructor</option>
                </select>
            </div>
        </div>

        <div class="mb-5">
            <label for="register_password">Contraseña</label>
            <input class="block w-full border-b border-primary p-2" type="password" name="user_password" id="register_password" value="" required>
        </div>

        <div class="mb-5">
            <button class="bg-primary text-white p-2 px-5 rounded" type="submit">Registrar</button>
        </div>
    </form>

    <p class="mb-2">
        <a class="text-primary" href="#login">Ya tengo usuario</a>
    </p>
</div>
