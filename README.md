# Projecte Agencia de Viatges - Versio PRO corregida

## Configuracio BBDD inclosa

El projecte ve preparat per a MAMP:

```php
'host' => '127.0.0.1',
'port' => '8889',
'dbname' => 'agencia_viatges',
'user' => 'root',
'password' => 'root',
'charset' => 'utf8mb4',
```

## Instal·lacio (Mode Terminal - Recomanat)

Hem afegit eines per executar el projecte fàcilment des del terminal sense necessitat de configurar l'Apache de MAMP/XAMPP.

1. Assegura't de tenir el servei MySQL de MAMP/XAMPP funcionant (o qualsevol altre MySQL).
2. Obre un terminal a la carpeta del projecte.
3. Executa les migracions de la base de dades (crearà la BD i les taules):
   ```bash
   php cli.php migrate
   ```
4. Inicia el servidor de desenvolupament:
   ```bash
   php cli.php serve
   ```
5. Obre al navegador: http://localhost:8000

## Instal·lacio (Mode Tradicional)

1. Copia la carpeta del projecte dins la carpeta web de MAMP/XAMPP.
2. Obre phpMyAdmin o MySQL Workbench.
3. Executa el fitxer complet:

```text
database/agencia_viatges.sql
```

Important: executa el fitxer complet des del principi. El fitxer fa `DROP DATABASE IF EXISTS agencia_viatges` i la torna a crear per evitar errors de claus foranes amb taules antigues.

4. Obre al navegador (ajusta la ruta segons la teva carpeta):

```text
http://localhost:8888/nom_carpeta/public/
```

## Usuari administrador

```text
admin@agencia.test
admin123
```

## Si utilitzes XAMPP

Canvia `app/config/database.php`:

```php
'port' => '3306',
'user' => 'root',
'password' => '',
```
