<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Clientes Inactivos - GYM Habibi</title>
</head>
<body class="bg-amber-100/20 font-sans flex h-screen">
  
<?php include '../src/components/sideBar.php'; ?>

<main class="flex-grow">
    <div class="p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Clientes Inactivos</h1>
            <div class="flex space-x-4">
                <button id="reactivarSeleccionados" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-md duration-300 hidden">
                    Reactivar Seleccionados
                </button>
                <button onclick="actualizarInactivos()" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-md duration-300">
                    🔄 Actualizar Lista
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-red-100 rounded-lg p-4 border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-red-800">Total Inactivos</h3>
                    <span id="totalInactivos" class="text-2xl font-bold text-red-600">0</span>
                </div>
            </div>
            <div class="bg-yellow-100 rounded-lg p-4 border-l-4 border-yellow-500">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-yellow-800">Sin Pago +60 días</h3>
                    <span id="inactivos60" class="text-2xl font-bold text-yellow-600">0</span>
                </div>
            </div>
            <div class="bg-orange-100 rounded-lg p-4 border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-orange-800">Sin Pago +90 días</h3>
                    <span id="inactivos90" class="text-2xl font-bold text-orange-600">0</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">

            <?php include '../src/components/buscar.php'; ?>

            <div class="overflow-x-auto">
                <table id="inactivosTable" class="w-full">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs text-red-800 uppercase font-semibold">
                                <input type="checkbox" id="selectAll" class="rounded" />
                            </th>
                            <th class="px-6 py-4 text-left text-xs text-red-800 uppercase font-semibold">Nombre</th>
                            <th class="px-6 py-4 text-left text-xs text-red-800 uppercase font-semibold">Apellido</th>
                            <th class="px-6 py-4 text-left text-xs text-red-800 uppercase font-semibold">DNI</th>
                            <th class="px-6 py-4 text-left text-xs text-red-800 uppercase font-semibold">Último Pago</th>
                            <th class="px-6 py-4 text-left text-xs text-red-800 uppercase font-semibold">Días sin Pago</th>
                            <th class="px-6 py-4 text-left text-xs text-red-800 uppercase font-semibold">Motivo</th>
                            <th class="px-6 py-4 text-left text-xs text-red-800 uppercase font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Datos dinámicos aquí -->
                    </tbody>
                </table>
            </div>

            <!-- Mensaje cuando no hay datos -->
            <div id="noDataMessage" class="hidden p-8 text-center text-gray-500">
                <div class="text-6xl mb-4">🎉</div>
                <h3 class="text-xl font-semibold mb-2">¡No hay clientes inactivos!</h3>
                <p>Todos los clientes están al día con sus pagos.</p>
            </div>
        </div>
    </div>
</main>

<!-- Modal de Confirmación -->
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Reactivación</h3>
            <p class="text-sm text-gray-500 mb-6">¿Estás seguro de que quieres reactivar a este cliente?</p>
            <div class="flex justify-center space-x-4">
                <button id="cancelReactivar" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Cancelar</button>
                <button id="confirmarReactivar" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Reactivar</button>
            </div>
        </div>
    </div>
</div>

</body>

<script>
let clientesInactivos = [];
let clienteAReactivar = null;

// Cargar datos de clientes inactivos
async function cargarInactivos() {
    try {
        const response = await fetch('data/clientes_inactivos.json');
        if (!response.ok) {
            throw new Error('No se pudo cargar el archivo de clientes inactivos');
        }
        const data = await response.json();
        clientesInactivos = data;
        renderizarTabla(data);
        actualizarEstadisticas(data);
    } catch (error) {
        console.error(error);
        document.getElementById('noDataMessage').classList.remove('hidden');
    }
}

function renderizarTabla(clientes) {
    const tbody = document.querySelector('#inactivosTable tbody');
    tbody.innerHTML = '';

    if (clientes.length === 0) {
        document.getElementById('noDataMessage').classList.remove('hidden');
        return;
    } else {
        document.getElementById('noDataMessage').classList.add('hidden');
    }

    clientes.forEach(cliente => {
        const tr = document.createElement('tr');

        const diffDays = calcularDiasSinPago(cliente.ultimo_pago);

        tr.innerHTML = `
            <td class="px-4 py-4 text-left"><input type="checkbox" class="selectCliente" data-dni="${cliente.dni}" /></td>
            <td class="px-6 py-4">${cliente.nombre}</td>
            <td class="px-6 py-4">${cliente.apellido}</td>
            <td class="px-6 py-4">${cliente.dni}</td>
            <td class="px-6 py-4">${cliente.ultimo_pago}</td>
            <td class="px-6 py-4">${diffDays}</td>
            <td class="px-6 py-4">${cliente.motivo || ''}</td>
            <td class="px-6 py-4">
                <button class="bg-green-500 hover:bg-green-700 text-white text-xs py-1 px-3 rounded-md" onclick="confirmarReactivar('${cliente.dni}')">Reactivar</button>
            </td>
        `;

        tbody.appendChild(tr);
    });

    actualizarBotonReactivarSeleccionados();
}

function actualizarEstadisticas(clientes) {
    const total = clientes.length;
    const inactivos60 = clientes.filter(c => calcularDiasSinPago(c.ultimo_pago) > 60).length;
    const inactivos90 = clientes.filter(c => calcularDiasSinPago(c.ultimo_pago) > 90).length;

    document.getElementById('totalInactivos').textContent = total;
    document.getElementById('inactivos60').textContent = inactivos60;
    document.getElementById('inactivos90').textContent = inactivos90;
}

function calcularDiasSinPago(fechaPago) {
    const hoy = new Date();
    const ultimoPago = new Date(fechaPago);
    const diffTime = hoy - ultimoPago;
    return Math.floor(diffTime / (1000 * 60 * 60 * 24));
}

function confirmarReactivar(dni) {
    clienteAReactivar = clientesInactivos.find(c => c.dni === dni);
    document.getElementById('confirmModal').classList.remove('hidden');
}

document.getElementById('cancelReactivar').addEventListener('click', () => {
    clienteAReactivar = null;
    document.getElementById('confirmModal').classList.add('hidden');
});

document.getElementById('confirmarReactivar').addEventListener('click', () => {
    if (clienteAReactivar) {
        reactivarCliente(clienteAReactivar);
        clienteAReactivar = null;
        document.getElementById('confirmModal').classList.add('hidden');
    }
});

function reactivarCliente(cliente) {
    // Aquí deberías hacer la llamada al backend para actualizar el estado del cliente.
    // Como ejemplo, solo eliminamos el cliente del arreglo y actualizamos la tabla.
    alert(`Cliente ${cliente.nombre} ${cliente.apellido} reactivado!`);

    clientesInactivos = clientesInactivos.filter(c => c.dni !== cliente.dni);
    renderizarTabla(clientesInactivos);
    actualizarEstadisticas(clientesInactivos);
}

function actualizarInactivos() {
    cargarInactivos();
}

// Funcionalidad para checkbox seleccionar todo y mostrar botón reactivar seleccionados
document.getElementById('selectAll').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.selectCliente').forEach(cb => cb.checked = checked);
    actualizarBotonReactivarSeleccionados();
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('selectCliente')) {
        actualizarBotonReactivarSeleccionados();
    }
});

function actualizarBotonReactivarSeleccionados() {
    const anyChecked = Array.from(document.querySelectorAll('.selectCliente')).some(cb => cb.checked);
    document.getElementById('reactivarSeleccionados').classList.toggle('hidden', !anyChecked);
}

// Aquí puedes implementar la función para reactivar varios seleccionados (si quieres)
document.getElementById('reactivarSeleccionados').addEventListener('click', () => {
    const seleccionados = Array.from(document.querySelectorAll('.selectCliente:checked')).map(cb => cb.dataset.dni);
    if (seleccionados.length === 0) return;

    if (!confirm(`¿Deseas reactivar ${seleccionados.length} cliente(s) seleccionados?`)) return;

    seleccionados.forEach(dni => {
        const cliente = clientesInactivos.find(c => c.dni === dni);
        if (cliente) {
            clientesInactivos = clientesInactivos.filter(c => c.dni !== dni);
            // Aquí harías la llamada al backend para reactivar cada cliente
        }
    });
    alert(`${seleccionados.length} cliente(s) reactivados!`);
    renderizarTabla(clientesInactivos);
    actualizarEstadisticas(clientesInactivos);
});

window.onload = () => {
    cargarInactivos();
};
</script>

</html>
