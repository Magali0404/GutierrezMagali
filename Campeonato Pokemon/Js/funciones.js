let ids = []; //almacenará los 6 ids aleatorios.
let mostrar = [];
let result = [];
let tarjetas = document.getElementById("mostrarPokemon"); //busca un elemento HTML que tenga el id "mostrarPokemon".

async function campeonato() {
    for (let index = 0; index < 6; index++) {
        let numeroAleatorio = Math.floor(Math.random() * (1025)) + 1; /*math.random genera un numero 
        aleatorio entre 0 y 1, lo multiplicamos por 1025 que son la cantidad de pokemones en la api(numero maximo),
        math.floor redondea un numero hacia abajo y sumamos 1 para que el ID empiece desde 1.
        */
        ids.push(numeroAleatorio) //agrega los numeros aleatorios en el array ids.
    }
    console.log(ids);

    const pedidos = ids.map(id => //.map() recorre cada elemento de ids, regresa valores a pedidos.
        fetch("https://pokeapi.co/api/v2/pokemon/"+id) //petición HTTP (fetch) a la pokeAPI para obtener los datos del Pokémon correspondiente.
            .then(response => response.json()) //convierte cada respuesta en formato JSON.
    )
    Promise.all(pedidos) //promise tiene todas las promesas del fetch (pedidos), en este caso se usa porque guarde las promesas en una variable.
        .then(data => { 
            result = data //guarda los datos en result.
            console.log(result)
            mostrar = funcMostrar(result); //llama a la funcion funcMostrar para mostrar los pokemones.
            //batalla(result);
        })
}

function funcMostrar(result) {
    let tarjetaHtml = ``; 
    let index = 0; 
    result.forEach(res => { //cuenta los pokemones y los reparte en 2 equipos.
        let pokeImagen = res.sprites.front_default ; //guarda el url de la imagen. 
        if (index < 3) { //si el indice es menor a 3 el pokemon va al primer equipo.
                        //se arman las tarjetas.
            tarjetaHtml = ` 
            <div class="tarjetaUno"> 
                <h3>Equipo 1:</h3>
                <h3>Nombre: ${res.name}</h3>
                <p>Ataque: ${res.stats[1].base_stat}</p>
                <p>Defensa: ${res.stats[2].base_stat}</p>
                <img src="${pokeImagen}">
            </div>
            `
        } else {
            //si el indice es mayor a 3 el pokemon va al segundo equipo.
            tarjetaHtml = `
            <div class="tarjetaDos">    
                <h3>Equipo 2:</h3>
                <h3>Nombre: ${res.name}</h3>
                <p>Ataque: ${res.stats[1].base_stat}</p>
                <p>Defensa: ${res.stats[2].base_stat}</p>
                <img src="${pokeImagen}">
            </div>
            `
        }
        index++ //se incrementa el indice.
        tarjetas.innerHTML += tarjetaHtml; //a tarjetas (hace referencia a un id) se le agrega internamente el contenido de tarjeta html.
    });
}

function batalla(result) {
    const resultado = document.getElementById('resultado'); //obtiene el contenedor donde se mostrarán los resultados de la batalla (id="resultado").
    let ataqueEquipo1 = 0;
    let ataqueEquipo2 = 0;
    let defensaEquipo1 = 0;
    let defensaEquipo2 = 0;
    let index = 0;

    result.forEach(res => {
        if (index < 3) {
            ataqueEquipo1 += res.stats[1].base_stat; //suma total de los ataques del equipo 1.
            defensaEquipo1 += res.stats[2].base_stat;//suma total de la defensa del equipo 1.
        } else {
            ataqueEquipo2 += res.stats[1].base_stat; //suma total de los ataques del equipo 2.
            defensaEquipo2 += res.stats[2].base_stat;//suma total de la defensa del equipo 2.
        }
        index++
    });

    const dañoTotal1 = ataqueEquipo1 - defensaEquipo2;//daño total del ataque menos la defensa enemiga.
    const dañoTotal2 = ataqueEquipo2 - defensaEquipo1;
    
    if (dañoTotal1 > dañoTotal2) {
        // hace referencia al id "resultado" y le cambia el html interno.
        //armo las tarjetas resultantes de la batalla.
    resultado.innerHTML = ` 
        <div class="contenedorResultados">
            <div class="resultadoUno">
                <h3>⚔️Equipo 1:</h3>
                <p>Ataque: ${ataqueEquipo1}</p>
                <p>Defensa: ${defensaEquipo1}</p>
                <p>Resultado: ${dañoTotal1}</p>
            </div>
            <div class="resultadoDos">
                <h3>⚔️Equipo 2:</h3>
                <p>Ataque: ${ataqueEquipo2}</p>
                <p>Defensa: ${defensaEquipo2}</p>
                <p>Resultado: ${dañoTotal2}</p>
            </div>
        </div>
        <h1 class="ganadorTitulo">🏅 Equipo 1 es el ganador</h1>
        `
    } else {
    resultado.innerHTML = `
        <div class="contenedorResultados">
            <div class="resultadoUno">
                <h3>⚔️Equipo 1:</h3>
                <p>Ataque: ${ataqueEquipo1}</p>
                <p>Defensa: ${defensaEquipo1}</p>
                <p>Resultado: ${dañoTotal1}</p>
            </div>
            <div class="resultadoDos">
                <h3>⚔️Equipo 2:</h3>
                <p>Ataque: ${ataqueEquipo2}</p>
                <p>Defensa: ${defensaEquipo2}</p>
                <p>Resultado: ${dañoTotal2}</p>
            </div>
        </div>
        <h1 class="ganadorTitulo">🏅 Equipo 2 es el ganador</h1>
        `
    }
}
campeonato();