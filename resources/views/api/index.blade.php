<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rick & Morty API</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <style>
        .character-image:hover {
            cursor: pointer; /* Cambia el cursor a una mano cuando se pasa el mouse sobre la imagen */
        }
    </style>




    <div class="container">
        <h1>Consulta la API de Rick & Morty</h1>
        <form id="locationForm">
            <div class="form-group">
                <label for="location">ID de la Localidad:</label>
                <input type="number" class="form-control" id="location" required>
            </div>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>
        <div class="mt-4" id="characterList"></div>
    </div>

    <div class="modal fade" id="characterModal" tabindex="-1" role="dialog" aria-labelledby="characterModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="characterModalLabel">Información del Personaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="characterModalContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <div class="modal fade" id="characterModal" tabindex="-1" role="dialog" aria-labelledby="characterModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="characterModalLabel">Información del Personaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="characterModalImage" class="img-fluid" alt="Imagen del personaje">
                    <div id="characterModalContent"></div>
                </div>
            </div>
        </div>
    </div>


    <script>
    const locationForm = document.getElementById('locationForm');
    const characterList = document.getElementById('characterList');

    locationForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const locationId = document.getElementById('location').value;
        const response = await axios.get(`https://rickandmortyapi.com/api/location/${locationId}`);
        const locationData = response.data;

        // Cambio de fondo
        if (locationData.id <= 50) {
            document.body.style.backgroundColor = 'green';
        } else if (locationData.id <= 80) {
            document.body.style.backgroundColor = 'blue';
        } else {
            document.body.style.backgroundColor = 'red';
        }

        const residents = locationData.residents.slice(0, 5);

        const characters = await Promise.all(
            residents.map(async (residentUrl) => {
                const characterResponse = await axios.get(residentUrl);
                return characterResponse.data;
            })
        );

        characters.sort((a, b) => (a.name > b.name ? 1 : -1));

        //Mostrar tarjeta
        const characterHtml = characters.map((character) => `
            <div class="col-md-4 character-card">
                <div class="card mb-3">
                    <p class="card-text">
                    <img src="${character.image}" class="card-img-top character-image" alt="Imagen del personaje"
                    data-character-image="${character.image}" data-toggle="modal" data-target="#characterModal">

                    </p>
                    <div class="card-body">
                        <h5 class="card-title">${character.name}</h5>
                        <p class="card-text">Status: ${character.status}</p>
                        <p class="card-text">Species: ${character.species}</p>
                        <p class="card-text">Origin: ${character.origin.name}</p>

                        ${character.episode.slice(0, 3).map((episodeUrl) => `
                            <p class="card-text">
                                <a href="${episodeUrl}" target="_blank">Episodio</a>
                            </p>
                        `).join('')}
                    </div>
                </div>
            </div>
        `);

        characterList.innerHTML = `
            <div class="row">${characterHtml.join('')}</div>
        `;

        // Agregar evento para mostrar el modal al hacer clic en la imagen
        const characterImages = document.querySelectorAll('.character-image');
        characterImages.forEach((image) => {
            image.addEventListener('click', async () => {
                // Obtener el imageNumber
                const imageUrl = image.getAttribute('src');
                const urlParts = imageUrl.split('/');
                const lastPart = urlParts[urlParts.length - 1];
                const numberParts = lastPart.split('.');
                const imageNumber = numberParts[0];

                // Buscar el personaje correspondiente al imageNumber
                const selectedCharacter = characters.find(character => character.image.split('/').slice(-1)[0].split('.')[0] === imageNumber);

                // Mostrar los datos del personaje en el modal
                const characterModalContent = document.getElementById('characterModalContent');
                characterModalContent.innerHTML = `
                    <p>Nombre: ${selectedCharacter.name}</p>
                    <p>Status: ${selectedCharacter.status}</p>
                    <p>Especie: ${selectedCharacter.species}</p>
                `;

                $('#characterModal').modal('show'); // Mostrar el modal

                // Enviar los datos del personaje al servidor
                const postData = {
                    nombre: selectedCharacter.name,
                    status: selectedCharacter.status,
                    especie: selectedCharacter.species
                };

                try {
                    const response = await axios.post('/characters/store', postData);
                    if (response.data.message) {
                        console.log(response.data.message); // Muestra un mensaje de éxito en la consola
                    }
                } catch (error) {
                    console.error('Error al guardar los datos:', error);
                }
            });
        });



    });

    </script>

</body>
</html>
