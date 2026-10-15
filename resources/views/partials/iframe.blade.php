@php
$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
$payload = json_encode(
    [
        "tp:tid" => "GAMMA",
        "tp:uid" => $user_id,
        "tp:gid" => $group_id,
        "tp:mxp" => 4,
        "exp" => time() + 86400
    ]
);

$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

$delete_project_id = $_POST['delete_project'] ?? false;
if($delete_project_id) {
    $url = 'https://d3dwebapp.maticad.com/api/host/d3dweb/GAMMA/projects/' . $delete_project_id;

    $ch = curl_init($url);
    $authorization = "Authorization: Bearer ". $jwt;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization ));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    $res = curl_exec($ch);
    curl_close($ch);
}

$show_projects = isset($_GET['proyectos']);
if($show_projects) {
    $url = 'https://d3dwebapp.maticad.com/api/host/d3dweb/GAMMA/projects?username=' . $user_id;

    $ch = curl_init($url);
    $authorization = "Authorization: Bearer ". $jwt;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization ));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
}

$show_gamma_projects = isset($_GET['ambientes']);
if($show_gamma_projects) {
    $url = 'https://d3dwebapp.maticad.com/api/host/d3dweb/GAMMA/projects';

    $ch = curl_init($url);
    $authorization = "Authorization: Bearer ". $jwt;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization ));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
}

$projectId = false;
if( isset($_GET['proyecto']) ) $projectId = sanitize_text_field( $_GET['proyecto'] );

$pano360Path = false;
if( isset($_GET['pano360_path']) ) $pano360Path = sanitize_text_field( $_GET['pano360_path'] );
@endphp

@if ($show_projects && $response)
    @php
    $proyectos = json_decode($response);
    @endphp
    <div class="py-10 lg:py-20 bg-primary mb-10" style="{!! $background_image ? 'background-image: url(\''. $background_image . '\'); background-size: cover; background-position: center;'  : '' !!}">
        <div class="max-w-screen-lg mx-auto">
            <h1 class="font-sans text-2xl lg:text-4xl font-bold text-white">Todos tus proyectos</h1>
        </div>
    </div>
    <div class="max-w-screen-lg mx-auto py-5">
        <div class="md:grid md:grid-cols-3 md:gap-4">
            @foreach ($proyectos as $proyecto)
                <div class="p-5 border-primary border-2">
                    <a href="<?= home_url( $tileplanner_route . '?proyecto=' . $proyecto->id ) ?>">
                        <img class="h-[200px] lg:h-[400px] w-full object-cover object-center" src="<?= $proyecto->preview ?>" alt="<?= $proyecto->name ?>">
                        <h2 class="font-bold text-2xl text-center text-primary mb-5 font-sans"><?= $proyecto->name ?></h2>
                    </a>
                    <div class="grid grid-cols-2 gap-4">
                        <a class="block" href="<?= home_url( $tileplanner_route. '?proyecto=' . $proyecto->id ) ?>">
                            <button class="w-full bg-primary border-primary py-1 px-4 rounded text-white">Editar</button>
                        </a>
                        <form id="delete-project-form" class="delete-project-form" action="" method="post">
                            <input type="hidden" name="delete_project" value="<?= $proyecto->id ?>">
                            <button class="w-full block bg-white py-1 px-4 rounded border border-black" type="submit">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
    window.onload = function() {
        document.getElementById('delete-project-form').addEventListener("submit", function(event) {
            if (!confirm('¿Seguro que quieres borrar el proyecto?')) event.preventDefault();
        });
    };
    </script>
@elseif ($projectId)
    <iframe style="border: none; min-height: calc(100vh - 70px);" width="100%" height="100%" src="https://d3dwebapp.maticad.com?lang=es&token=<?= $jwt ?>&projectId=<?= $projectId ?>" title="Proyecto <?= $projectId ?>"></iframe>
@else
    <iframe style="border: none; min-height: calc(100vh - 70px);" width="100%" height="100%" src="https://d3dwebapp.maticad.com?lang=es&token=<?= $jwt ?>" title="Tileplanner"></iframe>
@endif
