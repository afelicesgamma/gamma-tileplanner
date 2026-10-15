# Gamma Tileplanner

Plugin de WordPress que integra el planificador de espacios Tileplanner de
Gamma Sites. Presenta la aplicación de Tileplanner dentro del sitio y permite
a los usuarios autenticados abrir el planificador y acceder a sus proyectos.

La interfaz se sirve en una ruta configurable. Si el campo de slug se deja
vacío, Tileplanner se muestra en la página de inicio del sitio.
Los usuarios que no han iniciado sesión ven las opciones para acceder o
registrarse. Los usuarios autenticados pueden abrir el planificador, consultar
sus proyectos y abrir o eliminar proyectos existentes.

## Configuración

El plugin añade el panel **Gamma Tileplanner** a las opciones del sitio. Desde allí
se pueden configurar:

- El slug de la ruta de Tileplanner (vacío para usar la página de inicio).
- El logo de la cabecera.
- El color primario.
- La imagen de fondo.

La aplicación se carga desde el servicio externo de Tileplanner de Maticad.

## Requisitos

- WordPress.
- PHP 7.4 o posterior.
- Dependencias PHP instaladas mediante Composer.
- Assets de frontend compilados en `dist/`.

Para preparar los archivos de desarrollo:

```bash
composer install
npm install
npm run build
```

## Base del proyecto

Este plugin se basa en el boilerplate [Plubo](https://github.com/joanrodas/plubo/).
