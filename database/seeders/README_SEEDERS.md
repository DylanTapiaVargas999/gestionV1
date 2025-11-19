# 🎯 GUÍA DE SEEDERS - DEMOSTRACIÓN SGCS

## 📋 Descripción

Este sistema de seeders está diseñado para crear una **demostración completa y profesional** del Sistema de Gestión de Configuración de Software (SGCS) con dos proyectos principales:

1. **E-Commerce Platform** - Metodología **Scrum** (Ágil)
2. **Sistema ERP Corporativo** - Metodología **Cascada** (Tradicional)

---

## 🗂️ Estructura de Seeders

### **Orden de Ejecución:**

1. **MetodologiasSeeder** - Crea metodologías Scrum y Cascada con sus fases
2. **PlantillasECSeeder** - Plantillas de elementos de configuración por metodología
3. **RolesSeeder** - 12 roles profesionales del SGCS
4. **UsuarioSeeder** - 19 usuarios profesionales con roles específicos
5. **DemoCompletaSeeder** - ⭐ Seeder maestro que crea todo el contenido demo

---

## 👥 Usuarios Creados

### **Gestión Global**
- `scm.manager@sgcs.com` - Carlos Méndez (Gestor de Configuración)
- `ccb.admin@sgcs.com` - Ana Patricia López (Admin CCB)
- `release.manager@sgcs.com` - Elena Vargas (Release Manager)
- `auditor@sgcs.com` - Lic. Javier Campos (Auditor)

### **Equipo Proyecto Scrum (E-Commerce)**
- `po.scrum@sgcs.com` - María González (Product Owner)
- `sm.scrum@sgcs.com` - Roberto Castillo (Scrum Master)
- `dev.senior.scrum@sgcs.com` - Luis Hernández (Dev Senior)
- `dev1.scrum@sgcs.com` - Carmen Ruiz (Desarrolladora)
- `dev2.scrum@sgcs.com` - Diego Morales (Desarrollador)
- `qa.scrum@sgcs.com` - Patricia Vega (Analista QA)
- `tester.scrum@sgcs.com` - Jorge Ramírez (Tester)

### **Equipo Proyecto Cascada (ERP)**
- `pm.cascada@sgcs.com` - Fernando Sánchez (Líder Proyecto)
- `architect.cascada@sgcs.com` - Dr. Alberto Jiménez (Arquitecto)
- `analyst.cascada@sgcs.com` - Laura Martínez (Analista)
- `dev.senior.cascada@sgcs.com` - Andrés Ortiz (Dev Senior)
- `dev1.cascada@sgcs.com` - Sofía Gutiérrez (Desarrolladora)
- `dev2.cascada@sgcs.com` - Miguel Ángel Torres (Desarrollador)
- `qa.cascada@sgcs.com` - Gabriela Rojas (Analista QA)
- `tester.cascada@sgcs.com` - Ricardo Pérez (Tester)

**Contraseñas:** Varían según el rol (ej: `scm123`, `po123`, `dev123`, etc.)

---

## 🎭 Roles del SGCS

1. **Gestor de Configuración** - SCM Manager
2. **Administrador CCB** - CCB Administrator
3. **Líder de Proyecto** - Project Leader
4. **Product Owner** - Dueño del producto
5. **Scrum Master** - Facilitador Scrum
6. **Desarrollador Senior** - Senior Developer
7. **Desarrollador** - Developer
8. **Analista QA** - Quality Assurance
9. **Tester** - Testing Specialist
10. **Arquitecto de Software** - Software Architect
11. **Auditor de Configuración** - Configuration Auditor
12. **Release Manager** - Release Manager

---

## 📦 Proyectos Creados

### **1. E-Commerce Platform (ECOM-2024)** 🛒
- **Metodología:** Scrum
- **Estado:** En desarrollo activo
- **Equipo:** 7 miembros
- **Elementos de Configuración:** 12
  - Product Backlog
  - Sprint Backlog
  - Repositorio Git
  - Esquema de BD
  - Documentación API REST
  - Módulos: Autenticación, Productos, Carrito, Pagos
  - Tests Automatizados
  - CI/CD Pipeline
  - Definition of Done

- **Fases Scrum:**
  - Product Backlog
  - Sprint Planning
  - In Progress
  - In Review
  - Done

- **Tareas:** 8 historias de usuario en diferentes estados

### **2. Sistema ERP Corporativo (ERP-2024)** 🏢
- **Metodología:** Cascada
- **Estado:** En fase de implementación
- **Equipo:** 8 miembros
- **Elementos de Configuración:** 15
  - SRS (Especificación de Requisitos)
  - Plan de Proyecto
  - Documento de Arquitectura (SAD)
  - Modelo Entidad-Relación
  - Scripts DDL
  - Repositorio Git
  - Módulos: Contabilidad, RRHH, Inventario, Compras, Ventas
  - Plan Maestro de Pruebas
  - Casos de Prueba
  - Manual de Usuario
  - Plan de Despliegue

- **Fases Cascada:**
  - Requisitos
  - Análisis
  - Diseño
  - Implementación
  - Pruebas
  - Despliegue
  - Mantenimiento

- **Tareas:** 17 tareas distribuidas en todas las fases

### **3. Proyectos Adicionales** (para llenar la demo)
- **MOB-2024** - App Móvil Bancaria (Scrum)
- **WEB-2024** - Portal Institucional (Cascada)
- **API-2024** - API Gateway Empresarial (Scrum)

---

## 🔗 Relaciones entre Elementos de Configuración

### **Proyecto Scrum:**
- Sprint Backlog → DEPENDE_DE → Product Backlog
- Módulo Autenticación → DEPENDE_DE → Repositorio y BD
- Módulo Productos → DEPENDE_DE → Autenticación
- Módulo Carrito → DEPENDE_DE → Productos
- Módulo Pagos → DEPENDE_DE → Carrito
- Tests → REFERENCIA → API

### **Proyecto Cascada:**
- Arquitectura → DERIVADO_DE → SRS
- Modelo BD → DERIVADO_DE → Arquitectura
- Scripts BD → DERIVADO_DE → Modelo BD
- Todos los módulos → DEPENDE_DE → Repositorio y BD
- Ventas → REQUERIDO_POR → Inventario
- Casos de Prueba → DERIVADO_DE → Plan de Pruebas

---

## 🔒 Comités de Control de Cambios (CCB)

### **CCB E-Commerce**
- Quorum: 3 miembros
- Miembros:
  - Carlos Méndez (Presidente)
  - María González (Product Owner)
  - Roberto Castillo (Scrum Master)
  - Luis Hernández (Líder Técnico)
  - Patricia Vega (QA Lead)

### **CCB ERP**
- Quorum: 4 miembros
- Miembros:
  - Ana Patricia López (Presidente CCB)
  - Fernando Sánchez (Líder Proyecto)
  - Dr. Alberto Jiménez (Arquitecto)
  - Andrés Ortiz (Líder Técnico)
  - Gabriela Rojas (QA Manager)
  - Lic. Javier Campos (Auditor)

---

## 🚀 Cómo Ejecutar los Seeders

### **Opción 1: Ejecutar todo** (Recomendado)

```bash
php artisan db:seed
```

Esto ejecutará todos los seeders en el orden correcto.

### **Opción 2: Ejecutar solo la demo completa**

```bash
# Primero ejecuta los seeders base
php artisan db:seed --class=MetodologiasSeeder
php artisan db:seed --class=PlantillasECSeeder
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=UsuarioSeeder

# Luego ejecuta la demo completa
php artisan db:seed --class=DemoCompletaSeeder
```

### **Opción 3: Refrescar todo desde cero**

```bash
php artisan migrate:fresh --seed
```

⚠️ **ADVERTENCIA:** Esto eliminará TODOS los datos existentes.

---

## 📊 Características de la Demostración

### ✅ **Completa:**
- Dos proyectos con metodologías diferentes
- Equipos completos con roles asignados
- Elementos de configuración realistas
- Relaciones entre elementos (dependencias, derivaciones)
- Tareas distribuidas en fases
- Comités de cambio funcionales

### ✅ **Profesional:**
- Nombres de usuarios realistas
- Roles dignos de un SGCS
- Descripciones técnicas detalladas
- Estructura organizacional coherente
- Versionado de elementos
- Estados de elementos variados

### ✅ **Realista:**
- Fechas coherentes
- Estados de tareas variados (completada, en progreso, pendiente)
- Relaciones lógicas entre componentes
- Módulos con dependencias técnicas reales
- Documentación según estándares (IEEE, PMBOK)

---

## 🔍 Verificación

Después de ejecutar los seeders, verifica que todo se haya creado correctamente:

```bash
# Contar registros
php artisan tinker
>>> DB::table('usuarios')->count();          # Debería ser 19
>>> DB::table('proyectos')->count();         # Debería ser 5
>>> DB::table('elementos_configuracion')->count();  # Debería ser ~27
>>> DB::table('equipos')->count();           # Debería ser 2
>>> DB::table('comite_cambios')->count();    # Debería ser 2
```

---

## 📝 Notas Importantes

1. **Metodologías:** Solo se crean Scrum y Cascada (no Kanban)
2. **Plantillas EC:** Adaptadas específicamente para cada metodología
3. **Usuarios:** Todos tienen correos únicos y contraseñas simples para demo
4. **UUIDs:** Todos los IDs principales son UUIDs
5. **Relaciones:** Las FK están correctamente configuradas
6. **Estados:** Variados para simular proyectos en curso real

---

## 🛠️ Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "SQLSTATE[23000]: Integrity constraint violation"
Ejecuta los seeders en orden o usa:
```bash
php artisan migrate:fresh --seed
```

### Error: "Call to undefined method"
Verifica que todos los modelos existan y estén correctamente importados.

---

## 📧 Contacto

Para más información sobre el SGCS, consulta los documentos del proyecto:
- `ANALISIS_COMPLETO_PROYECTO.md`
- `SISTEMA_CCB_IMPLEMENTADO.md`
- `CRONOGRAMA_INTELIGENTE_IMPLEMENTADO.md`

---

**¡Disfruta de tu demostración completa del SGCS! 🎉**
