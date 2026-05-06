# Documentació del projecte

## Fitxa del projecte

**Nom:** Agència ProTravel  
**UF:** UF1845 - Accés a dades en aplicacions web de l’entorn servidor  
**Objectiu:** Crear una aplicació web que mostri paquets en promoció i gestioni el procés de pre-reserva, validació del proveïdor i formalització.

## Requisits funcionals

1. Mostrar paquets en promoció.
2. Mostrar detall d’una promoció amb preus base i suplements.
3. Permetre al client enviar una pre-reserva.
4. Permetre informar dades del client.
5. Permetre informar dades específiques de cada viatger.
6. Calcular el total de reserva segons adults, nens i suplements.
7. Crear reserva en estat `PRE_RESERVA`.
8. Notificar el proveïdor quan es crea la pre-reserva.
9. Permetre a l’administrador acceptar o rebutjar la reserva.
10. Permetre formalitzar la reserva quan estigui acceptada.
11. Notificar novament el proveïdor quan queda formalitzada.

## Requisits no funcionals

1. Ús de PDO i consultes preparades.
2. Separació de responsabilitats entre models, controladors, serveis i vistes.
3. Validació de dades al servidor.
4. Protecció CSRF en formularis de canvi d’estat i login.
5. Disseny responsive.
6. Connexió configurable per MAMP/XAMPP.

## Model de dades resumit

- **proveidors:** dades de contacte del majorista.
- **paquets:** informació general del viatge.
- **promocions:** dates i preus específics d’un paquet.
- **clients:** dades de la persona que fa la reserva.
- **reserves:** capçalera de reserva, estat, dates i totals.
- **viatgers:** persones associades a una reserva.
- **usuaris:** accés intern al panell d’administració.

## Mòduls

### Mòdul públic

- Home de promocions.
- Fitxa de promoció.
- Formulari de pre-reserva.
- Pantalla de confirmació.

### Mòdul administració

- Login.
- Dashboard.
- Llistat de reserves.
- Fitxa de reserva.
- Accions d’acceptació, rebuig i formalització.

### Mòdul de notificació

El servei `ProveedorNotifier` genera una entrada en un fitxer de log per simular l’enviament de correus al proveïdor.

## Flux d’estats

```text
PRE_RESERVA → ACCEPTADA → FORMALITZADA
      └────→ REBUTJADA
```

## Planificació Agile proposada

### Sprint 1

- Definició d’entitats.
- Disseny de base de dades.
- Creació de dades inicials.

### Sprint 2

- Catàleg públic de promocions.
- Detall de promoció.
- Formulari de pre-reserva.

### Sprint 3

- Alta de reserva y viatgers.
- Càlcul de totals.
- Notificació al proveïdor.

### Sprint 4

- Backoffice.
- Login intern.
- Gestió d’estats.
- Proves i documentació.

## Proves recomanades

1. Entrar a la home i veure promocions actives.
2. Obrir una promoció.
3. Fer una pre-reserva amb 1 adult.
4. Fer una pre-reserva amb 2 viatgers i suplements.
5. Validar que el total es calcula correctament.
6. Accedir al panell intern.
7. Acceptar una reserva.
8. Formalitzar una reserva acceptada.
9. Revisar el fitxer `storage/logs/provider-mails.log`.
