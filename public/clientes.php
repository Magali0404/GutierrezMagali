<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Clientes</title>
</head>
<body class="bg-amber-100/20 font-sans flex h-screen">
  
<?php include '../src/components/sideBar.php'; ?>

  <main class="flex-grow">
    <div class="p-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Lista de Clientes</h1>
      </div>

      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table id="clientesTable" class="w-full">

<?php include '../src/components/buscar.php'; ?>

          <thead class="bg-white">
            <tr>
              <th class="px-6 py-4 text-left text-xs text-black uppercase">Nombre</th>
              <th class="px-6 py-4 text-left text-xs text-black uppercase">Apellido</th>
              <th class="px-6 py-4 text-left text-xs text-black uppercase">DNI</th>
              <th class="px-6 py-4 text-left text-xs text-black uppercase">Celular</th>
              <th class="px-6 py-4 text-left text-xs text-black uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
    <script>
      fetch('data/clientes.json')
      .then(response => {
        if (!response.ok) {
          throw new Error("No se pudo cargar el JSON");
        }
        return response.json();
      })
      .then(data => {
        console.log(data);

        const tbody = document.querySelector("#clientesTable tbody");
        tbody.innerHTML = '';

        data.forEach((cliente, index) => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td class="px-6 py-4 text-sm text-gray-700">${cliente.nombre}</td>
            <td class="px-6 py-4 text-sm text-gray-700">${cliente.apellido}</td>
            <td class="px-6 py-4 text-sm text-gray-700">${cliente.dni}</td>
            <td class="px-6 py-4 text-sm text-gray-700">${cliente.telefono}</td>
            <td class="px-6 py-4">
              <div class="space-x-2">
                <button class="bg-yellow-400 hover:bg-yellow-700 text-white text-xs py-1 px-3 rounded-md duration-300" onclick="editarCliente(${index})">Editar</button>
                <button class="bg-red-500 hover:bg-red-700 text-white text-xs py-1 px-3 rounded-md duration-300" onclick="eliminarCliente(${index})">Eliminar</button>
                <button class="bg-green-500 hover:bg-green-700 text-white text-xs py-1 px-3 rounded-md duration-300" onclick="verPagos(${index})">Ver Pagos</button>
              </div>
            </td>
          `;
          tbody.appendChild(row);
        });

      })
      .catch(error => {
        console.error("Error al cargar o procesar el JSON:", error);
      });
    </script>
</html>