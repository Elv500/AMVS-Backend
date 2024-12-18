**Backend - Gestión de Torneos de Voleibol 🏐**

Requisitos
PHP: 8.1 o superior
Composer: Instalado
Base de datos: Psql
Laravel: 9.x o superior

-----------------------------------------------------------
**Pasos para ejecutar el Backend**

Clonar el repositorio
git clone <URL_DEL_REPOSITORIO>
cd backend

Instalar dependencias
*composer install*

Configurar el archivo .env
Crea una copia del archivo .env.example:

cp .env.example .env
Configura la conexión a la base de datos en el archivo .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña

Generar clave de la aplicación
*php artisan key:generate*

Ejecutar las migraciones y seeders
*php artisan migrate --seed*

Archivos publicos accesibles en storage
*php artisan storage:link*

Levantar el servidor
*php artisan serve*

------------------------------------------------------------------------------------
Rutas Principales del Backend

Torneos:
GET /api/tournaments → Listar torneos
POST /api/tournaments → Crear un nuevo torneo

Rol de Partidos:
POST /api/matches/generate → Generar partidos automáticamente

Partidos:
GET /api/matches/tournament/{id} → Listar partidos por torneo

Tabla de Posiciones:
GET /api/teams/leaderboard/tournament/{id} → Tabla de posiciones filtrada por torneo

-------------------------------------------------------------------------------------