<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php wp_head() @endphp

    <style media="screen">
        :root {
            --tileplanner-primary-color: <?php echo carbon_get_theme_option('tileplanner_primary_color') ?? 'red'; ?>;
        }
    </style>

</head>
