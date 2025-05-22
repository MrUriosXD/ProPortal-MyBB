# ProPortal 1.0 para MyBB

**ProPortal 1.0** es una extensión para MyBB que convierte el portal estándar en una página de inicio vibrante y fácil de personalizar. Con ProPortal, los administradores pueden crear una experiencia única para su comunidad, mostrando hilos recientes, noticias, estadísticas o encuestas en bloques y columnas flexibles. Todo se configura desde el panel de administración, donde puedes ajustar el diseño y decidir quién ve qué, sin complicaciones.

## Características

- **Columnas configurables**: Organiza el portal en columnas (izquierda, centro, derecha) con diseño ajustable.
- **Columnas superior e inferior**: Activa o desactiva las columnas "Top" y "Bottom" mediante checkboxes para mayor flexibilidad.
- **Bloques personalizados**: Añade contenido estático o dinámico, como anuncios, últimos mensajes o bloques impulsados por PHP.
- **Permisos por grupos**: Configura qué grupos de usuarios pueden ver cada bloque, perfecto para contenido privado.
- **Páginas personalizadas**: Crea páginas adicionales dentro del portal para contenido específico.
- **Ancho y espaciado ajustable**: Personaliza el diseño del portal ajustando el ancho de las columnas y el espacio entre bloques.
- **Control de avatares**: Opción global para activar o desactivar la visualización de avatares en el portal.
- **Color del grupo**: Habilita o desactiva los colores asociados a los grupos de usuarios para un diseño uniforme.
- **Instalación sencilla**: Incluye un asistente de instalación accesible vía `portal/install/index.php`.

## Requisitos

- MyBB 1.6.x o 1.8.x (verifica la compatibilidad con tu versión específica).
- Servidor web con soporte para PHP y MySQL.
- Acceso de escritura en la carpeta raíz del foro para subir archivos.

## Instalación

El proceso de instalación de ProPortal 1.0 es sencillo. Sigue estos pasos:

1. Haz una copia de seguridad de tu archivo `portal.php` actual.
2. Descarga y descomprime el archivo de ProPortal 1.0.
3. Sube el contenido de la carpeta `Upload` a la carpeta raíz de tu foro MyBB.
4. Accede al asistente de instalación visitando `http://www.tuforo.com/portal/install/index.php`.
5. Sigue las instrucciones del asistente para completar la instalación.
6. Visita tu nuevo portal en `portal.php`.

### Configuración de URLs Amigables (SEF)

Para habilitar URLs amigables en las páginas del portal, añade la siguiente regla a tu archivo `.htaccess`:

```apache
RewriteRule ^page-([0-9A-Za-z]+)\.html$ portal.php?pages=$1 [L,QSA]
```

### Redirección a la Página del Portal

Si deseas que los usuarios sean redirigidos al portal al ingresar el dominio de tu foro, añade lo siguiente a tu archivo `.htaccess`:

```apache
DirectoryIndex portal.php
```

## Desinstalación

Para desinstalar ProPortal 1.0:

1. Elimina el archivo `lock` de la carpeta `portal/install/`.
2. Accede al asistente de desinstalación en `http://www.tuforo.com/portal/install/uninstall.php`.
3. Haz clic en "Siguiente" para que el asistente elimine los datos de ProPortal de la base de datos, respaldándolos en la carpeta de instalación.
4. Elimina manualmente los archivos de ProPortal de tu servidor.

## Notas Importantes

- **Compatibilidad**: ProPortal 1.0 es compatible con MyBB 1.8.38, la versión actual de MyBB. Asegúrate de usar esta versión para un rendimiento óptimo. Ten en cuenta que ProPortal 1.0.0 es la versión más reciente disponible.

## Soporte

Si tienes problemas o preguntas sobre ProPortal, visita los foros de [ProMyBB](http://www.promybb.com) para obtener ayuda de la comunidad.

## Contribuciones

¡Las contribuciones son bienvenidas! Si deseas contribuir al desarrollo de ProPortal:

1. Haz un fork de este repositorio.
2. Crea una rama para tu funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3. Realiza tus cambios y haz commit (`git commit -m 'Añadir nueva funcionalidad'`).
4. Sube tu rama al repositorio (`git push origin feature/nueva-funcionalidad`).
5. Abre un Pull Request para revisión.

Por favor, sigue las guías de estilo de MyBB y asegúrate de probar tus cambios en un entorno de desarrollo.

## Autor

ProPortal 1.0 fue creado por Adnan TOPAL, © 2010.

## Licencia

Este proyecto no especifica una licencia en la documentación original. Contacta al autor para más información sobre los términos de uso.

---

**Fuentes**: Información basada en mybb-es.com, community.mybb.com, y la documentación oficial de ProPortal.
