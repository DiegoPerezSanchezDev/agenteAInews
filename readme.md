# Agente de Noticias IA 🤖📰

Un agente automatizado que busca las noticias más relevantes sobre Inteligencia Artificial y te las envía cada semana en un elegante boletín bilingüe (español e inglés).

Este proyecto funciona de forma 100% autónoma utilizando **GitHub Actions**, por lo que no necesitas un servidor ni un hosting para ejecutarlo.

![alt text](image.png)

---

## ✨ Características

-   **Resumen Semanal:** Se ejecuta automáticamente cada domingo por la mañana.
-   **Contenido Bilingüe:** Recopila las 7 noticias más importantes en español y en inglés.
-   **Formato Profesional:** El correo está diseñado en HTML para una lectura limpia y agradable.
-   **Coste Cero:** Utiliza únicamente servicios con generosos planes gratuitos (GitHub Actions y NewsAPI).
-   **Fácil de Configurar:** Solo necesitas configurar unas claves de API como secrets en tu repositorio.
-   **Personalizable:** Puedes modificar fácilmente los términos de búsqueda, el horario de envío o el diseño del correo.

---

## 🚀 Instalación y Configuración

Sigue estos pasos para tener tu propio agente de noticias funcionando en minutos.

### Paso 1: Prerrequisitos

Necesitarás cuentas en los siguientes servicios (todos gratuitos):
1.  Una cuenta de **GitHub**.
2.  Una cuenta de **Google (Gmail)** para enviar los correos.
3.  Una clave de API de **[NewsAPI.org](https://newsapi.org/)**.

### Paso 2: Obtener Claves y Contraseñas

1.  **Clave de NewsAPI:**
    -   Regístrate en [NewsAPI.org](https://newsapi.org/) y obtén tu clave de API desde el panel de control.

2.  **Contraseña de Aplicación de Gmail:**
    -   **Activa la Verificación en Dos Pasos** en tu cuenta de Google (es obligatorio).
    -   Ve a **[Contraseñas de Aplicaciones de Google](https://myaccount.google.com/apppasswords)**.
    -   Crea una nueva contraseña para una aplicación personalizada (ej: "Agente Noticias GitHub").
    -   Copia la **contraseña de 16 caracteres** que se genera. ¡Esta es la que usarás, no tu contraseña normal!

### Paso 3: Configurar el Repositorio de GitHub

1.  **Haz un "Fork"** de este repositorio o clónalo en tu propia cuenta de GitHub.
2.  Ve a la pestaña **`Settings` > `Secrets and variables` > `Actions`**.
3.  Crea los siguientes **`Repository secrets`**:

| Nombre del Secret      | Valor que debes pegar                                       |
| ---------------------- | ----------------------------------------------------------- |
| `NEWS_API_KEY`         | Tu clave de API de NewsAPI.                                 |
| `GMAIL_USER`           | Tu dirección de correo de Gmail (ej: `tu_correo@gmail.com`).  |
| `GMAIL_APP_PASSWORD`   | La contraseña de aplicación de 16 caracteres que generaste. |
| `RECIPIENT_EMAIL`      | El correo donde quieres recibir las noticias (puede ser el mismo `GMAIL_USER`). |

### Paso 4 (Opcional): Crear Filtro en Gmail

Para mantener tu bandeja de entrada organizada, crea un filtro en Gmail que mueva automáticamente estos correos a una etiqueta.
-   **Asunto:** `[AI NEWS] Tu Resumen Semanal de Inteligencia Artificial`
-   **Acción:** `Aplicar la etiqueta` -> `Nueva etiqueta...` -> `AI NEWS`.

---

## 🛠️ Uso y Pruebas

El agente se ejecutará automáticamente cada domingo. Si quieres probarlo inmediatamente:
1.  Ve a la pestaña **`Actions`** de tu repositorio.
2.  En el menú de la izquierda, haz clic en **`Enviar Resumen Semanal de Noticias IA`**.
3.  Haz clic en el botón **`Run workflow`** para lanzarlo manualmente.

En un par de minutos, deberías recibir tu primer boletín de noticias.

---

## ⚙️ Personalización

Puedes adaptar el agente fácilmente:
-   **Cambiar el Horario:** Modifica la expresión `cron` en el archivo `.github/workflows/main.yml`.
-   **Cambiar los Temas de Búsqueda:** Edita la variable `$query` en el archivo `enviar_noticias.php` para buscar noticias sobre otros temas.

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Siéntete libre de usarlo y modificarlo como quieras.