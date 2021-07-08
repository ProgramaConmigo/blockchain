# blockchain

Bienvenido a mi proyecto de blockchain sobre PHP

Este cumplira la función de mostrar de forma simple el funcionamiento esencial de una Blockchain de transferencias semejante a BTC.

Puedes ver el proceso de programación en mi canal de Twitch https://www.twitch.tv/programaconmigo


COMO INSTALARLA

 1 - Cuando clones el repositorio, ten en cuenta de confirmar que tu servidor web tenga los permisos necesarios.
 2 - En public/index.php se define la ruda de la carpeta donde se almacenaran las wallets y la base de datos. Crea una con permisos deonde quieras y pon ahi la ruta.
 3 - Una vez montado, ya podras acceder al directorio public desde tu servidor web, y crear una cuenta nueva. Cada cuenta se identifica con un hash que se genera con la clave publica.
 4 - Airdrop inicial. Crea un fichero dentro del directorio de la base de datos llamado Block. Este ha de contenter un texto como este, en el cual pondras el hash den el campo destination, y la cantidad de monedas en amount:
 [{"source" : "0000000000000000000000000000000000000000000000000000000000000000","destination":"HAS CUENTA DESTINO","amount":"1000000000000"}]

Y así ya tocarias tener funcional tu Blockchain.

Gracias :D
