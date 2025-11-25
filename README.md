# Sistema de Gestión Documental Médico

Sistema web simplificado para la gestión de documentos médicos, desarrollado en PHP con arquitectura MVC y desplegado en Docker.

## Características

- **Autenticación Segura**: Registro y login con hashing de contraseñas.
- **Roles de Usuario**:
  - **Administrador (Personal Médico)**: Puede subir documentos y asignarlos a pacientes.
  - **Paciente**: Puede ver y descargar sus documentos asignados.
- **Gestión Documental**: Subida, listado y descarga de archivos (PDF, imágenes, etc.).
- **Interfaz Médica**: Diseño limpio y profesional adaptado al entorno sanitario.
- **Dockerizado**: Entorno completo con PHP, Apache, MySQL y phpMyAdmin.

## Requisitos

- Docker y Docker Compose instalados.

## Instalación y Despliegue

1. Clonar el repositorio o descargar los archivos.
2. Abrir una terminal en la carpeta raíz del proyecto.
3. Ejecutar el siguiente comando para construir y levantar los contenedores:

```bash
docker-compose up -d --build
```

4. Esperar unos segundos a que los servicios inicien correctamente.

## Acceso al Sistema

- **Aplicación Web**: [http://localhost:8080](http://localhost:8080)
- **phpMyAdmin**: [http://localhost:8081](http://localhost:8081)

### Credenciales por Defecto

**Base de Datos (MySQL):**

- Usuario: `root`
- Contraseña: `rootpassword`
- Base de datos: `medical_dms`

**Usuario Administrador Pre-configurado:**

- Email: `admin@hospital.com`
- Contraseña: `admin123`

## Estructura del Proyecto

```
/
├── config/             # Configuración de BD y constantes
├── controllers/        # Controladores (Lógica de negocio)
├── database/           # Scripts SQL de inicialización
├── helpers/            # Funciones auxiliares
├── models/             # Modelos (Acceso a datos)
├── public/             # Archivos estáticos (CSS, JS, imágenes)
├── uploads/            # Carpeta de almacenamiento de documentos
├── views/              # Vistas (HTML/PHP)
├── .env.example        # Variables de entorno de ejemplo
├── docker-compose.yml  # Orquestación de contenedores
├── Dockerfile          # Configuración de imagen PHP
└── index.php           # Front Controller
```

## Uso

1. **Registro**: Los usuarios pueden registrarse como "Paciente" o "Personal Médico".
2. **Login**: Acceder con email y contraseña.
3. **Admin**:
   - Subir documentos seleccionando un paciente de la lista.
   - Ver lista de documentos subidos.
   - Eliminar documentos.
4. **Paciente**:
   - Ver lista de documentos propios.
   - Descargar documentos.
