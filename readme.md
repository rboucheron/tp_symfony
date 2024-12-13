

# TP Symfony - Raphaël BOUCHERON

## Étapes d'installation



Clonez le projet à l'aide de la commande suivante :


```bash
git clone https://github.com/rboucheron/tp_symfony
```



Une fois le projet récupéré, installez les dépendances nécessaires avec Composer :

```bash
composer install
```



Démarrez la base de données avec Docker Compose :

```bash
docker compose up
```



Créez les clés privée et publique pour le chiffrement des tokens JWT :

```bash
php bin/console lexik:jwt:generate-keypair
```



Appliquez les migrations pour créer les tables nécessaires dans la base de données :

```bash
php bin/console doctrine:migrations:migrate
```


Démarrez un serveur local Symfony pour tester l'application :

```bash
symfony server:start
```

---

Une fois ces étapes terminées, vous pouvez accéder à l'application à l'adresse :

[http://localhost:8000](http://localhost:8000)



## API Reference

#### Crée un USER :

```http
  POST /api/signin
```

Dans le corps de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `email` | `string` | jhone.doe@mail.fr |
| `name` | `string` | jhone doe |
| `password` | `string` | azerty1345::3355 |
| `phone` | `string` | 07 08 09 03 08 |


#### Crée un ADMIN :

```http
  POST /api/signin/admin
```

Dans le corps de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `email` | `string` | admin@mail.fr |
| `name` | `string` | admin |
| `password` | `string` | azerty1345::3355 |
| `phone` | `string` | 06 15 04 19 08 |


#### Authentification :

```http
  POST /api/auth
```
Dans le corps de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `email` | `string` | jhone.doe@mail.fr |
| `password` | `string` | azerty1345::3355 |

#### Voir son profil :

```http
  GET /api/manage/account
```
Dans le header de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `Authorization` | `JWT` | Bearer <exemple jwt > |


#### Modifier son profil :

```http
  PUT /api/manage/account
```
Dans le header de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `Authorization` | `JWT` | Bearer <exemple jwt > |

Dans le corps de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `email` | `string` | jhone.doe@mail.fr |
| `password` | `string` | azerty1345::3355 |

#### Crée une Reservation :

```http
  POST /api/reservations
```
Dans le header de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `Authorization` | `JWT` | Bearer <exemple jwt > |

Dans le corps de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `date` | `string` | `2024-12-15` |
| `timeSlot` | `string` | `14:00-16:00` |
| `eventName` | `string` | `Conférence sur l'innovation technologique` |

#### Voir ces propres réservations :

```http
  GET /api/reservations
```
Dans le header de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `Authorization` | `JWT` | Bearer <exemple jwt > |


#### Voir toutes les réservations :

```http
  GET /api/all/reservations
```
Dans le header de la Requéte :

| Parametre | Type     | Exemple               |
| :-------- | :------- | :------------------------- |
| `Authorization` | `JWT` | Bearer <exemple jwt > |
