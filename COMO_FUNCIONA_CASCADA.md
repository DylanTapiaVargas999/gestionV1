# 📘 CÓMO FUNCIONA LA GESTIÓN CASCADA (WATERFALL)

## 🌊 Metodología Cascada - Visión General

La metodología Cascada es un modelo de desarrollo secuencial donde cada fase debe completarse antes de pasar a la siguiente. Es como una cascada de agua que fluye hacia abajo - no puedes retroceder.

## 🎯 Fases del Proyecto Cascada

### 1. **Requisitos**
- **Objetivo**: Definir QUÉ se va a construir
- **Actividades**: Documentar requisitos funcionales y no funcionales
- **Entregables**: Documento de requisitos aprobado
- **No se avanza sin**: Requisitos completos y aprobados

### 2. **Análisis**
- **Objetivo**: Definir CÓMO se va a construir
- **Actividades**: Análisis de sistemas, casos de uso, modelos de datos
- **Entregables**: Documento de análisis y diseño de alto nivel
- **No se avanza sin**: Análisis completo validado

### 3. **Diseño**
- **Objetivo**: Crear la arquitectura y diseño detallado
- **Actividades**: Diseño de base de datos, interfaces, arquitectura
- **Entregables**: Diagramas UML, especificaciones técnicas
- **No se avanza sin**: Diseño aprobado por arquitecto

### 4. **Implementación**
- **Objetivo**: Construir el sistema
- **Actividades**: Codificación, desarrollo, integración
- **Entregables**: Código fuente, commits de GitHub, builds
- **No se avanza sin**: Código completado y compilado

### 5. **Pruebas**
- **Objetivo**: Verificar calidad
- **Actividades**: Unit tests, integration tests, UAT
- **Entregables**: Reportes de pruebas, bugs corregidos
- **No se avanza sin**: Todas las pruebas pasadas

### 6. **Despliegue**
- **Objetivo**: Poner en producción
- **Actividades**: Instalación, configuración, capacitación
- **Entregables**: Sistema en producción operativo
- **No se avanza sin**: Despliegue exitoso validado

### 7. **Mantenimiento** (opcional)
- **Objetivo**: Soporte post-producción
- **Actividades**: Corrección de bugs, mejoras menores
- **Entregables**: Actualizaciones, parches
- **Continuo**: Esta fase es permanente

---

## 🔄 FLUJO DE TRABAJO COMPLETO

### A. Creación de Tareas en Cascada

#### Opción 1: Manual (Líder del Proyecto)
```
1. Click en "Nueva Actividad"
2. Completar formulario:
   - Nombre de la tarea
   - Descripción
   - Fase (seleccionar una de las 7 fases)
   - Elemento de Configuración (opcional)
   - Responsable (miembro del equipo)
   - Fechas inicio/fin
   - Horas estimadas
   - Prioridad (1-10)
   - Entregable esperado
3. Guardar
```

#### Opción 2: Automática (CCB aprueba cambio)
```
Cuando el CCB aprueba una Solicitud de Cambio:
→ Job "ImplementarSolicitudAprobadaJob" se ejecuta automáticamente
→ Crea versión PENDIENTE del Elemento de Configuración (EC)
→ Genera tarea automática en fase "Implementación"
   - Nombre: "Implementar cambio: [nombre del EC]"
   - Estado: "Pendiente"
   - Sin responsable asignado (el líder lo asigna después)
   - Con horas estimadas según prioridad:
     * CRÍTICA: 40 horas
     * ALTA: 24 horas
     * MEDIA: 16 horas
     * BAJA: 8 horas
   - Con fechas calculadas automáticamente
```

### B. Tablero Kanban por Fase

Cada fase tiene su propio tablero Kanban con 4 columnas:

```
┌─────────────┬──────────────┬──────────────┬─────────────┐
│  PENDIENTE  │ EN PROGRESO  │ EN REVISIÓN  │ COMPLETADA  │
├─────────────┼──────────────┼──────────────┼─────────────┤
│   [Tarea]   │   [Tarea]    │   [Tarea]    │  [Tarea]    │
│   [Tarea]   │   [Tarea]    │              │  [Tarea]    │
│   [Tarea]   │              │              │  [Tarea]    │
└─────────────┴──────────────┴──────────────┴─────────────┘
```

**Drag & Drop**: Arrastra tareas entre columnas para cambiar su estado

### C. Completar una Tarea (IMPORTANTE ⚠️)

#### Paso 1: Mover a "Completada"
```
Usuario arrastra la tarea → Columna "COMPLETADA"
```

#### Paso 2: Modal de Commit Aparece
```
┌──────────────────────────────────────────┐
│ 🔗 Commit Requerido                      │
├──────────────────────────────────────────┤
│ Para marcar como completada, ingresa     │
│ el enlace del commit de GitHub:          │
│                                          │
│ [ https://github.com/user/repo/commit/.. ]│
│                                          │
│        [Cancelar]  [Confirmar]           │
└──────────────────────────────────────────┘
```

#### Paso 3: Sistema Procesa Automáticamente
```javascript
1. Valida URL de GitHub ✓
2. Extrae información del commit ✓
3. CREA o ACTUALIZA Elemento de Configuración (EC):
   - Si la tarea NO tiene EC → Crea uno nuevo
     * Código: [PROYECTO]-EC-001, 002, etc.
     * Título: Nombre de la tarea
     * Tipo: CODIGO
     * Estado: APROBADO
   
   - Si la tarea YA tiene EC → Lo actualiza
     * Mantiene el mismo EC
     * Actualiza estado a APROBADO

4. CREA NUEVA VERSIÓN del EC ✓✓✓
   - Versión automática:
     * Primera vez: v1.0.0
     * Subsiguientes: v1.1.0, v1.2.0, etc.
   
   - Registro de cambios:
     "Tarea completada: [nombre tarea]
      Commit: [URL del commit]"
   
   - Estado: APROBADO
   - Aprobado por: Usuario que completó la tarea
   - Aprobado en: Fecha/hora actual

5. REGISTRA COMMIT en base de datos ✓
   - URL del repositorio
   - Hash del commit
   - Vinculado al EC
   - Autor (si GitHub lo provee)
   - Mensaje del commit
   - Fecha del commit

6. Actualiza estado de la tarea a "Completada"
```

#### Paso 4: Confirmación
```
Alert aparece con mensaje:
"Tarea completada exitosamente. 
 EC '[CODIGO-EC]' actualizado a versión 1.2.0."

→ Página recarga
→ Tarea aparece en columna "COMPLETADA"
→ Métricas se actualizan automáticamente
```

---

## 📊 Dashboard Cascada - Pestañas

### Pestaña 1: PROGRESO POR FASES
**Muestra**:
- Cronología del proyecto (Inicio → Hoy → Fin)
- Lista vertical de las 7 fases
- Barra de progreso para cada fase
- Colores según estado:
  * Verde: Fase completada 100%
  * Azul: Fase en progreso
  * Gris: Fase pendiente

**Ejemplo Visual**:
```
1. ✅ Requisitos          [████████████████] 100%
2. 🔵 Análisis            [████████░░░░░░░░]  50%
3. ⚪ Diseño              [░░░░░░░░░░░░░░░░]   0%
4. ⚪ Implementación      [░░░░░░░░░░░░░░░░]   0%
5. ⚪ Pruebas             [░░░░░░░░░░░░░░░░]   0%
6. ⚪ Despliegue          [░░░░░░░░░░░░░░░░]   0%
7. ⚪ Mantenimiento       [░░░░░░░░░░░░░░░░]   0%
```

### Pestaña 2: CRONOGRAMA MAESTRO
**Muestra**:
- Lista de TODAS las tareas del proyecto
- Agrupadas por fase
- Información por tarea:
  * Nombre
  * Responsable
  * Fechas inicio/fin
  * Prioridad (P1-P10)
  * Estado (badge con color)
  * Elemento de Configuración (si tiene)

### Pestaña 3: DIAGRAMA DE GANTT
**Muestra**:
- Línea de tiempo visual
- Barras horizontales por cada tarea
- Colores según fase
- Fechas en el eje X
- Tareas en el eje Y
- Permite ver:
  * Dependencias temporales
  * Overlaps
  * Ruta crítica
  * Duración total del proyecto

---

## 🎯 Características Especiales de Cascada

### 1. **Fases Secuenciales Estrictas**
```
❌ NO PERMITIDO: Trabajar en Implementación antes de terminar Diseño
✅ PERMITIDO: Solo avanzar a siguiente fase cuando actual está 100%
```

### 2. **Control de Versiones Automático**
```
Cada tarea completada → Nueva versión del EC
Historial completo de cambios
Trazabilidad total: Tarea → Commit → Versión
```

### 3. **Sin Sprints**
```
A diferencia de Scrum:
- NO hay sprints de 2 semanas
- NO hay daily standups
- NO hay retrospectivas
- SÍ hay fases largas y secuenciales
```

### 4. **Elementos de Configuración (EC)**
```
Cada tarea puede (o debe) estar vinculada a un EC:
- EC = Artefacto/entregable del proyecto
- Ejemplos:
  * "Sistema de Login" (tipo: CODIGO)
  * "Base de Datos Usuarios" (tipo: DATOS)
  * "Manual de Usuario" (tipo: DOCUMENTO)
  * "API REST" (tipo: CODIGO)
```

### 5. **Cronograma Inteligente** ⚡
```
Cuando se aprueba un cambio crítico por el CCB:
→ Sistema analiza impacto en cronograma
→ Detecta:
  * Desviaciones temporales
  * Sobrecarga de recursos
  * Conflictos de fechas
→ Propone ajustes automáticos
→ Si es CRÍTICO: Aplica ajustes inmediatamente
→ Si es MEDIA/BAJA: Requiere aprobación manual
```

---

## 🔒 Gestión de Configuración (CCB)

### Flujo de Solicitudes de Cambio

```
1. Usuario crea Solicitud de Cambio
   ↓
2. Solicitud va a estado "PENDIENTE"
   ↓
3. Usuario envía a CCB (Comité de Control de Cambios)
   ↓
4. Estado cambia a "EN_CCB"
   ↓
5. Miembros del CCB votan (APROBAR / RECHAZAR)
   ↓
6a. Si MAYORÍA APRUEBA:
    → Estado: "APROBADA"
    → Job automático se ejecuta:
      * Crea versión PENDIENTE del EC
      * Genera tarea en fase "Implementación"
      * Analiza impacto en cronograma
    → Estado final: "IMPLEMENTADA"
   ↓
6b. Si MAYORÍA RECHAZA:
    → Estado: "RECHAZADA"
    → Solicitud se cierra sin cambios
```

### Job Automático: ImplementarSolicitudAprobadaJob

**Qué hace**:
1. ✅ Crea versión PENDIENTE del EC (no aprobada aún)
2. ✅ Genera tarea automática en Cascada/Scrum
3. ✅ Analiza impacto en cronograma
4. ✅ Propone ajustes si es necesario
5. ✅ Marca solicitud como IMPLEMENTADA

**Diferencias Scrum vs Cascada**:

| Aspecto | Scrum | Cascada |
|---------|-------|---------|
| Fase destino | Product Backlog | Implementación |
| Estado tarea | "To Do" | "Pendiente" |
| Asignación | Null (se asigna en Sprint Planning) | Null (líder asigna después) |
| Estimación | Story Points (1, 3, 5, 8) | Horas (8, 16, 24, 40) |
| Duración | No aplica | 1-5 días según prioridad |

---

## 📈 Métricas del Proyecto

### Métricas Generales (4 cards en dashboard)
```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ FASE ACTUAL  │   PROGRESO   │   DURACIÓN   │    TAREAS    │
│ Requisitos   │     25%      │   128 días   │      38      │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

### Métricas por Fase (5 cards en vista de fase)
```
┌────────┬────────────┬──────────┬───────┬──────────┐
│  TOTAL │ COMPLETADAS│ PROGRESO │ HORAS │ PROGRESO │
│   4    │     0      │    0     │  28   │    0%    │
└────────┴────────────┴──────────┴───────┴──────────┘
```

---

## 🛠️ RESUMEN TÉCNICO

### Backend (Laravel)
```php
// Controlador: CascadaController.php
- dashboard(): Vista principal con pestañas
- verFase(): Vista detallada de una fase
- Filtra tareas case-insensitive
- Calcula métricas automáticamente

// Job: ImplementarSolicitudAprobadaJob.php
- handle(): Ejecuta flujo completo
- crearVersionesEC(): Crea versión PENDIENTE
- crearTareasCascada(): Genera tarea automática
- analizarImpactoCronograma(): Verifica impacto
```

### Frontend (Blade + JavaScript)
```javascript
// Drag & Drop
- allowDrop(ev): Permite soltar
- drag(ev): Inicia arrastre
- drop(ev, estado): Procesa soltar

// Modal de Commit
- confirmarCommit(): Valida URL GitHub
- actualizarEstadoTarea(): AJAX call al backend
- Muestra mensaje con versión creada
```

### Base de Datos
```sql
-- Tablas principales
tareas_proyecto: Almacena tareas
  - id_fase: FK a fases_metodologia
  - id_ec: FK a elementos_configuracion (opcional)
  - estado: 'Pendiente', 'En Progreso', 'En Revisión', 'Completada'
  - commit_url: URL del commit de GitHub

elementos_configuracion: ECs del proyecto
  - version_actual_id: FK a versiones_ec
  - estado: 'PENDIENTE', 'EN_REVISION', 'APROBADO'

versiones_ec: Historial de versiones
  - version: '1.0.0', '1.1.0', etc.
  - registro_cambios: Log de qué cambió
  - aprobado_por, aprobado_en

commits_repositorio: Commits de GitHub
  - hash_commit: SHA del commit
  - ec_id: FK al EC
  - mensaje, autor, fecha_commit
```

---

## ⚠️ BUGS CORREGIDOS

### Bug #1: Tareas desaparecen en "En Progreso"
**Problema**: Al mover tarea a "En Progreso", desaparecía del tablero
**Causa**: Filtro case-sensitive en vista (`where('estado', 'En Progreso')`)
**Solución**: 
```php
// ANTES (❌)
$tareas->where('estado', 'En Progreso')

// DESPUÉS (✅)
$tareas->filter(function($t) {
    return in_array(strtolower(trim($t->estado)), 
        ['en progreso', 'en_progreso', 'in progress']);
})
```

---

## ✅ CHECKLIST DE FUNCIONALIDADES

- [x] Dashboard con 3 pestañas (Progreso/Cronograma/Gantt)
- [x] Tablero Kanban por fase con drag & drop
- [x] Modal de commit al completar tarea
- [x] Creación automática de versión del EC
- [x] Registro de commit en BD
- [x] Job automático del CCB
- [x] Filtros case-insensitive en estados
- [x] Diseño minimalista sin emojis
- [x] Métricas actualizadas en tiempo real
- [x] Trazabilidad completa (Tarea→Commit→Versión)

---

**Fecha de documentación**: 13 de noviembre de 2025
**Sistema**: SGCS - Sistema de Gestión de Configuración de Software
**Versión**: 1.0
