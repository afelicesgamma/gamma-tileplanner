# Gamma Tileplanner

Plugin de WordPress que integra el planificador de espacios Tileplanner de
Gamma Sites. Presenta la aplicación de Tileplanner dentro del sitio y permite
a los usuarios autenticados abrir el planificador y acceder a sus proyectos.

La interfaz se sirve en una ruta configurable. Si el campo de slug se deja
vacío, Tileplanner se muestra en la página de inicio del sitio.
Los usuarios que no han iniciado sesión ven las opciones para acceder o
registrarse, solicitar un enlace de recuperación y cambiar su contraseña.
Los usuarios autenticados pueden abrir el planificador, consultar sus
proyectos y abrir o eliminar proyectos existentes. El registro asigna el rol
`subscriber` explícitamente.

Solo los usuarios con la capacidad `manage_options` pueden acceder al backend
de WordPress. El plugin oculta además entradas del menú y de la barra de
administración; ocultar esas entradas no revoca permisos.

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
- PHP 8.2 o posterior.
- Composer.
- Node.js 22.12 o posterior (el proyecto fija Node.js 24.11.1 con Volta).
- npm.

## Desarrollo

```bash
composer install
npm ci
npm run dev
```

`npm run dev` recompila los recursos de frontend en `dist/` cuando detecta
cambios. Para generar una compilación de producción manualmente, ejecuta
`npm run build`.

El frontend se compila con Vite 8, Tailwind CSS 4 y Alpine.js. Tailwind se
integra mediante el plugin de Vite, sin configuración de PostCSS. El lint de
los scripts JavaScript y TypeScript se ejecuta con `npm run lint`.
La fuente Inter se sirve desde archivos locales del plugin.

Tailwind CSS 4 requiere navegadores modernos: Safari 16.4+, Chrome 111+ o
Firefox 128+.

## Para preparar los archivos de producción

Desde el directorio del plugin, ejecuta:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
composer run-script zip
```

El comando de Composer genera el archivo `gamma-tileplanner.zip` en el
directorio padre. El paquete incluye `vendor/` y `dist/`, necesarios para que
el plugin funcione, y omite las dependencias y configuraciones de desarrollo.
El CSS, JavaScript y las fuentes locales se generan en `dist/`; el plugin carga
sus rutas con hash desde `dist/manifest.json` y los encola únicamente en la
ruta de Tileplanner.

## Base del proyecto

Este plugin se basa en el boilerplate [Plubo](https://github.com/joanrodas/plubo/).
