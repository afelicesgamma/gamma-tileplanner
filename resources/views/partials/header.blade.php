<header class="bg-white">
    <div class="max-w-[1400px] py-[20px] mx-auto">
        <div class="flex items-center justify-between">
            <img class="h-[1.8rem] w-auto" src="{{$logo}}">

            @if ($is_user_logged_id)
            <div class="flex items-center gap-3">
                <nav class="flex items-center gap-3 text-sm">
                    <a class="" href="{{home_url( $tileplanner_route )}}">Inicio</a>
                    <a class="" href="{{home_url( $tileplanner_route . '?proyectos' )}}">Proyectos abiertos</a>
                </nav>

                <nav class="flex items-center gap-3 text-sm" aria-label="Usuario">
                    <span class="">Hola {{esc_html( $user_display_name )}}</span>
                    <a class="ml-2" href="{{$logout_url}}" aria-label="Cerrar sesión" title="Cerrar sesión">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5m5 5H3"/>
                        </svg>
                    </a>
                </nav>
            </div>
            @endif

        </div>
    </div>
</header>
