# 🎯 SUPER USUARIO - GUÍA DE ACCESO

## 👤 CREDENCIALES DEL SUPER USUARIO

**Email:** `admin@sgcs.com`  
**Contraseña:** `admin123`

---

## ✨ CARACTERÍSTICAS DEL SUPER USUARIO

El usuario **admin@sgcs.com** es el **ADMINISTRADOR GENERAL** del sistema y tiene acceso completo a:

### 📂 **PROYECTO SCRUM**
- **Nombre:** E-Commerce Platform
- **Código:** ECOM-2024
- **Metodología:** Scrum
- **Rol:** Product Owner y Líder del equipo
- **Acceso a:**
  - Dashboard Scrum con tablero Kanban
  - Sprint Planning
  - Daily Scrum
  - Sprint Review
  - Sprint Retrospective
  - Crear y gestionar Sprints
  - Crear y gestionar User Stories
  - Gestión completa de elementos de configuración
  - Comité de Control de Cambios (CCB)

### 📂 **PROYECTO CASCADA**
- **Nombre:** Sistema ERP Corporativo
- **Código:** ERP-2024
- **Metodología:** Cascada
- **Rol:** Líder de Proyecto y Líder del equipo
- **Acceso a:**
  - Dashboard Cascada
  - Diagrama de Gantt
  - Gestión de fases
  - Gestión de tareas y entregables
  - Elementos de configuración
  - Liberaciones
  - Comité de Control de Cambios (CCB)

---

## 🚀 CÓMO INICIAR SESIÓN

1. Abre tu navegador
2. Ve a: `http://localhost:8000`
3. Haz clic en "Iniciar Sesión"
4. Ingresa:
   - **Email:** admin@sgcs.com
   - **Contraseña:** admin123
5. ¡Listo! Tendrás acceso completo

---

## 📋 ACCESOS RÁPIDOS

### **Dashboard Scrum:**
```
http://localhost:8000/proyectos/{id_proyecto_scrum}/scrum/dashboard
```

### **Dashboard Cascada:**
```
http://localhost:8000/proyectos/{id_proyecto_cascada}
```

### **Crear Sprint:**
```http
POST /proyectos/{id}/scrum/sprints
{
  "nombre": "Sprint 4",
  "objetivo": "Implementar nuevas funcionalidades",
  "fecha_inicio": "2025-11-20",
  "fecha_fin": "2025-12-04"
}
```

### **Crear User Story:**
```http
POST /proyectos/{id}/scrum/user-stories
{
  "nombre": "Como usuario quiero...",
  "descripcion": "Descripción detallada",
  "id_sprint": 3,
  "id_fase": 2,
  "story_points": 5
}
```

---

## 👥 OTROS USUARIOS DISPONIBLES

Para probar diferentes roles:

| Email | Contraseña | Rol |
|-------|------------|-----|
| `po@sgcs.com` | `po123` | Product Owner Scrum |
| `sm@sgcs.com` | `sm123` | Scrum Master |
| `pm@sgcs.com` | `pm123` | Project Manager Cascada |
| `dev1@sgcs.com` | `dev123` | Desarrollador |
| `qa@sgcs.com` | `qa123` | QA Lead |

---

## 🎯 LO QUE PUEDES HACER CON EL SUPER USUARIO

✅ **Ver y gestionar 2 proyectos completos** (Scrum + Cascada)  
✅ **Crear nuevos Sprints** en proyectos Scrum  
✅ **Iniciar y completar Sprints**  
✅ **Crear User Stories** y asignarlas a Sprints  
✅ **Mover tareas** en el tablero Kanban  
✅ **Gestionar el Product Backlog**  
✅ **Ver métricas y Burndown Charts**  
✅ **Gestionar Elementos de Configuración**  
✅ **Participar en el CCB** (Comité de Control de Cambios)  
✅ **Aprobar o rechazar solicitudes de cambio**  
✅ **Ver cronogramas inteligentes**  
✅ **Gestionar liberaciones**  

---

## 🔍 VERIFICAR ACCESO

Ejecuta este comando para verificar tus proyectos:

```bash
php tools/test_scrum_completo.php
```

Esto mostrará:
- Los proyectos donde eres líder
- Sprints disponibles
- User Stories
- Rutas de acceso

---

**¡DISFRUTA PROBANDO TODO EL SISTEMA!** 🎉
