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

## Instal·lacio

1. Copia la carpeta del projecte dins la carpeta web de MAMP/XAMPP.
2. Obre phpMyAdmin o MySQL Workbench.
3. Executa el fitxer complet:

```text
database/agencia_viatges.sql
```

Important: executa el fitxer complet des del principi. El fitxer fa `DROP DATABASE IF EXISTS agencia_viatges` i la torna a crear per evitar errors de claus foranes amb taules antigues.

4. Obre al navegador:

```text
http://localhost:8888/projecte_agencia_viatges_php_PRO_v2_BDD_FIX/public/
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
