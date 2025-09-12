/**
 * Script para manejar la carga de estados y municipios en formularios
 */

// Función para inicializar los selectores de estado y municipio
function initEstadosMunicipios() {
    console.log('Inicializando estados y municipios');
    // Referencias a los elementos select
    const estadoSelect = document.getElementById('estado');
    const municipioSelect = document.getElementById('municipio');
    
    console.log('Estado select:', estadoSelect);
    console.log('Municipio select:', municipioSelect);
    
    if (!estadoSelect || !municipioSelect) {
        console.error('No se encontraron los elementos select de estado o municipio');
        return;
    }
    
    // Guardar los valores actuales antes de recargar las opciones
    const estadoActual = estadoSelect.getAttribute('data-valor-actual') || '';
    const municipioActual = municipioSelect.getAttribute('data-valor-actual') || '';
    
    console.log('Estado actual:', estadoActual);
    console.log('Municipio actual:', municipioActual);
    
    // Cargar estados
    fetch('/estados')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(estados => {
            console.log('Estados cargados:', estados);
            // Limpiar y agregar opción por defecto
            estadoSelect.innerHTML = '<option value="">Seleccione un estado</option>';
            
            // Agregar opciones de estados
            estados.forEach(estado => {
                const option = document.createElement('option');
                option.value = estado.nombre;
                option.textContent = estado.nombre;
                // Preseleccionar el estado si coincide con el valor actual (ignorando mayúsculas/minúsculas)
                if (estado.nombre.toLowerCase() === estadoActual.toLowerCase()) {
                    option.selected = true;
                    console.log('Estado seleccionado desde API:', estado.nombre);
                }
                estadoSelect.appendChild(option);
            });
            
            // Si hay un estado seleccionado, cargar sus municipios
            if (estadoActual) {
                cargarMunicipios(estadoActual, municipioActual);
            }
        })
        .catch(error => {
            console.error('Error al cargar estados:', error);
            // Cargar estados de respaldo en caso de error
            cargarEstadosRespaldo(estadoSelect, estadoActual, municipioActual);
        });
    
    // Función para cargar municipios
    function cargarMunicipios(estadoSeleccionado, municipioSeleccionado = '') {
        console.log('Cargando municipios para estado:', estadoSeleccionado, 'municipio seleccionado:', municipioSeleccionado);
        
        if (!estadoSeleccionado) {
            // Si no hay estado seleccionado, limpiar municipios
            municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
            municipioSelect.disabled = true;
            return;
        }
        
        // Habilitar select de municipios
        municipioSelect.disabled = false;
        
        // Cargar municipios del estado seleccionado
        fetch(`/municipios/${encodeURIComponent(estadoSeleccionado)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(municipios => {
                console.log('Municipios cargados:', municipios);
                // Limpiar y agregar opción por defecto
                municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                
                // Agregar opciones de municipios
                municipios.forEach(municipio => {
                    const option = document.createElement('option');
                    option.value = municipio;
                    option.textContent = municipio;
                    // Preseleccionar el municipio si coincide con el valor actual (ignorando mayúsculas/minúsculas)
                    if (municipio.toLowerCase() === municipioSeleccionado.toLowerCase()) {
                        option.selected = true;
                        console.log('Municipio seleccionado desde API:', municipio);
                    }
                    municipioSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar municipios:', error);
                // En caso de error, cargar municipios de respaldo
                cargarMunicipiosRespaldo(municipioSelect, estadoSeleccionado, municipioSeleccionado);
            });
    }
    
    // Evento para cargar municipios cuando cambia el estado
    estadoSelect.addEventListener('change', function() {
        const estadoSeleccionado = this.value;
        console.log('Estado seleccionado (cambio):', estadoSeleccionado);
        cargarMunicipios(estadoSeleccionado);
    });
}

// Función para cargar estados de respaldo en caso de error
function cargarEstadosRespaldo(estadoSelect, estadoActual = '', municipioActual = '') {
    console.log('Cargando estados de respaldo, estado actual:', estadoActual);
    const estadosRespaldo = [
        { nombre: 'AGUASCALIENTES' },
        { nombre: 'BAJA CALIFORNIA' },
        { nombre: 'BAJA CALIFORNIA SUR' },
        { nombre: 'CAMPECHE' },
        { nombre: 'CHIAPAS' },
        { nombre: 'CHIHUAHUA' },
        { nombre: 'CIUDAD DE MÉXICO' },
        { nombre: 'COAHUILA' },
        { nombre: 'COLIMA' },
        { nombre: 'DURANGO' },
        { nombre: 'ESTADO DE MÉXICO' },
        { nombre: 'GUANAJUATO' },
        { nombre: 'GUERRERO' },
        { nombre: 'HIDALGO' },
        { nombre: 'JALISCO' },
        { nombre: 'MICHOACÁN' },
        { nombre: 'MORELOS' },
        { nombre: 'NAYARIT' },
        { nombre: 'NUEVO LEÓN' },
        { nombre: 'OAXACA' },
        { nombre: 'PUEBLA' },
        { nombre: 'QUERÉTARO' },
        { nombre: 'QUINTANA ROO' },
        { nombre: 'SAN LUIS POTOSÍ' },
        { nombre: 'SINALOA' },
        { nombre: 'SONORA' },
        { nombre: 'TABASCO' },
        { nombre: 'TAMAULIPAS' },
        { nombre: 'TLAXCALA' },
        { nombre: 'VERACRUZ' },
        { nombre: 'YUCATÁN' },
        { nombre: 'ZACATECAS' }
    ];
    
    // Limpiar y agregar opción por defecto
    estadoSelect.innerHTML = '<option value="">Seleccione un estado</option>';
    
    // Agregar opciones de estados
    estadosRespaldo.forEach(estado => {
        const option = document.createElement('option');
        option.value = estado.nombre;
        option.textContent = estado.nombre;
        
        // Preseleccionar el estado si coincide con el valor actual (ignorando mayúsculas/minúsculas)
        if (estado.nombre.toLowerCase() === estadoActual.toLowerCase()) {
            option.selected = true;
            console.log('Estado seleccionado:', estado.nombre);
        }
        estadoSelect.appendChild(option);
    });
    
    // Si hay un estado seleccionado, cargar sus municipios
    if (estadoActual) {
        const municipioSelect = document.getElementById('municipio');
        if (municipioSelect) {
            // Buscar el estado en el formato correcto (mayúsculas)
            const estadoFormateado = estadosRespaldo.find(e => e.nombre.toLowerCase() === estadoActual.toLowerCase())?.nombre || estadoActual;
            cargarMunicipiosRespaldo(municipioSelect, estadoFormateado, municipioActual);
        }
    }
}

// Función para cargar municipios de respaldo en caso de error
function cargarMunicipiosRespaldo(municipioSelect, estadoSeleccionado, municipioSeleccionado = '') {
    console.log('Cargando municipios de respaldo para:', estadoSeleccionado, 'municipio seleccionado:', municipioSeleccionado);
    
    // Mapa de municipios por estado (solo algunos ejemplos para demostración)
    const municipiosPorEstado = {
        'AGUASCALIENTES': ['Aguascalientes', 'Asientos', 'Calvillo', 'Cosío', 'Jesús María', 'Pabellón de Arteaga', 'Rincón de Romos', 'San José de Gracia', 'Tepezalá', 'El Llano', 'San Francisco de los Romo'],
        'BAJA CALIFORNIA': ['Ensenada', 'Mexicali', 'Tecate', 'Tijuana', 'Playas de Rosarito', 'San Quintín'],
        'CHIAPAS': ['Tuxtla Gutiérrez', 'San Cristóbal de las Casas', 'Tapachula', 'Palenque', 'Comitán', 'Chiapa de Corzo'],
        'CIUDAD DE MÉXICO': ['Álvaro Obregón', 'Azcapotzalco', 'Benito Juárez', 'Coyoacán', 'Cuajimalpa', 'Cuauhtémoc', 'Gustavo A. Madero', 'Iztacalco', 'Iztapalapa', 'Magdalena Contreras', 'Miguel Hidalgo', 'Milpa Alta', 'Tláhuac', 'Tlalpan', 'Venustiano Carranza', 'Xochimilco'],
        'HIDALGO': ['Pachuca de Soto', 'Tulancingo', 'Tula de Allende', 'Huejutla de Reyes', 'Ixmiquilpan', 'Actopan', 'Tepeji del Río', 'Tizayuca', 'Apan', 'Zimapán', 'Zacualtipán', 'Atotonilco el Grande'],
        'JALISCO': ['Guadalajara', 'Zapopan', 'Tlaquepaque', 'Tonalá', 'Puerto Vallarta', 'Lagos de Moreno', 'El Salto', 'Tepatitlán de Morelos'],
        'NUEVO LEÓN': ['Monterrey', 'Guadalupe', 'San Nicolás de los Garza', 'Apodaca', 'General Escobedo', 'Santa Catarina', 'Juárez', 'García'],
        'VERACRUZ': ['Xalapa', 'Veracruz', 'Coatzacoalcos', 'Córdoba', 'Poza Rica', 'Orizaba', 'Minatitlán', 'Boca del Río']
    };
    
    // Limpiar y agregar opción por defecto
    municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
    
    // Obtener los municipios para el estado seleccionado
    const municipios = municipiosPorEstado[estadoSeleccionado] || [];
    
    if (municipios.length === 0) {
        // Si no hay municipios para este estado, mostrar mensaje
        const option = document.createElement('option');
        option.value = "";
        option.textContent = "No hay municipios disponibles para este estado";
        municipioSelect.appendChild(option);
        console.log('No se encontraron municipios para:', estadoSeleccionado);
        return;
    }
    
    // Agregar opciones de municipios
    municipios.forEach(municipio => {
        const option = document.createElement('option');
        option.value = municipio;
        option.textContent = municipio;
        // Preseleccionar el municipio si coincide con el valor actual (ignorando mayúsculas/minúsculas)
        if (municipioSeleccionado && municipio.toLowerCase() === municipioSeleccionado.toLowerCase()) {
            option.selected = true;
            console.log('Municipio seleccionado de respaldo:', municipio);
        }
        municipioSelect.appendChild(option);
    });
    
    console.log(`Se cargaron ${municipios.length} municipios para ${estadoSeleccionado}`);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initEstadosMunicipios();
});