# Sistema POO para Procesamiento de Encuestas Cinematográficas

Un sistema completo desarrollado en PHP con Programación Orientada a Objetos (POO) para gestionar encuestas sobre gustos cinematográficos, utilizando Bootstrap con paleta violeta y almacenamiento en sesiones.

## 🎯 Características Principales

### ✨ Funcionalidades Core

- **Gestión de Encuestas**: Crear, leer, actualizar y eliminar encuestas
- **Validación Robusta**: Validación completa de datos en tiempo real
- **Búsqueda Avanzada**: Por ID o nombre de participante
- **Estadísticas Dinámicas**: Gráficos interactivos con Chart.js
- **Análisis de Datos**: Relaciones entre géneros, plataformas y países

### 🏗️ Arquitectura POO

- **Clase Encuesta**: Modelo de datos con validación
- **Clase EncuestaManager**: Gestión de la colección de encuestas
- **Clase EncuestaEstadisticas**: Cálculos estadísticos y análisis

### 🎨 Diseño y UX

- **Bootstrap 5**: Framework CSS responsivo
- **Paleta Violeta**: Diseño moderno y atractivo
- **Font Awesome**: Iconografía completa
- **Animaciones CSS**: Transiciones suaves
- **Responsive Design**: Compatible con dispositivos móviles

## 📁 Estructura del Proyecto

```
laboratorio2P1/
├── clases/
│   ├── Encuesta.php              # Modelo de encuesta individual
│   ├── EncuestaManager.php       # Gestión de colección de encuestas
│   └── EncuestaEstadisticas.php  # Cálculos estadísticos
├── views/
│   ├── header.php                # Header común con navegación
│   └── footer.php                # Footer común con scripts
├── assets/
│   └── style.css                 # Estilos personalizados con paleta violeta
├── index.php                     # Página principal
├── formulario.php                # Formulario para nueva encuesta
├── resultados.php                # Visualización de resultados y estadísticas
├── buscar.php                    # Búsqueda de encuestas
├── demo.php                      # Carga de datos de demostración
├── cerrarSesion.php              # Cierre de sesión
├── config.php                    # Configuración del sistema
└── README.md                     # Documentación
```

## 🚀 Instalación y Configuración

### Requisitos

- PHP 7.4 o superior
- Servidor web (Apache/Nginx)
- Navegador web moderno

### Instalación

1. Clone o descargue el proyecto en su servidor web
2. Asegúrese de que PHP tenga permisos para crear sesiones
3. Acceda a `index.php` desde su navegador

### Configuración

El archivo `config.php` contiene las configuraciones principales:

- Zona horaria
- Configuración de sesiones
- Parámetros de validación
- Autoloader de clases

## 📊 Funcionalidades Detalladas

### 1. Gestión de Encuestas

- **Crear**: Formulario con validación completa
- **Listar**: Tabla responsiva con todas las encuestas
- **Buscar**: Por ID exacto o nombre parcial
- **Eliminar**: Confirmación antes de eliminar

### 2. Validaciones Implementadas

- **ID**: Número entero positivo único
- **Nombre**: Solo letras y espacios
- **País**: Lista predefinida de países válidos
- **Género**: Lista predefinida de géneros cinematográficos
- **Frecuencia**: Opciones predefinidas de consumo
- **Plataformas**: Selección múltiple de plataformas válidas

### 3. Estadísticas y Análisis

- **Total de encuestas**
- **Género más popular** (con manejo de empates)
- **Plataforma más utilizada**
- **País con más participantes**
- **Distribución de frecuencias** (con porcentajes)
- **Análisis de relaciones** (género-plataforma, país-género)

### 4. Visualización de Datos

- **Gráficos Circulares**: Para distribución de géneros y países
- **Gráficos de Barras**: Para plataformas utilizadas
- **Gráficos Polares**: Para frecuencias de consumo
- **Tablas Interactivas**: Listados completos con acciones

## 🔧 Criterios de Aceptación Cumplidos

### ✅ Validación y Almacenamiento

- [x] Validación completa de campos obligatorios
- [x] ID único por encuesta
- [x] Validación de patrones (nombre solo letras)
- [x] Listas predefinidas para campos selectivos
- [x] Almacenamiento en `$_SESSION['encuestas']`
- [x] Formato asociativo con `toArray()`

### ✅ Clases POO

- [x] Clase `Encuesta` con atributos privados
- [x] Constructor con validación
- [x] Getters y setters apropiados
- [x] Método `toArray()` implementado
- [x] Validación estática de datos

### ✅ Gestión de Encuestas

- [x] `agregarEncuesta()` con verificación de ID único
- [x] `eliminarEncuesta()` con manejo de errores
- [x] `buscarEncuestas()` por ID y nombre
- [x] `obtenerTodas()` funcional
- [x] Validación de parámetros de entrada

### ✅ Estadísticas

- [x] `totalEncuestas()` implementado
- [x] `generoMasPopular()` con manejo de empates
- [x] `plataformaMasUsada()` con conteo múltiple
- [x] `paisConMasParticipantes()` con ranking
- [x] Métodos actualizables dinámicamente

### ✅ Visualización

- [x] Listado completo en tabla HTML
- [x] Resumen estadístico completo
- [x] Gráficos con Chart.js
- [x] Diseño responsivo y organizado
- [x] Actualización automática de interfaz

## 🎨 Paleta de Colores

La aplicación utiliza una paleta violeta consistente:

- **Primario**: `#6f42c1`
- **Secundario**: `#8e44ad`
- **Oscuro**: `#4a2c85`
- **Claro**: `#e3d5ff`
- **Extra Claro**: `#f4f0ff`

## 📱 Navegación

1. **Inicio**: Vista general con estadísticas rápidas
2. **Nueva Encuesta**: Formulario de registro
3. **Resultados**: Gráficos y estadísticas completas
4. **Buscar**: Funcionalidad de búsqueda avanzada
5. **Demo**: Carga de datos de ejemplo
6. **Cerrar Sesión**: Limpieza de datos

## 🧪 Datos de Demostración

La página `demo.php` permite cargar 15 encuestas de ejemplo con:

- Participantes de diferentes países latinoamericanos
- Variedad de géneros cinematográficos
- Diferentes frecuencias de consumo
- Múltiples plataformas de streaming

## 🔒 Seguridad

- Sanitización de datos de entrada
- Validación del lado del servidor
- Protección contra inyección de código
- Configuración segura de sesiones
- Verificación CSRF básica

## 🚀 Mejoras Futuras

- Exportación de datos (CSV, PDF)
- Filtros avanzados de búsqueda
- Comparativas temporales
- Sistema de roles y permisos
- Integración con base de datos
- API REST para datos

## 👥 Contribución

Este proyecto fue desarrollado como sistema educativo para demostrar:

- Programación Orientada a Objetos en PHP
- Arquitectura MVC básica
- Validación robusta de datos
- Visualización de estadísticas
- Diseño responsivo con Bootstrap

## 📄 Licencia

Proyecto educativo desarrollado para fines académicos.

---

**Desarrollado con ❤️ usando PHP POO, Bootstrap y Chart.js**
