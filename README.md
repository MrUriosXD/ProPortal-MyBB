# ProPortal 1.0 para MyBB

**ProPortal 1.0** es una extensión para MyBB que transforma el portal estándar en una página de inicio vibrante y fácil de personalizar.  
Con ello, los administradores pueden ofrecer a su comunidad una experiencia única, mostrando hilos recientes, noticias, estadísticas, encuestas, entre otros bloques.  
Todo se configura desde el panel de administración en la pestaña de ProPortal.

---

## ✨ Características

- 🧩 **Columnas configurables**: Organiza el portal en columnas (izquierda, centro, derecha) con diseño ajustable.
- 🏗️ **Columnas superior e inferior**: Activa o desactiva las columnas superior, inferior, ambas o ninguna, mediante checkboxes en el panel de administración.
- 📦 **Bloques personalizados**: Añade contenido estático o dinámico, como anuncios, últimos mensajes o bloques con PHP.
- 🛡️ **Permisos por grupos**: Define qué grupos de usuarios ven cada bloque, ideal para contenido exclusivo.
- 📄 **Páginas personalizadas**: Crea páginas adicionales dentro del portal para contenido único.
- 📐 **Ancho y espaciado ajustable**: Personaliza el diseño con control sobre el ancho de columnas y el espacio entre bloques.
- 👤 **Control de avatares**: Activa o desactiva globalmente la visualización de avatares en el portal.
- 🎨 **Colores de grupo**: Habilita o desactiva los colores de los grupos de usuarios para un diseño uniforme.
- ⚡ **Instalación sencilla**: Incluye un asistente accesible vía `[tu-dominio]/portal/install/index.php`.

---

## 🖼️ Galería de Capturas

<details>
  <summary>Haz clic para ver las capturas de pantalla</summary>

  | ℹ️ Información del ProPortal | ⚙️ Configuración del ProPortal | 🧮 Gestión de Bloques | 🏠 Vista General del ProPortal | 📊 Estadísticas |
  |:---------------------------:|:-----------------------------:|:--------------------:|:-----------------------------:|:----------------:|:-----------:|
  | <img src="https://github.com/user-attachments/assets/dac03a74-aee1-4dd9-8294-65bc171f2dff" width="400"/> | <img src="https://github.com/user-attachments/assets/944ca764-e678-497d-ba67-f4a511261d31" width="400"/> | <img src="https://github.com/user-attachments/assets/d6bdf3ed-dc7f-420d-9af7-29f8bc95d0a6" width="400"/> | <img src="https://github.com/user-attachments/assets/02a9f6bc-e498-4c8b-ac74-59c77769eb17" width="400"/> | <img src="https://github.com/user-attachments/assets/b8d9f410-609f-4870-af4d-ef57f33dce8d" width="400"/> |

</details>

<img width="1360" height="880" alt="screencapture-localhost-defaultportal-admin-index-php-2025-09-27-15_47_47" src="https://github.com/user-attachments/assets/dac03a74-aee1-4dd9-8294-65bc171f2dff" />
<img width="1360" height="1506" alt="screencapture-localhost-defaultportal-admin-index-php-2025-09-27-15_49_27" src="https://github.com/user-attachments/assets/944ca764-e678-497d-ba67-f4a511261d31" />
<img width="1360" height="1334" alt="screencapture-localhost-defaultportal-admin-index-php-2025-09-27-15_49_42" src="https://github.com/user-attachments/assets/d6bdf3ed-dc7f-420d-9af7-29f8bc95d0a6" />
<img width="1360" height="936" alt="screencapture-localhost-defaultportal-admin-index-php-2025-09-27-15_49_56" src="https://github.com/user-attachments/assets/02a9f6bc-e498-4c8b-ac74-59c77769eb17" />
<img width="1360" height="599" alt="screencapture-localhost-defaultportal-admin-index-php-2025-09-27-15_52_53" src="https://github.com/user-attachments/assets/b8d9f410-609f-4870-af4d-ef57f33dce8d" />
---

## 📋 Requisitos

- 💬 MyBB 1.8.38 (recomendado para un rendimiento óptimo; compatible con 1.6.x y 1.8.x, verifica tu versión).
- 🌐 Servidor web con soporte para PHP y MySQL.
- ✍️ Acceso de escritura en la carpeta raíz del foro y en `proportal/install/` para la instalación.

---

## ⚙️ Instalación

El proceso de instalación de ProPortal 1.0 es directo:

1. 🗂️ Haz una copia de seguridad de tu archivo `portal.php`.
2. 📦 Descarga y descomprime el archivo de ProPortal 1.0.
3. ⬆️ Sube el contenido de la carpeta `Upload` a la raíz de tu foro MyBB.
4. 🔑 Asegúrate de que la carpeta `proportal/install/` tenga permisos de escritura.
5. 🛠️ Accede al asistente de instalación en `[tu-dominio]/proportal/install/index.php`.
6. ✅ Sigue las instrucciones del asistente para completar la instalación.
7. 🌟 Visita tu nuevo portal en `[tu-dominio]/portal.php`.

### 🌍 URLs Amigables (SEF)

Para habilitar URLs amigables en las páginas del portal, añade esta regla a tu archivo `.htaccess`:

> Esto no existe, lo hemos quitado por ahora los pages.
```apache
RewriteRule ^page-([0-9A-Za-z]+)\.html$ portal.php?pages=$1 [L,QSA]
```

### 🔁 Redirección a la Página del Portal

Para redirigir a los usuarios al portal al entrar en tu foro, añade lo siguiente a tu `.htaccess`:

```apache
DirectoryIndex portal.php
```

---

## ❌ Desinstalación

Para desinstalar ProPortal 1.0:

1. 🗑️ Elimina el archivo `lock` de la carpeta `[tu-dominio]/proportal/install/` y manten el archivo `installed` para proceder con la desinstalación .
2. ✍️ Asegúrate de que la carpeta `[tu-dominio]/proportal/install/` tenga permisos de escritura.
3. 🛠️ Accede al instalador desde `[tu-dominio]/proportal/install/index.php` y sigue los pasos.  
4. 📤 Elimina manualmente los archivos de ProPortal de tu servidor para proceder con la desinstalacion por completo.

---

## ⚠️ Notas Importantes

- 🧩 **Compatibilidad**: ProPortal 1.0 es compatible con MyBB 1.8.39, la versión actual de MyBB. Usa esta versión para un rendimiento óptimo.

---

## 🆘 Soporte

ProPortal se originó en los foros de [ProMyBB](http://www.promybb.com), pero el desarrollador original, Adnan TOPAL, dejó de participar hace años, dejando la extensión sin un espacio oficial de soporte.

Si ProPortal crece en popularidad, planeamos crear un foro de soporte financiado por donaciones y un hosting dedicado, según la demanda de la comunidad.  
Por ahora, te invitamos a reportar problemas o sugerencias abriendo un *issue* en este repositorio o contribuyendo al proyecto.

---

## 🤝 Contribuciones

¡Tus contribuciones son bienvenidas! Para colaborar en el desarrollo de ProPortal:

1. 🍴 Haz un *fork* de este repositorio.
2. 🌿 Crea una rama para tu funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3. ✏️ Realiza tus cambios y haz *commit* (`git commit -m 'Añadir nueva funcionalidad'`).
4. 🚀 Sube tu rama al repositorio (`git push origin feature/nueva-funcionalidad`).
5. 🔍 Abre un *Pull Request* para revisión.

Sigue las guías de estilo de MyBB y prueba tus cambios en un entorno de desarrollo.

---

## 🏷️ Créditos y Modificaciones

### 👤 Autor original (versión base)
- **Nombre**: Adnan TOPAL  (Conocido como **DragonFever**)
- **Ultima Actualizacion**: 14/01/2010
- **Versión Base**: 1.0.0 beta  
- **Descripción**: Desarrollador original de la extensión **ProPortal** para MyBB.

---

### 💡 Inspiración adicional
- **Autor**: [MrBrechreiz](https://www.mybb.de/forum/user-5076.html)
- **Ultima Actualizacion**:	25/02/2018
- **Versión**: ProPortal 2.6.2
- **Descripción**: Se han tomado ideas y mejoras puntuales de esta versión para complementar el desarrollo.

---

### 🛠️ Adaptación y mejoras por MrUriosXD

- **Autor**: MrUriosXD  
- **Versión adaptada**: 1.0.0 (100)
- **Ultima Actualizacion**:	Aun no tiene version a el publico
- **Compatibilidad**: MyBB 1.8.39  
- **PHP**: Adaptado completamente a PHP 8.2

#### Cambios realizados:
- Conservación de la estructura y esencia del desarrollo original
- Correcciones de errores y mejoras de estabilidad
- Limpieza y optimización del código

---

### 🎯 Objetivo

Esta versión busca ofrecer una edición moderna, funcional y estable de **ProPortal**, fiel al espíritu original, pero adaptada a las necesidades y tecnologías actuales.


----------------------------------------

---

## 📜 Licencia

Este proyecto está licenciado bajo la licencia [Atribución-NoComercial-CompartirIgual 3.0 No Adaptada (CC BY-NC-SA 3.0)](https://creativecommons.org/licenses/by-nc-sa/3.0/deed.es).

---

**Fuentes**: Información basada en mybb-es.com, community.mybb.com, y la documentación oficial de ProPortal.
