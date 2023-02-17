# Prueba de PHP: Ampliar un servidor GraphQL

## **Implementación**

Se han actualizado las librerías, separado la de testing para que solamente estén en el entorno de DEV. 
Se ha optado por separar en un servicio la localización para poder inyectarla y construir la respuesta que se requería en la prueba.

En este caso, hay dos alternativas dado que al ser gratuítas ofrecían limitaciones en cuanto al número de peticiones. Así a lo largo del desarrollo, se puede evitar, en la medida de los posible que nos quedemos sin peticiones. Se ha contemplado no obstante el devolver unos valores válidos en caso de que sucediera.

Se han hecho test Funcionales para comprobar que se devuelven los datos que se pedían. 

## ** ToDo **

Comprobar que el input de los datos sigue siempre el mismo formato. 
Se podría optar por algún tipo de schema que se validara como dato de entrada. 




