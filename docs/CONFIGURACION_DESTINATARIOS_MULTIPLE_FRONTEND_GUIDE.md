# Sistema de Alertas - Configuración de Destinatarios para Frontend

## 📧 **Gestión de Múltiples Destinatarios**

El sistema de alertas de mantenimiento está preparado para manejar múltiples destinatarios de correo con validaciones robustas y configuración flexible.

---

## 🚀 **Endpoints de API para Destinatarios**

### **1. Obtener Configuración Actual**
```http
GET /api/configuracion-alertas
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Configuraciones obtenidas exitosamente",
  "data": {
    "destinatarios": {
      "emails_principales": {
        "valor": "[\"admin@empresa.com\", \"mantenimiento@empresa.com\"]",
        "descripcion": "Lista de emails principales para alertas de mantenimiento"
      },
      "emails_copia": {
        "valor": "[\"supervisor@empresa.com\"]",
        "descripcion": "Lista de emails en copia para alertas de mantenimiento"
      }
    }
  }
}
```

### **2. Actualizar Destinatarios**
```http
PUT /api/configuracion-alertas/destinatarios
Content-Type: application/json
```

**Payload:**
```json
{
  "emails_principales": [
    "admin@empresa.com",
    "mantenimiento@empresa.com",
    "jefe-taller@empresa.com"
  ],
  "emails_copia": [
    "supervisor@empresa.com",
    "director@empresa.com"
  ],
  "notificar_inmediato": true,
  "incluir_en_copia_diaria": true
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "message": "Configuraciones de destinatarios actualizadas exitosamente",
  "data": {
    "emails_principales": [
      "admin@empresa.com",
      "mantenimiento@empresa.com",
      "jefe-taller@empresa.com"
    ],
    "emails_copia": [
      "supervisor@empresa.com",
      "director@empresa.com"
    ],
    "total_destinatarios": 5,
    "fecha_actualizacion": "2025-07-24T10:15:00.000000Z"
  }
}
```

---

## ✅ **Validaciones Implementadas**

### **Emails Principales**
- ✅ **Obligatorio**: Mínimo 1 email
- ✅ **Máximo**: 10 emails principales
- ✅ **Formato**: Validación RFC + DNS
- ✅ **Únicos**: No duplicados en la lista
- ✅ **Longitud**: Máximo 255 caracteres por email

### **Emails en Copia**
- ✅ **Opcional**: Puede estar vacío
- ✅ **Máximo**: 20 emails en copia
- ✅ **Formato**: Validación RFC + DNS
- ✅ **Únicos**: No duplicados en la lista
- ✅ **Cross-validation**: No puede duplicar emails principales

### **Validaciones Globales**
- ✅ **Total máximo**: 25 emails únicos en total
- ✅ **Normalización**: Convierte a lowercase y trim automático
- ✅ **Sin duplicados**: Entre principales y copia

---

## 🎨 **Ejemplo de Implementación en Blade/JavaScript**

### **Formulario HTML/Blade**
```html
<form id="destinatarios-form" method="POST">
    @csrf
    @method('PUT')
    
    <!-- Emails Principales -->
    <div class="form-group">
        <label for="emails_principales">Emails Principales *</label>
        <div id="emails-principales-container">
            <input type="email" name="emails_principales[]" 
                   class="form-control mb-2" 
                   placeholder="admin@empresa.com" required>
        </div>
        <button type="button" id="add-principal" class="btn btn-sm btn-secondary">
            + Agregar Email Principal
        </button>
        <small class="text-muted">Máximo 10 emails principales</small>
    </div>

    <!-- Emails en Copia -->
    <div class="form-group">
        <label for="emails_copia">Emails en Copia (Opcional)</label>
        <div id="emails-copia-container">
            <input type="email" name="emails_copia[]" 
                   class="form-control mb-2" 
                   placeholder="supervisor@empresa.com">
        </div>
        <button type="button" id="add-copia" class="btn btn-sm btn-secondary">
            + Agregar Email en Copia
        </button>
        <small class="text-muted">Máximo 20 emails en copia</small>
    </div>

    <!-- Opciones Adicionales -->
    <div class="form-check">
        <input type="checkbox" name="notificar_inmediato" 
               class="form-check-input" id="notificar_inmediato" value="1">
        <label class="form-check-label" for="notificar_inmediato">
            Notificar inmediatamente cuando se detecte una alerta
        </label>
    </div>

    <div class="form-check">
        <input type="checkbox" name="incluir_en_copia_diaria" 
               class="form-check-input" id="incluir_en_copia_diaria" value="1">
        <label class="form-check-label" for="incluir_en_copia_diaria">
            Incluir en el reporte diario automático
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Guardar Configuración</button>
</form>
```

### **JavaScript para Manejo Dinámico**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    const maxPrincipales = 10;
    const maxCopia = 20;
    
    // Agregar email principal
    document.getElementById('add-principal').addEventListener('click', function() {
        const container = document.getElementById('emails-principales-container');
        const currentInputs = container.querySelectorAll('input').length;
        
        if (currentInputs < maxPrincipales) {
            const newInput = createEmailInput('emails_principales[]', 'Email principal', true);
            container.appendChild(newInput);
        } else {
            alert(`Máximo ${maxPrincipales} emails principales permitidos`);
        }
    });
    
    // Agregar email en copia
    document.getElementById('add-copia').addEventListener('click', function() {
        const container = document.getElementById('emails-copia-container');
        const currentInputs = container.querySelectorAll('input').length;
        
        if (currentInputs < maxCopia) {
            const newInput = createEmailInput('emails_copia[]', 'Email en copia', false);
            container.appendChild(newInput);
        } else {
            alert(`Máximo ${maxCopia} emails en copia permitidos`);
        }
    });
    
    // Crear input de email con botón de eliminar
    function createEmailInput(name, placeholder, required) {
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        
        div.innerHTML = `
            <input type="email" name="${name}" 
                   class="form-control" 
                   placeholder="${placeholder}" 
                   ${required ? 'required' : ''}>
            <button type="button" class="btn btn-outline-danger btn-sm remove-email">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        // Agregar evento de eliminar
        div.querySelector('.remove-email').addEventListener('click', function() {
            div.remove();
        });
        
        return div;
    }
    
    // Envío del formulario con AJAX
    document.getElementById('destinatarios-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            emails_principales: formData.getAll('emails_principales[]').filter(email => email.trim()),
            emails_copia: formData.getAll('emails_copia[]').filter(email => email.trim()),
            notificar_inmediato: formData.has('notificar_inmediato'),
            incluir_en_copia_diaria: formData.has('incluir_en_copia_diaria')
        };
        
        fetch('/api/configuracion-alertas/destinatarios', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Configuración guardada exitosamente');
                updateUI(data.data);
            } else {
                showErrors(data.errors || { general: [data.message] });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrors({ general: ['Error de conexión. Intente nuevamente.'] });
        });
    });
});

function showSuccess(message) {
    // Implementar notificación de éxito
    alert(message);
}

function showErrors(errors) {
    // Implementar mostrar errores de validación
    console.error('Errores de validación:', errors);
    
    // Ejemplo básico
    let errorMessage = 'Errores encontrados:\n';
    Object.values(errors).forEach(errorArray => {
        errorArray.forEach(error => {
            errorMessage += '- ' + error + '\n';
        });
    });
    alert(errorMessage);
}

function updateUI(data) {
    // Actualizar contadores o indicadores en la UI
    const totalSpan = document.getElementById('total-destinatarios');
    if (totalSpan) {
        totalSpan.textContent = data.total_destinatarios;
    }
}
```

---

## 🧪 **Endpoint de Prueba de Envío**

### **Probar Configuración Actual**
```http
POST /api/configuracion-alertas/probar-envio
Content-Type: application/json
```

**Payload para Simulación:**
```json
{
  "email": "test@empresa.com",
  "mailer": "log",
  "enviar_real": false
}
```

**Payload para Envío Real:**
```json
{
  "email": "admin@empresa.com",
  "mailer": "smtp",
  "enviar_real": true
}
```

---

## 📋 **Mensajes de Error Comunes**

### **Errores de Validación:**
```json
{
  "success": false,
  "message": "Datos de validación incorrectos",
  "errors": {
    "emails_principales": [
      "Debe especificar al menos un email principal."
    ],
    "emails_principales.0": [
      "El formato del email principal no es válido."
    ],
    "emails_copia": [
      "Los emails en copia no pueden duplicarse con los principales: admin@empresa.com"
    ]
  }
}
```

### **Error de Límites:**
```json
{
  "success": false,
  "message": "Datos de validación incorrectos",
  "errors": {
    "emails_principales": [
      "El total de emails únicos no puede exceder 25."
    ]
  }
}
```

---

## 🔧 **Configuración Actual del Sistema**

- **Email Origen**: `ebravotube@gmail.com`
- **Email Test**: `ebravotube@gmail.com` (mismo que origen)
- **SMTP**: Gmail configurado
- **Plantillas**: HTML + Text disponibles
- **PDF**: Generación automática de reportes
- **Anti-Spam**: Headers configurados

---

## ✨ **Funcionalidades Listas para el Frontend**

✅ **CRUD Completo**: Crear, leer, actualizar destinatarios  
✅ **Validación Robusta**: Frontend + Backend  
✅ **Multiple Emails**: Hasta 25 destinatarios únicos  
✅ **Test de Envío**: Endpoint para probar configuración  
✅ **Logs Completos**: Seguimiento de todas las operaciones  
✅ **Error Handling**: Manejo de errores detallado  
✅ **Normalización**: Emails automáticamente normalizados  
✅ **Anti-Duplicados**: Validación cruzada de duplicados  

El backend está **100% preparado** para que el frontend implemente la gestión de múltiples destinatarios de alertas de mantenimiento.
