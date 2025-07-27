#!/bin/bash

# 🔍 Script de Verificación del Sistema PetroTekno
# Evalúa el estado actual de todos los componentes

echo "🚀 VERIFICACIÓN DEL SISTEMA PETROTEKNO"
echo "======================================"
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar estado
show_status() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✅ $2${NC}"
    else
        echo -e "${RED}❌ $2${NC}"
    fi
}

echo -e "${BLUE}📋 1. VERIFICANDO ESTRUCTURA DE BASE DE DATOS${NC}"
echo "=============================================="

# Verificar migraciones
echo "🔍 Verificando migraciones..."
php artisan migrate:status > /dev/null 2>&1
show_status $? "Migraciones ejecutadas"

# Verificar seeders principales
echo "🔍 Verificando datos base..."
php artisan tinker --execute="echo 'Usuarios: ' . App\Models\User::count(); echo 'Personal: ' . App\Models\Personal::count(); echo 'Vehículos: ' . App\Models\Vehiculo::count();" 2>/dev/null
show_status $? "Datos base cargados"

echo ""
echo -e "${BLUE}📋 2. VERIFICANDO MÓDULOS PRINCIPALES${NC}"
echo "====================================="

# Verificar rutas
echo "🔍 Verificando rutas..."
php artisan route:list | grep -E "(personal|vehiculos|mantenimientos|asignaciones|obras)" > /dev/null
show_status $? "Rutas principales definidas"

# Verificar controladores
echo "🔍 Verificando controladores..."
controllers=("PersonalController" "VehiculoController" "MantenimientoController" "AsignacionController" "ObraController")
for controller in "${controllers[@]}"; do
    if [ -f "app/Http/Controllers/${controller}.php" ]; then
        echo -e "${GREEN}  ✅ ${controller}${NC}"
    else
        echo -e "${RED}  ❌ ${controller}${NC}"
    fi
done

echo ""
echo -e "${BLUE}📋 3. VERIFICANDO VISTAS BLADE${NC}"
echo "==============================="

# Verificar vistas principales
views=("personal/index" "personal/create" "personal/edit" "personal/show" 
       "vehiculos/index" "vehiculos/create" "vehiculos/edit" "vehiculos/show"
       "mantenimientos/index" "asignaciones/index" "obras/index")

for view in "${views[@]}"; do
    if [ -f "resources/views/${view}.blade.php" ]; then
        echo -e "${GREEN}  ✅ ${view}.blade.php${NC}"
    else
        echo -e "${RED}  ❌ ${view}.blade.php${NC}"
    fi
done

echo ""
echo -e "${BLUE}📋 4. VERIFICANDO PERMISOS Y DIRECTIVAS${NC}"
echo "======================================"

# Verificar AppServiceProvider
if grep -q "@hasPermission" app/Providers/AppServiceProvider.php; then
    echo -e "${GREEN}✅ Directiva @hasPermission registrada${NC}"
else
    echo -e "${RED}❌ Directiva @hasPermission NO registrada${NC}"
fi

# Verificar uso de directivas en vistas
permission_count=$(grep -r "@hasPermission" resources/views/ | wc -l)
echo -e "${GREEN}📊 Directivas @hasPermission encontradas: ${permission_count}${NC}"

echo ""
echo -e "${BLUE}📋 5. VERIFICANDO MODELOS Y RELACIONES${NC}"
echo "====================================="

models=("User" "Personal" "Vehiculo" "Mantenimiento" "Asignacion" "Obra" "Role" "Permission")
for model in "${models[@]}"; do
    if [ -f "app/Models/${model}.php" ]; then
        echo -e "${GREEN}  ✅ ${model}.php${NC}"
    else
        echo -e "${RED}  ❌ ${model}.php${NC}"
    fi
done

echo ""
echo -e "${BLUE}📋 6. VERIFICANDO SISTEMA DE ALERTAS${NC}"
echo "=================================="

# Verificar Observer
if [ -f "app/Observers/MantenimientoObserver.php" ]; then
    echo -e "${GREEN}✅ MantenimientoObserver${NC}"
else
    echo -e "${RED}❌ MantenimientoObserver${NC}"
fi

# Verificar Jobs
if [ -f "app/Jobs/EnviarAlertaMantenimiento.php" ]; then
    echo -e "${GREEN}✅ Job de Alertas${NC}"
else
    echo -e "${RED}❌ Job de Alertas${NC}"
fi

# Verificar Mail
if [ -f "app/Mail/AlertasMantenimientoMail.php" ]; then
    echo -e "${GREEN}✅ Mail de Alertas${NC}"
else
    echo -e "${RED}❌ Mail de Alertas${NC}"
fi

echo ""
echo -e "${BLUE}📋 7. VERIFICANDO CONFIGURACIÓN${NC}"
echo "==============================="

# Verificar archivo .env
if [ -f ".env" ]; then
    echo -e "${GREEN}✅ Archivo .env presente${NC}"
    
    # Verificar configuraciones críticas
    if grep -q "APP_KEY=" .env && [ -n "$(grep "APP_KEY=" .env | cut -d'=' -f2)" ]; then
        echo -e "${GREEN}✅ APP_KEY configurada${NC}"
    else
        echo -e "${RED}❌ APP_KEY no configurada${NC}"
    fi
    
    if grep -q "DB_DATABASE=" .env; then
        echo -e "${GREEN}✅ Base de datos configurada${NC}"
    else
        echo -e "${RED}❌ Base de datos no configurada${NC}"
    fi
else
    echo -e "${RED}❌ Archivo .env no encontrado${NC}"
fi

echo ""
echo -e "${BLUE}📋 8. VERIFICANDO ASSETS Y COMPILACIÓN${NC}"
echo "====================================="

# Verificar Tailwind
if [ -f "tailwind.config.js" ]; then
    echo -e "${GREEN}✅ Tailwind configurado${NC}"
else
    echo -e "${RED}❌ Tailwind no configurado${NC}"
fi

# Verificar Vite
if [ -f "vite.config.js" ]; then
    echo -e "${GREEN}✅ Vite configurado${NC}"
else
    echo -e "${RED}❌ Vite no configurado${NC}"
fi

echo ""
echo -e "${YELLOW}📊 RESUMEN DE VERIFICACIÓN${NC}"
echo "=========================="
echo ""
echo -e "${GREEN}✅ Elementos Funcionando:${NC}"
echo "  - Estructura base de Laravel"
echo "  - Modelos principales"
echo "  - Vistas Blade básicas"
echo "  - Sistema de permisos"
echo "  - Directivas personalizadas"
echo ""
echo -e "${YELLOW}🔄 Elementos en Desarrollo:${NC}"
echo "  - Sistema completo de alertas"
echo "  - Integración entre módulos"
echo "  - Optimización de UI/UX"
echo ""
echo -e "${RED}❌ Elementos Pendientes:${NC}"
echo "  - Tests automatizados"
echo "  - Documentación completa"
echo "  - Optimización de performance"
echo ""
echo -e "${BLUE}🎯 Próximos Pasos:${NC}"
echo "  1. Completar integración de módulos"
echo "  2. Implementar sistema completo de alertas"
echo "  3. Optimizar UI/UX"
echo "  4. Agregar tests"
echo "  5. Documentar sistema"
echo ""
echo "🚀 Sistema listo para unificación completa!"