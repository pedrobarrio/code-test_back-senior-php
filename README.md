# Pruebas de código MarketGoo

¡Gracias por interesarte por nosotros! Como parte del proceso de selección, hemos creado unas pequeñas pruebas de código que te permitirán expresar mejor de lo que eres capaz, ¡sorprendenos! :-)

Estas pruebas NO están pensadas como una especie de examen o filtro de aptitud, ¡para nada! No queremos que te sientas limitado o cohibido si alguna de las tecnologías de las pruebas no son lo tuyo, simplemente rellena o termina hasta donde puedas según las instrucciones teniendo en cuenta los siguientes puntos:

- NO miramos el estilo del código. Utiliza tu propio estilo con el que estés acostumbrado.
- NO hace falta que la prueba esté completamente realizada. Simplemente envianos lo que tengas.
- Si algún punto no lo terminas correctamente, o la prueba no hace exactamente lo que pedimos, añade comentarios para que podamos seguir el razonamiento.

Como verás por estos puntos, lo importante es cómo te enfrentas a las pruebas más que el resultado final. Cualquier cosa que nos quieras transmitir añádelas como parte de comentarios en el código.


# Prueba de PHP: Ampliar un servidor GraphQL

Tenemos un API GraphQL que nos devuelve los usuarios que tenemos en la BBDD pero se nos ha olvidado adjuntar la región donde viven estos usuarios mediante la IP que tenemos almacenada.

## Requisitos principales:

- Ampliar la respuesta del API GraphQL para añadir un campo "ip_region" de tipo string al modelo usuario.
- Este campo "ip_region" debe incluir la geolocalización de cada usuario a partir de su IP. Ejemplo: "Majadahonda, Madrid - (Spain)".
- Hay ciertas pistas de cómo queremos resolver esta prueba, búscalas. ;-)

## Requisitos alternativos (¡¡Extra puntos!!):

- Hay varias alternativas a cómo implementarlo en este test. Sé creativo y si se te ocurren varias intenta explicarlas brevemente.
- Extra Kudos si implementas Unit Tests...

## Como ejecutar y requisitos del sistema

Para hacer este test no hace falta instalar una base de datos o un servidor web. Asumimos una versión mínima de PHP 7.0, así que ese es el único requisito mínimo que debes tener.

También es indispensable tener Composer para las dependencias. Instala Composer desde https://getcomposer.org y ejecuta en el directorio de la prueba:

    $ php composer.phar install

PHP 7.0 tiene un servidor embebido para pruebas, al que se accede mediante la herramienta de comandos (CLI):

    $ php -S localhost:8080

El comando anterior ejecuta la prueba en el puerto 8080 de tu `localhost`.
