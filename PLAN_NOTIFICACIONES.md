# 📢 PLAN DE IMPLEMENTACIÓN: SISTEMA DE NOTIFICACIONES

**Fecha:** 13 de noviembre de 2025  
**Proyecto:** SGCS (Sistema de Gestión de Configuración de Software)  
**Estado:** Análisis completo y plan de implementación

---

## 🎯 OBJETIVO

Implementar un sistema completo de notificaciones en tiempo real para mantener a los usuarios informados sobre eventos críticos del proyecto.

---

## 📊 ANÁLISIS DEL SISTEMA ACTUAL

### ✅ Infraestructura Existente
- **Laravel Notifications**: Framework ya incluido (trait `Notifiable` en Usuario.php)
- **Tabla notifications**: ❌ **PENDIENTE CREAR**
- **Notificaciones actuales**: Solo email (ResetPassword, VerifyEmail)
- **UI de notificaciones**: ❌ **NO EXISTE**

### 🔍 Hallazgos en el Código
- Comentarios `// TODO: Notificar` encontrados en:
  - `SolicitudCambioController.php` líneas 343, 355
  - Confirmando que el sistema fue diseñado para notificaciones pero no implementado

---

## 🎬 EVENTOS CRÍTICOS QUE REQUIEREN NOTIFICACIONES

### **1️⃣ GESTIÓN DE PROYECTOS**

#### **A. Asignación a Proyecto**
- **Cuándo**: Usuario es agregado como miembro de un equipo
- **Archivo**: `ProyectoController.php::store()` línea ~651
- **Quién notificar**: Miembro nuevo
- **Mensaje**: "Has sido asignado al proyecto {nombre_proyecto} como {rol}"
- **Acción**: Link al proyecto

#### **B. Asignación como Líder**
- **Cuándo**: Usuario es designado líder de equipo/proyecto
- **Archivo**: `ProyectoController.php::store()` línea ~600
- **Quién notificar**: Líder asignado
- **Mensaje**: "Has sido asignado como Líder del proyecto {nombre_proyecto}"
- **Acción**: Link al dashboard del proyecto

#### **C. Miembro Agregado al CCB**
- **Cuándo**: Usuario es añadido como miembro del Comité de Control de Cambios
- **Archivo**: `ProyectoController.php::store()` línea ~644
- **Quién notificar**: Miembro del CCB
- **Mensaje**: "Has sido agregado al CCB del proyecto {nombre_proyecto}"
- **Acción**: Link al dashboard CCB

---

### **2️⃣ GESTIÓN DE CAMBIOS (CCB)**

#### **A. Nueva Solicitud de Cambio**
- **Cuándo**: Se crea una nueva solicitud de cambio
- **Archivo**: `SolicitudCambioController.php::store()` línea ~67
- **Quién notificar**: Todos los miembros del CCB
- **Mensaje**: "{usuario} ha creado una solicitud de cambio: {titulo}"
- **Acción**: Link para votar

#### **B. Solicitud Aprobada**
- **Cuándo**: Solicitud alcanza quorum de aprobación
- **Archivo**: `SolicitudCambioController.php::verificarYProcesarQuorum()` línea ~332
- **Quién notificar**: 
  - Creador de la solicitud
  - Todos los miembros del CCB
- **Mensaje**: "La solicitud de cambio '{titulo}' ha sido APROBADA"
- **Acción**: Link a la solicitud

#### **C. Solicitud Rechazada**
- **Cuándo**: Solicitud alcanza quorum de rechazo
- **Archivo**: `SolicitudCambioController.php::verificarYProcesarQuorum()` línea ~345
- **Quién notificar**: 
  - Creador de la solicitud
  - Todos los miembros del CCB
- **Mensaje**: "La solicitud de cambio '{titulo}' ha sido RECHAZADA"
- **Acción**: Link a la solicitud

#### **D. Voto Pendiente**
- **Cuándo**: Miembro del CCB aún no ha votado (después de 24h)
- **Archivo**: **NUEVO** - Job programado
- **Quién notificar**: Miembro del CCB sin voto
- **Mensaje**: "Tienes una solicitud de cambio pendiente de votación: {titulo}"
- **Acción**: Link para votar

---

### **3️⃣ GESTIÓN DE TAREAS**

#### **A. Tarea Asignada**
- **Cuándo**: Tarea es asignada a un usuario
- **Archivo**: `TareaProyectoController.php::store()` línea ~93
- **Quién notificar**: Responsable asignado
- **Mensaje**: "Se te ha asignado la tarea: {nombre_tarea}"
- **Acción**: Link a la tarea

#### **B. Tarea Reasignada**
- **Cuándo**: Tarea cambia de responsable
- **Archivo**: `TareaProyectoController.php::update()` línea ~169
- **Quién notificar**: 
  - Nuevo responsable
  - Antiguo responsable (informativo)
- **Mensaje**: "La tarea '{nombre}' te ha sido asignada/reasignada"
- **Acción**: Link a la tarea

#### **C. Fecha Límite Próxima**
- **Cuándo**: Falta 1 día para la fecha fin de la tarea
- **Archivo**: **NUEVO** - Job programado diario
- **Quién notificar**: Responsable de la tarea
- **Mensaje**: "⚠️ La tarea '{nombre}' vence mañana"
- **Acción**: Link a la tarea

#### **D. Tarea Atrasada**
- **Cuándo**: Tarea no completada después de fecha_fin
- **Archivo**: **NUEVO** - Job programado diario
- **Quién notificar**: 
  - Responsable
  - Líder del proyecto
- **Mensaje**: "🔴 La tarea '{nombre}' está atrasada"
- **Acción**: Link a la tarea

---

### **4️⃣ GESTIÓN SCRUM**

#### **A. User Story Asignada a Sprint**
- **Cuándo**: User story es agregada a un sprint
- **Archivo**: `ScrumController.php::asignarUserStories()` línea ~484
- **Quién notificar**: 
  - Responsable de la user story (si tiene)
  - Equipo del sprint
- **Mensaje**: "La user story '{titulo}' ha sido asignada al {sprint_nombre}"
- **Acción**: Link al sprint

#### **B. Sprint Iniciado**
- **Cuándo**: Sprint cambia a estado 'activo'
- **Archivo**: `ScrumController.php` línea ~436
- **Quién notificar**: Todo el equipo Scrum
- **Mensaje**: "🏃 El sprint '{nombre}' ha comenzado"
- **Acción**: Link al tablero Scrum

#### **C. Sprint Completado**
- **Cuándo**: Sprint cambia a estado 'completado'
- **Archivo**: `ScrumController.php` línea ~468
- **Quién notificar**: Todo el equipo Scrum
- **Mensaje**: "✅ El sprint '{nombre}' ha finalizado"
- **Acción**: Link a retrospectiva

#### **D. Daily Scrum Pendiente**
- **Cuándo**: No se ha registrado daily scrum hoy
- **Archivo**: **NUEVO** - Job programado cada mañana
- **Quién notificar**: Scrum Master
- **Mensaje**: "Recuerda registrar el Daily Scrum de hoy"
- **Acción**: Link para registrar

---

### **5️⃣ CRONOGRAMA INTELIGENTE**

#### **A. Ajuste de Cronograma Propuesto**
- **Cuándo**: Sistema detecta desviación y propone ajuste
- **Archivo**: `CronogramaInteligenteController.php`
- **Quién notificar**: 
  - Líder del proyecto
  - Miembros del CCB
- **Mensaje**: "⚠️ El sistema ha detectado una desviación y propone un ajuste"
- **Acción**: Link para aprobar/rechazar

#### **B. Ajuste Aprobado**
- **Cuándo**: Líder aprueba ajuste propuesto
- **Archivo**: `CronogramaInteligenteController.php::aprobar()` línea ~130
- **Quién notificar**: 
  - Todos los miembros del proyecto
  - Responsables de tareas afectadas
- **Mensaje**: "📅 Se ha aprobado un ajuste al cronograma del proyecto"
- **Acción**: Link a cronograma actualizado

#### **C. Ajuste Rechazado**
- **Cuándo**: Líder rechaza ajuste propuesto
- **Archivo**: `CronogramaInteligenteController.php::rechazar()` línea ~158
- **Quién notificar**: Equipo de gestión
- **Mensaje**: "El ajuste propuesto fue rechazado"
- **Acción**: Link a análisis

---

### **6️⃣ ELEMENTOS DE CONFIGURACIÓN**

#### **A. Nueva Versión de EC Creada**
- **Cuándo**: Se implementa solicitud aprobada
- **Archivo**: `SolicitudCambioController.php::implementar()`
- **Quién notificar**: 
  - Usuarios con permisos sobre ese EC
  - Creador del EC
- **Mensaje**: "📦 Nueva versión del elemento '{titulo}': v{version}"
- **Acción**: Link al EC

#### **B. EC Requiere Aprobación**
- **Cuándo**: EC en estado PENDIENTE necesita revisión
- **Archivo**: `ElementoConfiguracionController.php::store()` línea ~103
- **Quién notificar**: Miembros del CCB
- **Mensaje**: "El elemento '{titulo}' requiere aprobación"
- **Acción**: Link para aprobar

---

### **7️⃣ LIBERACIONES**

#### **A. Nueva Liberación Creada**
- **Cuándo**: Se crea una nueva liberación
- **Archivo**: `LiberacionesController.php::store()` línea ~54
- **Quién notificar**: 
  - Todo el equipo del proyecto
  - Stakeholders
- **Mensaje**: "🚀 Nueva liberación '{nombre}' v{version} creada"
- **Acción**: Link a la liberación

---

## 🏗️ ARQUITECTURA DE IMPLEMENTACIÓN

### **FASE 1: INFRAESTRUCTURA BASE**

#### 1.1 Crear Tabla de Notificaciones
```bash
php artisan notifications:table
php artisan migrate
```

#### 1.2 Configurar Canales
- **database**: Guardar en BD (principal)
- **mail**: Email (opcional, para críticas)
- **broadcast**: WebSockets (futuro, tiempo real)

---

### **FASE 2: CLASES DE NOTIFICACIONES**

Crear en `app/Notifications/`:

```
app/Notifications/
├── Proyecto/
│   ├── UsuarioAsignadoAProyecto.php
│   ├── UsuarioAsignadoComoLider.php
│   └── MiembroAgregadoACCB.php
├── Cambios/
│   ├── NuevaSolicitudCambio.php
│   ├── SolicitudAprobada.php
│   ├── SolicitudRechazada.php
│   └── VotoPendienteCCB.php
├── Tareas/
│   ├── TareaAsignada.php
│   ├── TareaReasignada.php
│   ├── TareaProximaAVencer.php
│   └── TareaAtrasada.php
├── Scrum/
│   ├── UserStoryAsignadaASprint.php
│   ├── SprintIniciado.php
│   ├── SprintCompletado.php
│   └── DailyScrumPendiente.php
├── Cronograma/
│   ├── AjusteCronogramaPropuesto.php
│   ├── AjusteAprobado.php
│   └── AjusteRechazado.php
├── ElementosConfiguracion/
│   ├── NuevaVersionEC.php
│   └── ECRequiereAprobacion.php
└── Liberaciones/
    └── NuevaLiberacion.php
```

---

### **FASE 3: CONTROLADOR DE NOTIFICACIONES**

**Archivo**: `app/Http/Controllers/NotificationController.php`

**Rutas**:
```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
```

---

### **FASE 4: INTERFAZ DE USUARIO**

#### 4.1 Dropdown de Notificaciones (Navigation Menu)
**Ubicación**: `resources/views/layouts/navigation.blade.php`

**Elementos**:
- 🔔 Ícono de campana
- Badge con contador de no leídas
- Dropdown con últimas 5 notificaciones
- Link "Ver todas" → `/notifications`
- Botón "Marcar todas como leídas"

#### 4.2 Página Completa de Notificaciones
**Ruta**: `/notifications`
**Vista**: `resources/views/notifications/index.blade.php`

**Características**:
- Lista completa con paginación
- Filtros: Todas | No leídas | Leídas
- Tabs por categoría: Proyectos | Tareas | CCB | etc.
- Marcar individualmente como leída
- Eliminar notificación

---

### **FASE 5: JOBS PROGRAMADOS**

**Archivo**: `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Notificar tareas próximas a vencer (cada día a las 8:00 AM)
    $schedule->job(new NotificarTareasProximasAVencer)->dailyAt('08:00');
    
    // Notificar tareas atrasadas (cada día a las 9:00 AM)
    $schedule->job(new NotificarTareasAtrasadas)->dailyAt('09:00');
    
    // Recordatorio Daily Scrum (Lunes a Viernes a las 9:30 AM)
    $schedule->job(new RecordatorioDailyScrum)->weekdays()->at('09:30');
    
    // Recordatorio votos pendientes CCB (cada 24h)
    $schedule->job(new RecordatorioVotosPendientes)->daily();
}
```

---

## 📝 EJEMPLO DE IMPLEMENTACIÓN

### Notificación: Solicitud Aprobada

#### **1. Clase Notification**
```php
<?php

namespace App\Notifications\Cambios;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\SolicitudCambio;

class SolicitudAprobada extends Notification
{
    public function __construct(public SolicitudCambio $solicitud)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail']; // Canales
    }

    public function toDatabase($notifiable): array
    {
        return [
            'solicitud_id' => $this->solicitud->id_solicitud,
            'titulo' => $this->solicitud->titulo,
            'proyecto_id' => $this->solicitud->proyecto_id,
            'proyecto_nombre' => $this->solicitud->proyecto->nombre,
            'tipo' => 'solicitud_aprobada',
            'icono' => 'check-circle',
            'color' => 'green',
            'mensaje' => "La solicitud de cambio '{$this->solicitud->titulo}' ha sido APROBADA",
            'url' => route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ])
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud de Cambio Aprobada')
            ->line("La solicitud de cambio '{$this->solicitud->titulo}' ha sido APROBADA.")
            ->action('Ver Solicitud', route('proyectos.solicitudes.show', [
                'proyecto' => $this->solicitud->proyecto_id,
                'solicitud' => $this->solicitud->id_solicitud
            ]))
            ->line('Gracias por usar nuestro sistema.');
    }
}
```

#### **2. Uso en Controlador**
```php
use App\Notifications\Cambios\SolicitudAprobada;
use Illuminate\Support\Facades\Notification;

// En SolicitudCambioController::verificarYProcesarQuorum()
if ($votosAprobar >= $ccb->quorum) {
    $solicitud->update([
        'estado' => 'APROBADA',
        'aprobado_por' => Auth::id(),
        'aprobado_en' => now(),
    ]);
    
    // ✅ NOTIFICAR
    // 1. Notificar al creador
    $solicitud->creadoPor->notify(new SolicitudAprobada($solicitud));
    
    // 2. Notificar a todos los miembros del CCB
    $miembrosCCB = $ccb->miembros;
    Notification::send($miembrosCCB, new SolicitudAprobada($solicitud));
    
    ImplementarSolicitudAprobadaJob::dispatch($solicitud);
}
```

---

## 🎨 DISEÑO UI - COMPONENTE NOTIFICACIONES

### Estructura HTML del Dropdown
```blade
<div class="relative" x-data="{ open: false }">
    <!-- Botón Campana -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        
        <!-- Badge contador -->
        @if($unreadCount = auth()->user()->unreadNotifications->count())
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" @click.away="open = false" 
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="font-semibold text-gray-900">Notificaciones</h3>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                    Marcar todas como leídas
                </button>
            </form>
        </div>

        <!-- Lista de notificaciones -->
        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" 
                   class="block p-4 hover:bg-gray-50 border-b {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                    
                    <div class="flex items-start gap-3">
                        <!-- Icono -->
                        <div class="w-10 h-10 rounded-full bg-{{ $notification->data['color'] ?? 'gray' }}-100 flex items-center justify-center">
                            <!-- SVG icon basado en $notification->data['icono'] -->
                        </div>
                        
                        <!-- Contenido -->
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ $notification->data['mensaje'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        
                        <!-- Indicador no leída -->
                        @if(!$notification->read_at)
                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p>No tienes notificaciones</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="p-3 border-t bg-gray-50 text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Ver todas las notificaciones →
            </a>
        </div>
    </div>
</div>
```

---

## ⏱️ CRONOGRAMA DE IMPLEMENTACIÓN

### **Semana 1: Infraestructura**
- ✅ Día 1-2: Crear migración y tabla `notifications`
- ✅ Día 3-4: Crear NotificationController con rutas
- ✅ Día 5: Testing de infraestructura

### **Semana 2: Notificaciones Core (Proyecto & CCB)**
- ✅ Día 1-2: Clases Notification para Proyectos (3 tipos)
- ✅ Día 3-5: Clases Notification para CCB (4 tipos)
- ✅ Integrar en controladores correspondientes

### **Semana 3: Notificaciones Tareas & Scrum**
- ✅ Día 1-3: Notificaciones de Tareas (4 tipos)
- ✅ Día 4-5: Notificaciones Scrum (4 tipos)

### **Semana 4: UI Completo**
- ✅ Día 1-2: Dropdown de notificaciones en navigation
- ✅ Día 3-4: Página completa `/notifications`
- ✅ Día 5: Pulir diseño y UX

### **Semana 5: Jobs & Optimización**
- ✅ Día 1-3: Crear jobs programados
- ✅ Día 4: Testing completo
- ✅ Día 5: Documentación y deploy

---

## 🧪 CASOS DE PRUEBA

1. ✅ Usuario recibe notificación al ser asignado a proyecto
2. ✅ Notificación aparece en dropdown con badge correcto
3. ✅ Al hacer clic, navega a la URL correcta
4. ✅ Marcar como leída actualiza estado
5. ✅ Marcar todas como leídas funciona
6. ✅ Jobs programados se ejecutan correctamente
7. ✅ Email se envía para notificaciones críticas

---

## 📦 ARCHIVOS A CREAR/MODIFICAR

### **Nuevos Archivos** (30+ archivos)
- `database/migrations/YYYY_MM_DD_create_notifications_table.php`
- `app/Http/Controllers/NotificationController.php`
- `app/Notifications/Proyecto/*.php` (3 archivos)
- `app/Notifications/Cambios/*.php` (4 archivos)
- `app/Notifications/Tareas/*.php` (4 archivos)
- `app/Notifications/Scrum/*.php` (4 archivos)
- `app/Notifications/Cronograma/*.php` (3 archivos)
- `app/Notifications/ElementosConfiguracion/*.php` (2 archivos)
- `app/Notifications/Liberaciones/*.php` (1 archivo)
- `app/Jobs/NotificarTareasProximasAVencer.php`
- `app/Jobs/NotificarTareasAtrasadas.php`
- `app/Jobs/RecordatorioDailyScrum.php`
- `app/Jobs/RecordatorioVotosPendientes.php`
- `resources/views/notifications/index.blade.php`
- `resources/views/components/notification-dropdown.blade.php`

### **Archivos a Modificar** (10+ archivos)
- `routes/web.php` (agregar rutas de notificaciones)
- `app/Console/Kernel.php` (schedule jobs)
- `resources/views/layouts/navigation.blade.php` (agregar dropdown)
- `app/Http/Controllers/gestionProyectos/ProyectoController.php`
- `app/Http/Controllers/gestionConfiguracion/SolicitudCambioController.php`
- `app/Http/Controllers/gestionProyectos/TareaProyectoController.php`
- `app/Http/Controllers/gestionProyectos/ScrumController.php`
- `app/Http/Controllers/gestionProyectos/CronogramaInteligenteController.php`
- `app/Http/Controllers/gestionProyectos/LiberacionesController.php`
- `app/Http/Controllers/gestionProyectos/ElementoConfiguracionController.php`

---

## ✨ MEJORAS FUTURAS (Post-MVP)

1. **WebSockets con Laravel Echo**: Notificaciones en tiempo real sin recargar
2. **Push Notifications**: Notificaciones del navegador
3. **Digest Email**: Resumen diario/semanal de notificaciones
4. **Preferencias de Notificación**: Usuario elige qué notificaciones recibir
5. **Notificaciones de Menciones**: @usuario en comentarios
6. **Analytics**: Dashboard de estadísticas de notificaciones

---

## 🎯 RESULTADO ESPERADO

Un sistema robusto de notificaciones que:
- ✅ Mantiene a los usuarios informados en tiempo real
- ✅ Mejora la colaboración y comunicación
- ✅ Reduce el tiempo de respuesta en procesos críticos
- ✅ Aumenta la transparencia del proyecto
- ✅ Facilita el seguimiento de tareas y responsabilidades

---

**Estado**: 📋 PLAN COMPLETO - LISTO PARA IMPLEMENTAR  
**Próximo Paso**: Crear migración `notifications` table
