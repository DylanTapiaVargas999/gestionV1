# 📚 ARQUITECTURA SCRUM EN SGCS

## 🎯 ¿Qué es una User Story (Historia de Usuario)?

En Scrum, **NO trabajamos con "tareas técnicas"**, trabajamos con **User Stories**.

### ✅ User Story (Historia de Usuario)
```
Como [tipo de usuario]
Quiero [funcionalidad]
Para [beneficio]
```

**Ejemplo:**
- "Como cliente, quiero poder registrarme para acceder al sistema"
- "Como administrador, quiero gestionar órdenes para controlar las ventas"

### 📊 En tu sistema SGCS:

```
tabla: tareas_proyecto
├─ nombre: "US-006: Integrar pasarela de pagos Stripe"
├─ descripcion: "Como cliente, quiero pagar con tarjeta para..."
├─ story_points: 8 ← Complejidad (1, 2, 3, 5, 8, 13, 21)
├─ id_sprint: 3 ← A qué sprint pertenece
├─ id_ec: ECOM-PAY-001 ← Qué código/documento modifica
├─ responsable: Diego Morales ← Quién la implementa
├─ estado: In Progress / In Review / Done
└─ prioridad: 1-10
```

## 🔗 RELACIONES EN SCRUM

### 1️⃣ Proyecto → Sprints
```sql
SELECT * FROM sprints WHERE id_proyecto = 'ECOM-2024';
```
**Resultado:**
- Sprint 1 (completado, 26 pts)
- Sprint 2 (completado, 18 pts)
- Sprint 3 (activo, 39 pts)

### 2️⃣ Sprint → User Stories
```sql
SELECT * FROM tareas_proyecto 
WHERE id_sprint = 3;  -- Sprint 3
```
**Resultado:**
- US-006: Integrar pagos Stripe (8 pts)
- US-007: Dashboard analytics (13 pts)
- US-008: Gestión de órdenes (13 pts)
- US-009: Filtros de productos (5 pts)
**Total: 39 story points**

### 3️⃣ User Story → Elemento de Configuración
```sql
SELECT t.nombre, ec.codigo_ec 
FROM tareas_proyecto t
JOIN elementos_configuracion ec ON t.id_ec = ec.id
WHERE t.id_tarea = [USER_STORY_ID];
```
**Resultado:**
- US-006 → ECOM-PAY-001 (Integración de Pagos)
- US-007 → ECOM-ANALYTICS-001 (Dashboard)

**¿Por qué?** Cada User Story modifica código/documentos específicos

### 4️⃣ User Story → Desarrollador
```sql
SELECT t.nombre, u.nombre_completo 
FROM tareas_proyecto t
JOIN usuarios u ON t.responsable = u.id
WHERE t.id_sprint = 3;
```
**Resultado:**
- US-006 → Diego Morales
- US-007 → Luis Hernández
- US-008 → Carmen Ruiz

## 🎨 FLUJO DE TRABAJO SCRUM

```
1. PRODUCT BACKLOG
   ├── US-010: Wishlist (sin sprint)
   ├── US-011: Reviews (sin sprint)
   └── US-012: Notificaciones (sin sprint)
   ↓
2. SPRINT PLANNING
   - Product Owner prioriza
   - Equipo estima story points
   - Se asignan a Sprint 4
   ↓
3. SPRINT BOARD (Dashboard)
   Sprint 4 ← ACTIVO
   ├── Product Backlog (fila 1)
   ├── Sprint Planning (fila 2)
   ├── In Progress (fila 3) ← Desarrolladores trabajan aquí
   ├── In Review (fila 4) ← QA revisa
   └── Done (fila 5) ← Completado
   ↓
4. DAILY SCRUM
   - "¿Qué hice ayer?"
   - "¿Qué haré hoy?"
   - "¿Tengo impedimentos?"
   ↓
5. SPRINT REVIEW
   - Demostrar funcionalidad completa
   - Burndown Chart muestra progreso
   ↓
6. RETROSPECTIVE
   - ¿Qué salió bien?
   - ¿Qué mejorar?
```

## 📝 ¿Necesitas Subtareas?

### Opción A: Solo User Stories (Actual - Recomendado para tu proyecto)
```
User Story: "Integrar pasarela de pagos"
└── 8 story points
    - Se completa cuando funciona end-to-end
```

**Ventajas:**
- ✅ Simple
- ✅ Enfocado en valor
- ✅ Menos overhead

### Opción B: User Stories + Subtareas (Para equipos grandes)
```
User Story: "Integrar pasarela de pagos" (8 pts)
├── Subtarea 1: Crear formulario de pago (1 pt)
├── Subtarea 2: Integrar API Stripe (3 pts)
├── Subtarea 3: Validar transacción (2 pts)
└── Subtarea 4: Confirmar por email (2 pts)
```

**Ventajas:**
- ✅ Más granularidad
- ✅ Seguimiento detallado

**Desventajas:**
- ❌ Más complejo
- ❌ Más overhead de gestión

## 🎯 RECOMENDACIÓN PARA TU SISTEMA

**MANTÉN LO QUE TIENES:** Solo User Stories (tabla `tareas_proyecto`)

**Razones:**
1. Tu proyecto es mediano (13 usuarios, 2 proyectos)
2. User Stories ya tienen story points y responsables
3. Puedes agregar detalles en `descripcion` y `criterios_aceptacion`
4. Scrum profesional recomienda trabajar a nivel de User Story

## 📊 BURNDOWN CHART

```
Story Points
     40│                 ╱
       │              ╱  
       │           ╱     
     30│        ╱        
       │     ╱           
       │  ╱              ---- Línea Ideal
     20│                 ···· Línea Actual
       │                 
       │                 
     10│                 
       │                 
       │                 
      0└─────────────────
        0  2  4  6  8  10  12  14
              Días del Sprint
```

**Interpretación:**
- Línea azul (ideal): Decremento lineal perfecto
- Línea verde (actual): Progreso real del equipo
- Si actual < ideal: Vamos adelantados ✅
- Si actual > ideal: Vamos atrasados ⚠️

## ✅ CONCLUSIÓN

Tu arquitectura está **CORRECTA**:

```
Proyecto ECOM-2024
└── Sprints
    ├── Sprint 1 (3 user stories, 26 pts) ✅ Completado
    ├── Sprint 2 (2 user stories, 18 pts) ✅ Completado
    └── Sprint 3 (4 user stories, 39 pts) 🔄 Activo
        ├── US-006 → ECOM-PAY-001 → Diego Morales
        ├── US-007 → ECOM-ANALYTICS-001 → Luis Hernández
        ├── US-008 → ECOM-ORD-001 → Carmen Ruiz
        └── US-009 → ECOM-PROD-001 → Diego Morales
```

**No necesitas cambiar nada.** El término "tarea" y "user story" son equivalentes en tu sistema.
