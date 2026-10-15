<header class="bg-white">
    <div class="max-w-[1400px] py-[20px] mx-auto">
        <div class="flex items-center justify-between">
            <img class="h-[1.8rem] w-auto" src="{{$logo}}">

            @if ($is_user_logged_id)
                <div class="">
                    <a class="ml-2 text-sm !text-black" href="{{home_url( $tileplanner_route )}}">Inicio</a>
                    <a class="ml-2 text-sm !text-black" href="{{home_url( $tileplanner_route . '?proyectos' )}}">Proyectos abiertos</a>
                </div>
            @endif

        </div>
    </div>
</header>
