# 📋 GUÍA COMPLETA - GESTIÓN SCRUM EN SGCS

## ✅ ESTADO ACTUAL DEL SISTEMA

### Lo que está funcionando:
1. ✅ **Modelo de datos completo**: Sprints, User Stories, Daily Scrums
2. ✅ **Vistas Scrum**: Dashboard, Sprint Planning, Daily Scrum, Sprint Review, Sprint Retrospective
3. ✅ **API REST completa**: Crear sprints, iniciar sprints, crear user stories, actualizar tareas
4. ✅ **Relaciones de base de datos**: Sprints → User Stories funciona correctamente
5. ✅ **Seeders**: 3 sprints por proyecto Scrum (Sprint 1, 2, 3)
6. ✅ **Integración con SGCS**: Elementos de configuración vinculados a user stories

---

## 🎯 CÓMO USAR GESTIÓN SCRUM

### 1. **DASHBOARD SCRUM**
**URL**: `/proyectos/{id_proyecto}/scrum/dashboard`

**Funcionalidades**:
- Ver tablero Kanban con todas las user stories
- Cambiar entre sprints usando el selector
- Ver métricas del sprint (story points, tareas completadas)
- Visualizar burndown chart del sprint activo
- Drag & drop de tareas entre columnas (To Do → In Progress → In Review → Done)

**Fases del tablero**:
1. Product Backlog
2. To Do (Sprint Backlog)
3. In Progress (Doing)
4. In Review (Review)
5. Done (Completed)

---

### 2. **CREAR UN NUEVO SPRINT**
**Endpoint**: `POST /proyectos/{id_proyecto}/scrum/sprints`

**Body (JSON)**:
```json
{
  "nombre": "Sprint 4",
  "objetivo": "Implementar sistema de pagos y notificaciones",
  "fecha_inicio": "2025-11-15",
  "fecha_fin": "2025-11-29",
  "velocidad_estimada": 35
}
```

**Respuesta exitosa**:
```json
{
  "success": true,
  "message": "Sprint creado exitosamente",
  "sprint": {
    "id_sprint": 4,
    "nombre": "Sprint 4",
    "estado": "planificado",
    ...
  }
}
```

**Ejemplo con cURL**:
```bash
curl -X POST http://localhost:8000/proyectos/{id_proyecto}/scrum/sprints \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "Sprint 4",
    "objetivo": "Nuevas funcionalidades",
    "fecha_inicio": "2025-11-15",
    "fecha_fin": "2025-11-29"
  }'
```

---

### 3. **INICIAR UN SPRINT**
**Endpoint**: `POST /proyectos/{id_proyecto}/scrum/sprints/{id_sprint}/iniciar`

**Validaciones**:
- Solo sprints en estado "planificado" pueden iniciarse
- Solo puede haber 1 sprint activo a la vez
- Debe completar el sprint activo antes de iniciar otro

**Respuesta exitosa**:
```json
{
  "success": true,
  "message": "Sprint iniciado exitosamente",
  "sprint": {
    "id_sprint": 4,
    "estado": "activo"
  }
}
```

---

### 4. **CREAR USER STORY**
**Endpoint**: `POST /proyectos/{id_proyecto}/scrum/user-stories`

**Body (JSON)**:
```json
{
  "nombre": "Como usuario quiero poder resetear mi contraseña",
  "descripcion": "Implementar funcionalidad de recuperación de contraseña por email",
  "id_sprint": 3,
  "id_fase": 2,
  "story_points": 5,
  "prioridad": 1,
  "responsable": "usuario_id",
  "criterios_aceptacion": [
    "El usuario recibe un email con el link de reseteo",
    "El link expira en 24 horas",
    "La nueva contraseña cumple requisitos de seguridad"
  ]
}
```

**Campos**:
- `nombre` (requerido): Título de la user story
- `descripcion` (opcional): Descripción detallada
- `id_sprint` (opcional): ID del sprint (null = Product Backlog)
- `id_fase` (requerido): Fase del workflow (1-5)
- `story_points` (opcional): Puntos de estimación (1-100)
- `prioridad` (opcional): 1=Alta, 2=Media, 3=Baja, 4=Muy Baja, 5=Trivial
- `responsable` (opcional): ID del usuario asignado
- `criterios_aceptacion` (opcional): Array de criterios

**Respuesta**:
```json
{
  "success": true,
  "message": "User Story creada exitosamente",
  "userStory": {
    "id_tarea": 123,
    "nombre": "Como usuario quiero...",
    "estado": "To Do",
    "sprint": { "nombre": "Sprint 3" },
    ...
  }
}
```

---

### 5. **ACTUALIZAR USER STORY**
**Endpoint**: `PATCH /proyectos/{id_proyecto}/scrum/user-stories/{id_tarea}`

**Casos de uso**:
- Mover tarea entre fases (drag & drop en el tablero)
- Cambiar estado de la tarea
- Reasignar responsable
- Actualizar story points
- Mover tarea a otro sprint

**Body (JSON)** - Todos los campos son opcionales:
```json
{
  "estado": "In Progress",
  "id_fase": 3,
  "responsable": "nuevo_usuario_id",
  "story_points": 8
}
```

**Estados válidos**:
- `To Do`: Nueva tarea
- `In Progress`: En desarrollo
- `In Review`: En revisión
- `Done`: Completada
- `Completado`: Finalizada

---

### 6. **COMPLETAR UN SPRINT**
**Endpoint**: `POST /proyectos/{id_proyecto}/scrum/sprints/{id_sprint}/completar`

**Funcionalidad**:
- Cambia el estado del sprint a "completado"
- Calcula automáticamente la velocidad real (story points completados)
- Permite iniciar el siguiente sprint

**Respuesta**:
```json
{
  "success": true,
  "message": "Sprint completado exitosamente",
  "sprint": {
    "id_sprint": 3,
    "estado": "completado",
    "velocidad_estimada": 35,
    "velocidad_real": 28
  },
  "velocidad_real": 28
}
```

---

### 7. **REGISTRAR DAILY SCRUM**
**Endpoint**: `POST /proyectos/{id_proyecto}/scrum/daily-scrums`

**Body (JSON)**:
```json
{
  "id_sprint": 3,
  "id_miembro": "usuario_id",
  "que_hice_ayer": "Implementé el login con OAuth",
  "que_hare_hoy": "Voy a crear tests unitarios para autenticación",
  "impedimentos": "Necesito acceso al servidor de staging"
}
```

---

## 📊 ESTRUCTURA DE LA BASE DE DATOS

### Tabla: `sprints`
```sql
- id_sprint (PK)
- id_proyecto (FK → proyectos)
- nombre (VARCHAR 100)
- objetivo (TEXT)
- fecha_inicio (DATE)
- fecha_fin (DATE)
- velocidad_estimada (INT) -- Story points planeados
- velocidad_real (INT) -- Story points completados
- estado (ENUM: planificado, activo, completado, cancelado)
- observaciones (TEXT)
```

### Tabla: `tareas_proyecto` (User Stories)
```sql
- id_tarea (PK)
- id_proyecto (FK → proyectos)
- id_sprint (FK → sprints) -- Nueva columna
- sprint (VARCHAR 50) -- Deprecated, mantener para compatibilidad
- id_fase (FK → fases_metodologia)
- nombre (VARCHAR 255)
- descripcion (TEXT)
- story_points (INT)
- estado (VARCHAR 50)
- prioridad (INT)
- responsable (FK → usuarios)
- criterios_aceptacion (JSON)
```

### Tabla: `daily_scrums`
```sql
- id (PK)
- id_sprint (FK → sprints)
- id_miembro (FK → usuarios)
- fecha (DATE)
- que_hice_ayer (TEXT)
- que_hare_hoy (TEXT)
- impedimentos (TEXT)
```

---

## 🔄 FLUJO DE TRABAJO SCRUM

### Ciclo de un Sprint:

1. **SPRINT PLANNING**
   - Crear nuevo sprint: `POST /scrum/sprints`
   - Crear user stories del sprint: `POST /scrum/user-stories`
   - Estimar story points de cada user story
   - Iniciar el sprint: `POST /scrum/sprints/{id}/iniciar`

2. **DESARROLLO (Durante el sprint)**
   - Daily Scrum diario: `POST /scrum/daily-scrums`
   - Mover tareas en el tablero: `PATCH /scrum/user-stories/{id}`
   - Estados: To Do → In Progress → In Review → Done
   - Ver progreso en Dashboard

3. **SPRINT REVIEW**
   - Ver /scrum/sprint-review
   - Demostrar incremento completado
   - Revisar métricas y burndown chart

4. **SPRINT RETROSPECTIVE**
   - Ver /scrum/sprint-retrospective
   - Reflexionar sobre el proceso
   - Identificar mejoras

5. **FINALIZAR SPRINT**
   - Completar sprint: `POST /scrum/sprints/{id}/completar`
   - Sistema calcula velocidad real automáticamente
   - Tareas no completadas vuelven al Product Backlog

---

## 🎨 INTEGRACIÓN CON VISTAS

### Dashboard (`dashboard.blade.php`)
- Selector de sprint activo
- Tablero Kanban con 5 columnas (fases)
- Métricas: Total story points, completados, tasa de completitud
- Burndown chart con línea ideal vs actual
- Filtrado de tareas por sprint

### Sprint Planning (`sprint-planning.blade.php`)
- Product Backlog completo
- Formulario para crear nuevo sprint
- Formulario para crear user stories
- Drag & drop para mover user stories al sprint
- Estimación de story points

### Daily Scrum (`daily-scrum.blade.php`)
- Vista por miembro del equipo
- Formulario para registrar daily scrum
- Histórico de impedimentos
- Tareas asignadas por persona

### Sprint Review (`sprint-review.blade.php`)
- Métricas del sprint completado
- Lista de user stories completadas
- Burndown chart final
- Comparación velocidad estimada vs real

### Sprint Retrospective (`sprint-retrospective.blade.php`)
- Formato Estrella de Mar (Start/Stop/Continue/More/Less)
- Reflexiones del equipo
- Plan de mejora para próximo sprint

---

## 🚀 EJEMPLO DE USO COMPLETO

```bash
# 1. Crear nuevo sprint
curl -X POST http://localhost:8000/proyectos/{id}/scrum/sprints \
  -H "Content-Type: application/json" \
  -d '{"nombre": "Sprint 4", "fecha_inicio": "2025-11-15", "fecha_fin": "2025-11-29"}'

# 2. Crear user story en el sprint
curl -X POST http://localhost:8000/proyectos/{id}/scrum/user-stories \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "US-015: Reset de contraseña",
    "id_sprint": 4,
    "id_fase": 2,
    "story_points": 5,
    "prioridad": 1
  }'

# 3. Iniciar el sprint
curl -X POST http://localhost:8000/proyectos/{id}/scrum/sprints/4/iniciar

# 4. Actualizar estado de user story
curl -X PATCH http://localhost:8000/proyectos/{id}/scrum/user-stories/123 \
  -H "Content-Type: application/json" \
  -d '{"estado": "In Progress", "id_fase": 3}'

# 5. Completar el sprint
curl -X POST http://localhost:8000/proyectos/{id}/scrum/sprints/4/completar
```

---

## 📝 NOTAS IMPORTANTES

1. **Sprints y User Stories**: Las user stories se vinculan a sprints mediante `id_sprint` (FK). El campo `sprint` (string) se mantiene por compatibilidad pero ya no se usa.

2. **Estados de Sprint**:
   - `planificado`: Sprint creado pero no iniciado
   - `activo`: Sprint en ejecución (solo 1 a la vez)
   - `completado`: Sprint finalizado
   - `cancelado`: Sprint cancelado (no usado actualmente)

3. **Fases de Scrum** (configurables en BD):
   1. Product Backlog
   2. Sprint Backlog (To Do)
   3. Doing (In Progress)
   4. Review (In Review)
   5. Done (Completed)

4. **Story Points**: Sistema de estimación relativa (1-100). Recomendado: secuencia Fibonacci (1, 2, 3, 5, 8, 13, 21, etc.)

5. **Velocidad del equipo**: Se calcula automáticamente al completar sprint. Útil para estimar futuros sprints.

---

## ✅ SISTEMA COMPLETAMENTE FUNCIONAL

Todas las funcionalidades están implementadas y listas para usar. El sistema integra perfectamente Scrum con SGCS (gestión de configuración).
