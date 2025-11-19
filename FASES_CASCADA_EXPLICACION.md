# 🌊 CÓMO FUNCIONAN LAS FASES EN CASCADA

## ❓ TUS DUDAS RESPONDIDAS

### 1. **¿Cada fase tiene un tiempo definido o es automático?**

**RESPUESTA**: Las fases **NO tienen tiempo definido** en la tabla de base de datos. El tiempo se calcula **automáticamente** basado en las tareas.

#### ¿Cómo se calcula el tiempo de cada fase?

```
TIEMPO DE FASE = Fecha más temprana de inicio - Fecha más tardía de fin
```

**Ejemplo**:
```
Fase: Requisitos
  - Tarea 1: 01/01/2025 - 05/01/2025 (5 días)
  - Tarea 2: 03/01/2025 - 10/01/2025 (8 días)
  - Tarea 3: 06/01/2025 - 12/01/2025 (7 días)

→ Tiempo total de la fase: 01/01/2025 - 12/01/2025 = 12 días
```

#### ¿Dónde se define el tiempo?

**EN LAS TAREAS**, no en las fases:

```sql
-- Tabla: tareas_proyecto
fecha_inicio: '2025-01-01'
fecha_fin: '2025-01-10'
horas_estimadas: 40
duracion_estimada: 5 (días)
```

**Las fases NO tienen estos campos**:
```sql
-- Tabla: fases_metodologia
id_fase (PK)
id_metodologia
nombre_fase: 'Requisitos', 'Análisis', etc.
orden: 1, 2, 3, 4...
descripcion: 'Definir requisitos del sistema'

❌ NO TIENE: fecha_inicio
❌ NO TIENE: fecha_fin
❌ NO TIENE: duracion
```

---

### 2. **¿Las fases se cierran o cómo funciona eso?**

**RESPUESTA**: Las fases **NO se cierran manualmente**. Se consideran "completadas" automáticamente cuando:

```
FASE COMPLETADA = Cuando TODAS sus tareas están en estado "Completada"
```

#### Sistema de Estados de Fase

**3 Estados Posibles** (calculados automáticamente):

1. **⚪ PENDIENTE** (No iniciada)
   ```
   - 0 tareas completadas
   - Ninguna tarea en progreso
   - Todas las tareas en estado "Pendiente" o "To Do"
   ```

2. **🔵 EN PROGRESO** (Actual)
   ```
   - Al menos 1 tarea completada O en progreso
   - Aún tiene tareas pendientes
   - NO está 100% completada
   ```

3. **✅ COMPLETADA** (Terminada)
   ```
   - TODAS las tareas (100%) en estado "Completada"
   - No quedan tareas pendientes
   - Fase cerrada automáticamente
   ```

#### Código que Calcula el Estado

En `CascadaController.php`:

```php
// Calcular progreso por fase
$progresoPorFase = [];
$estadosCompletados = ['Done', 'Completado', 'Completada', 'DONE'];

foreach ($fases as $fase) {
    $tareasDelaFase = $tareasPorFase->get($fase->id_fase, collect());
    $totalTareas = $tareasDelaFase->count();
    $tareasCompletadas = $tareasDelaFase->whereIn('estado', $estadosCompletados)->count();

    $progresoPorFase[$fase->id_fase] = [
        'total' => $totalTareas,
        'completadas' => $tareasCompletadas,
        'porcentaje' => $totalTareas > 0 
            ? round(($tareasCompletadas / $totalTareas) * 100) 
            : 0,
        'fase_completada' => $totalTareas > 0 && $tareasCompletadas === $totalTareas
    ];
}
```

---

### 3. **¿Se debe configurar el tiempo de cada fase manualmente?**

**NO**. El sistema lo calcula automáticamente siguiendo este flujo:

```
PASO 1: Crear tareas con fechas
  → Usuario crea tarea en fase "Análisis"
  → Define: fecha_inicio = 15/01/2025
  → Define: fecha_fin = 20/01/2025

PASO 2: Sistema calcula automáticamente
  → Agrupa todas las tareas de "Análisis"
  → Busca la fecha_inicio más temprana
  → Busca la fecha_fin más tardía
  → Calcula duración total de la fase

PASO 3: Actualiza indicadores
  → Duración total: XX días
  → Progreso: XX%
  → Estado: Pendiente/En Progreso/Completada
```

---

## 📊 INDICADORES DEL DASHBOARD

### 4 Métricas Principales (Cards Superiores)

#### 1. **FASE ACTUAL**
```
- Muestra: Nombre de la primera fase incompleta
- Cálculo: Primera fase con porcentaje < 100%
- Actualización: Automática al completar tareas
```

**Ejemplo**:
```
FASE ACTUAL
Análisis

→ Si completas todas las tareas de "Análisis"
→ Cambia automáticamente a "Diseño"
```

#### 2. **PROGRESO GENERAL**
```
- Muestra: Porcentaje del proyecto completo
- Cálculo: (Fases completadas / Total fases) × 100
- Ejemplo: 2 fases completadas de 7 = 28%
```

**Fórmula**:
```javascript
progresoGeneral = (fasesCompletadas / totalFases) * 100

// Ejemplo:
// 7 fases totales
// 2 fases con 100% completadas
// Progreso = (2 / 7) * 100 = 28%
```

#### 3. **DURACIÓN TOTAL**
```
- Muestra: Días totales del proyecto
- Cálculo: fecha_fin_ultima_tarea - fecha_inicio_primera_tarea
- Actualización: Automática al crear/editar tareas
```

**Ejemplo**:
```
Primera tarea: 01/01/2025
Última tarea: 31/03/2025
Duración Total: 90 días
```

#### 4. **HITOS**
```
- Muestra: Cantidad de hitos del proyecto
- Hitos = Inicio y fin de cada fase
- Ejemplo: 7 fases = 14 hitos (7 inicios + 7 fines)
```

**Qué es un hito**:
```
HITO = Evento importante en el cronograma

Tipos de hitos:
1. Inicio de fase: "Comenzar Análisis"
2. Fin de fase: "Completar Requisitos"
3. Entregables: "Aprobar Documento de Diseño"
```

---

## 🎯 PROGRESO POR FASES (Vista Principal)

### Indicador Visual de Cada Fase

```
┌────────────────────────────────────────────────┐
│ ✓  1. Requisitos                     [✅ 100%]│
│    Definir requisitos funcionales              │
│    ████████████████████████████████  100%      │
│    5/5 actividades                             │
│                                                │
│ 2  2. Análisis                       [🔵 50%] │
│    Análisis de sistemas                        │
│    ████████████░░░░░░░░░░░░░░░░░░░░   50%     │
│    3/6 actividades      Ver detalles →         │
│                                                │
│ 3  3. Diseño                         [⚪ 0%]  │
│    Arquitectura y diseño detallado             │
│    ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░    0%     │
│    0/8 actividades      Ver detalles →         │
└────────────────────────────────────────────────┘
```

### Elementos de Cada Fase

1. **Icono de Estado**:
   - ✓ (Verde) = Completada 100%
   - Número (Azul) = En progreso
   - Número (Gris) = Pendiente

2. **Nombre de la Fase**: Requisitos, Análisis, Diseño, etc.

3. **Badge de Estado**:
   - "Completada" (Verde) = 100%
   - "En Progreso" (Azul) = Entre 1% y 99%
   - Sin badge = 0%

4. **Descripción**: Texto breve de la fase

5. **Barra de Progreso**:
   - Verde = Completada
   - Azul = En progreso
   - Gris = Pendiente

6. **Contador de Actividades**: "3/6 actividades"

7. **Link "Ver detalles"**: Solo si la fase NO está completada

---

## ⏱️ CÁLCULO AUTOMÁTICO DE TIEMPOS

### Cronograma Maestro (Pestaña 2)

Muestra **todas las tareas** con:

```
TAREA                  | RESPONSABLE | INICIO     | FIN        | PRIORIDAD | ESTADO
────────────────────────────────────────────────────────────────────────────────
Definir requisitos     | Juan Pérez  | 01/01/2025 | 05/01/2025 | P8        | Completada
Analizar casos de uso  | María López | 06/01/2025 | 12/01/2025 | P7        | En Progreso
Diseñar base de datos  | Carlos Ruiz | 13/01/2025 | 20/01/2025 | P9        | Pendiente
```

**Los tiempos se definen al crear cada tarea**:

```
1. Usuario click "Nueva Actividad"
2. Completa formulario:
   - Nombre: "Definir requisitos del sistema"
   - Fase: [Selecciona "Requisitos"]
   - Fecha inicio: 01/01/2025
   - Fecha fin: 05/01/2025
   - Horas estimadas: 40
3. Sistema calcula automáticamente:
   - Duración: 5 días (01 al 05)
   - Actualiza duración de la fase "Requisitos"
   - Actualiza duración total del proyecto
```

---

## 📈 DIAGRAMA DE GANTT (Pestaña 3)

### Vista de Línea de Tiempo

```
          ENE           FEB           MAR
        |─────────────|─────────────|─────────|
Requisitos  █████████                        
Análisis              ████████                
Diseño                          ███████████  
Implementación                              ████████
```

**Cálculo de Posición**:

```php
$diasDesdeInicio = $fechaInicioProyecto->diffInDays($tarea->fecha_inicio);
$duracionTarea = $tarea->fecha_inicio->diffInDays($tarea->fecha_fin) + 1;

$porcentajeInicio = ($diasDesdeInicio / $rangoTotalDias) * 100;
$porcentajeDuracion = ($duracionTarea / $rangoTotalDias) * 100;
```

**Ejemplo**:
```
Proyecto: 01/01/2025 - 31/03/2025 (90 días)
Tarea "Requisitos": 01/01/2025 - 10/01/2025 (10 días)

Posición en Gantt:
- Inicio: día 0 → 0% desde el inicio
- Duración: 10 días → (10/90)*100 = 11.1% del ancho total
- Barra verde de 11.1% de ancho, comenzando en 0%
```

---

## 🔄 ACTUALIZACIÓN AUTOMÁTICA DE INDICADORES

### ¿Cuándo se actualizan los indicadores?

**SIEMPRE que cambias el estado de una tarea**:

```
1. Usuario arrastra tarea de "En Progreso" → "Completada"
   ↓
2. Sistema actualiza estado de la tarea
   ↓
3. Recalcula automáticamente:
   - Tareas completadas de la fase: +1
   - Progreso de la fase: (4/6) = 66%
   - Si llega a 100% → Fase completada = true
   ↓
4. Recalcula indicadores del dashboard:
   - Progreso general: (fases completadas / total)
   - Fase actual: Primera incompleta
   - Duración total: Min/Max de todas las fechas
   ↓
5. Página recarga mostrando nuevos valores
```

---

## ✅ RESUMEN DE RESPUESTAS

| PREGUNTA | RESPUESTA |
|----------|-----------|
| **¿Las fases tienen tiempo definido?** | ❌ NO. El tiempo se calcula automáticamente sumando las fechas de las tareas de cada fase. |
| **¿Se debe configurar manualmente?** | ❌ NO. Solo creas las tareas con fechas, el sistema calcula todo automáticamente. |
| **¿Las fases se cierran?** | ✅ SÍ, automáticamente cuando TODAS las tareas están completadas (100%). No hay botón "Cerrar fase". |
| **¿Cómo avanzo a la siguiente fase?** | Completa todas las tareas de la fase actual. Automáticamente pasa a la siguiente. |
| **¿Puedo trabajar en varias fases a la vez?** | ⚠️ SÍ, técnicamente puedes crear tareas en cualquier fase, pero la metodología Cascada recomienda terminar una antes de empezar la siguiente. |

---

## 🛠️ CONFIGURACIÓN INICIAL DE UN PROYECTO CASCADA

### Paso a Paso para Definir Tiempos

```
PASO 1: Crear el proyecto
  → Define: Nombre, descripción, metodología = Cascada
  → Define: Fecha inicio general (ej: 01/01/2025)
  → Define: Fecha fin general (ej: 31/12/2025)

PASO 2: Las 7 fases ya están creadas automáticamente
  → No necesitas crearlas
  → Ya vienen con orden y descripción
  → 1. Requisitos
  → 2. Análisis
  → 3. Diseño
  → 4. Implementación
  → 5. Pruebas
  → 6. Despliegue
  → 7. Mantenimiento

PASO 3: Crear tareas en cada fase
  → Fase "Requisitos":
    - Tarea 1: 01/01/25 - 10/01/25 (10 días)
    - Tarea 2: 11/01/25 - 15/01/25 (5 días)
    → Sistema calcula: Fase Requisitos = 15 días

  → Fase "Análisis":
    - Tarea 1: 16/01/25 - 30/01/25 (15 días)
    - Tarea 2: 20/01/25 - 05/02/25 (17 días)
    → Sistema calcula: Fase Análisis = 21 días

PASO 4: Indicadores se actualizan solos
  → Duración Total = 01/01/25 - 05/02/25 = 36 días
  → Progreso General = 0% (ninguna fase completada)
  → Fase Actual = "Requisitos"
  → Hitos = 4 (inicio/fin de 2 fases con tareas)

PASO 5: Trabaja en las tareas
  → Completa tareas de "Requisitos" una por una
  → Progreso de fase sube: 0% → 50% → 100%
  → Al llegar a 100%:
    ✓ Fase "Requisitos" marcada como completada
    ✓ "Fase Actual" cambia a "Análisis"
    ✓ Progreso General sube a 14% (1/7 fases)
```

---

## 🎓 EJEMPLO COMPLETO

### Proyecto: Sistema de Ventas

```
PROYECTO: Sistema de Ventas Online
METODOLOGÍA: Cascada
INICIO: 01/01/2025
FIN: 31/07/2025
DURACIÓN: 212 días (7 meses)
```

### Distribución de Fases:

```
1. REQUISITOS (30 días)
   - Inicio: 01/01/2025
   - Fin: 30/01/2025
   - Tareas: 5
   - Estado: Completada ✅
   - Progreso: 100%

2. ANÁLISIS (21 días)
   - Inicio: 31/01/2025
   - Fin: 20/02/2025
   - Tareas: 6
   - Estado: En Progreso 🔵
   - Progreso: 50% (3/6 completadas)

3. DISEÑO (40 días)
   - Inicio: 21/02/2025
   - Fin: 31/03/2025
   - Tareas: 8
   - Estado: Pendiente ⚪
   - Progreso: 0%

4. IMPLEMENTACIÓN (60 días)
   - Inicio: 01/04/2025
   - Fin: 30/05/2025
   - Tareas: 15
   - Estado: Pendiente ⚪
   - Progreso: 0%

5. PRUEBAS (30 días)
   - Inicio: 01/06/2025
   - Fin: 30/06/2025
   - Tareas: 10
   - Estado: Pendiente ⚪
   - Progreso: 0%

6. DESPLIEGUE (15 días)
   - Inicio: 01/07/2025
   - Fin: 15/07/2025
   - Tareas: 4
   - Estado: Pendiente ⚪
   - Progreso: 0%

7. MANTENIMIENTO (16 días)
   - Inicio: 16/07/2025
   - Fin: 31/07/2025
   - Tareas: 3
   - Estado: Pendiente ⚪
   - Progreso: 0%
```

### Indicadores del Dashboard:

```
FASE ACTUAL: Análisis
PROGRESO GENERAL: 14% (1 fase completada de 7)
DURACIÓN TOTAL: 212 días
HITOS: 14 (7 inicios + 7 fines)
```

---

## 🚨 NOTAS IMPORTANTES

1. **NO hay tabla separada de "tiempos de fase"** - Todo se calcula desde las tareas
2. **NO se cierran manualmente** - El sistema lo hace al llegar a 100%
3. **SÍ puedes editar fechas** - Si cambias fechas de tareas, los tiempos de fase se recalculan automáticamente
4. **SÍ se puede extender una fase** - Agrega más tareas o extiende fechas de tareas existentes
5. **SÍ afecta al proyecto** - Si extiendes una fase, la "Duración Total" del proyecto aumenta

---

**Fecha**: 13 de noviembre de 2025
**Sistema**: SGCS - Gestión de Proyectos Cascada
**Autor**: Sistema Automático de Cálculo de Métricas
