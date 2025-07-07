# ProPortal 1.0 para MyBB

**ProPortal 1.0** es una extensión para MyBB que transforma el portal estándar en una página de inicio vibrante y fácil de personalizar. 
Con ello, los administradores pueden ofrecer a su comunidad una experiencia única, mostrando hilos recientes, noticias, estadísticas, encuestas, entre otros bloques.
Es por ello que hay que contar, que todo se configura desde el panel de administración en la pestaña de ProPortal.

## Características

- **Columnas configurables**: Organiza el portal en columnas (izquierda, centro, derecha) con diseño ajustable.
- **Columnas superior e inferior**: Activa o desactiva las columnas superior, la inferior, ambas o ninguna, mediante checkboxes en el panel de administración..
- **Bloques personalizados**: Añade contenido estático o dinámico, como anuncios, últimos mensajes o bloques con PHP.
- **Permisos por grupos**: Define qué grupos de usuarios ven cada bloque, ideal para contenido exclusivo.
- **Páginas personalizadas**: Crea páginas adicionales dentro del portal para contenido único.
- **Ancho y espaciado ajustable**: Personaliza el diseño con control sobre el ancho de columnas y el espacio entre bloques.
- **Control de avatares**: Activa o desactiva globalmente la visualización de avatares en el portal.
- **Colores de grupo**: Habilita o desactiva los colores de los grupos de usuarios para un diseño uniforme.
- **Instalación sencilla**: Incluye un asistente accesible vía `[tu-dominio]/portal/install/index.php`.
## Galería de Capturas

<details>
  <summary>Haz clic para ver las capturas de pantalla</summary>

  | Información del ProPortal | Configuración del ProPortal | Gestión de Bloques | Vista General del ProPortal |
  |:------------------------:|:--------------------------:|:------------------:|:--------------------------:|
  | <img src="https://github.com/user-attachments/assets/38548ec4-16d3-4e66-bf85-78c6c0565b8d" width="400"/>| <img src="https://github.com/user-attachments/assets/3148ab17-b8c0-4a11-b541-036b81b3f8fa" width="400"/> | <img src="https://github.com/user-attachments/assets/d24642da-d167-4a71-a667-c91ca9f5df3d" width="400"/> | <img src="https://github.com/user-attachments/assets/3c80633d-973c-41f3-9466-2e23e7483d71" width="400"/> |

</details>

## Requisitos

- MyBB 1.8.38 (recomendado para un rendimiento óptimo; compatible con 1.6.x y 1.8.x, verifica tu versión).
- Servidor web con soporte para PHP y MySQL.
- Acceso de escritura en la carpeta raíz del foro y en `proportal/install/` para la instalación.

## Instalación

El proceso de instalación de ProPortal 1.0 es directo:

1. Haz una copia de seguridad de tu archivo `portal.php`.
2. Descarga y descomprime el archivo de ProPortal 1.0.
3. Sube el contenido de la carpeta `Upload` a la raíz de tu foro MyBB.
4. Asegúrate de que la carpeta `proportal/install/` tenga permisos de escritura.
5. Accede al asistente de instalación en `[tu-dominio]/proportal/install/index.php`.
6. Sigue las instrucciones del asistente para completar la instalación.
7. Visita tu nuevo portal en `[tu-dominio]/portal.php`.

### URLs Amigables (SEF)

Para habilitar URLs amigables en las páginas del portal, añade esta regla a tu archivo `.htaccess`:

Esto no existe, lo hemos quitado por ahora los pages.
```apache
RewriteRule ^page-([0-9A-Za-z]+)\.html$ portal.php?pages=$1 [L,QSA]
```

### Redirección a la Página del Portal

Para redirigir a los usuarios al portal al entrar en tu foro, añade lo siguiente a tu `.htaccess`:

```apache
DirectoryIndex portal.php
```

## Desinstalación

Para desinstalar ProPortal 1.0:

1. Elimina el archivo `lock` de la carpeta `portal/install/`.
2. Asegúrate de que la carpeta `proportal/install/` tenga permisos de escritura.
3. Accede al asistente de desinstalación en `[tu-dominio]/proportal/install/uninstall.php`.
4. Haz clic en "Siguiente" para que el asistente elimine los datos de ProPortal, respaldándolos en la carpeta de instalación.
5. Elimina manualmente los archivos de ProPortal de tu servidor.

## Notas Importantes

- **Compatibilidad**: ProPortal 1.0 es compatible con MyBB 1.8.38, la versión actual de MyBB. Usa esta versión para un rendimiento óptimo.

## Soporte

ProPortal se originó en los foros de [ProMyBB](http://www.promybb.com), pero el desarrollador original, Adnan TOPAL, dejó de participar hace años, dejando la extensión sin un espacio oficial de soporte. 
Si ProPortal crece en popularidad, planeamos crear un foro de soporte financiado por donaciones y un hosting dedicado, según la demanda de la comunidad. 
Por ahora, te invitamos a reportar problemas o sugerencias abriendo un *issue* en este repositorio o contribuyendo al proyecto.

## Contribuciones

¡Tus contribuciones son bienvenidas! Para colaborar en el desarrollo de ProPortal:

1. Haz un *fork* de este repositorio.
2. Crea una rama para tu funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3. Realiza tus cambios y haz *commit* (`git commit -m 'Añadir nueva funcionalidad'`).
4. Sube tu rama al repositorio (`git push origin feature/nueva-funcionalidad`).
5. Abre un *Pull Request* para revisión.

Sigue las guías de estilo de MyBB y prueba tus cambios en un entorno de desarrollo.

## Autor

ProPortal 1.0 fue creado por Adnan TOPAL, © 2010.

## Créditos y Modificaciones

Esta versión de ProPortal ha sido adaptada y optimizada por **MrUriosXD**, basada en la versión **1.0.0 beta** e incorporando inspiración de las mejoras de la versión **2.6.2**, desarrollada por otro autor. Las actualizaciones incluyen compatibilidad con MyBB 1.8.38 y mejoras en la estabilidad, preservando la esencia original de la extensión.

## Licencia

Este proyecto no especifica una licencia explícita en la documentación original. En ausencia de términos claros, se recomienda asumir restricciones estándar (por ejemplo, uso personal o no comercial) y proceder con precaución.

---

**Fuentes**: Información basada en mybb-es.com, community.mybb.com, y la documentación oficial de ProPortal.
