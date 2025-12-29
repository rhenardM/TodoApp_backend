# Documentation d'intégration API Todo (Symfony + JWT)

## 1. Authentification (connexion)

### Endpoint
POST /api/login_check

### Corps de la requête
```json
{
  "username": "email@exemple.com",
  "password": "motdepasse"
}
```

### Réponse
```json
{
  "token": "<JWT>"
}
```

Stockez ce token JWT côté front (localStorage, etc). Il sera utilisé pour toutes les requêtes suivantes.

---

## 2. Headers d'authentification

Pour chaque requête API protégée, ajoutez :

```
Authorization: Bearer <JWT>
```

---

## 3. Endpoints Todo CRUD

### a. Lister les todos
- **GET** `/api/todos`
- **Réponse** :
```json
[
  {
    "id": 1,
    "title": "Titre",
    "description": "Desc...",
    "isCompleted": false,
    "user": {
      "id": 1,
      "email": "..."
    }
  },
  ...
]
```

### b. Créer une todo
- **POST** `/api/todos`
- **Body** :
```json
{
  "title": "Titre",
  "description": "Desc..."
}
```
- **Réponse** : todo créée (voir format ci-dessus)

### c. Modifier une todo
- **PUT** `/api/todos/{id}`
- **Body** :
```json
{
  "title": "Nouveau titre",
  "description": "Nouvelle desc...",
  "isCompleted": true
}
```
- **Réponse** : todo modifiée (voir format ci-dessus)

### d. Supprimer une todo
- **DELETE** `/api/todos/{id}`
- **Réponse** : 204 No Content

---

## 4. Gestion de l'utilisateur connecté
- Après connexion, le backend filtre automatiquement les todos par utilisateur connecté.
- Aucune gestion d'ID utilisateur côté front.

---

## 5. Exemple d'utilisation avec fetch (JS)

```js
// Connexion
const login = async (email, password) => {
  const res = await fetch('http://localhost:8000/api/login_check', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: email, password })
  });
  const data = await res.json();
  localStorage.setItem('jwt', data.token);
};

// Récupérer les todos
const fetchTodos = async () => {
  const token = localStorage.getItem('jwt');
  const res = await fetch('http://localhost:8000/api/todos', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  return await res.json();
};

// Créer une todo
const createTodo = async (title, description) => {
  const token = localStorage.getItem('jwt');
  const res = await fetch('http://localhost:8000/api/todos', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({ title, description })
  });
  return await res.json();
};

// Modifier une todo
const updateTodo = async (id, updates) => {
  const token = localStorage.getItem('jwt');
  const res = await fetch(`http://localhost:8000/api/todos/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(updates)
  });
  return await res.json();
};

// Supprimer une todo
const deleteTodo = async (id) => {
  const token = localStorage.getItem('jwt');
  await fetch(`http://localhost:8000/api/todos/${id}`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${token}` }
  });
};
```

---

## 6. Points importants
- Toujours utiliser le JWT pour toutes les requêtes après connexion.
- Les todos sont automatiquement liées à l'utilisateur connecté.
- Les endpoints sont RESTful et sécurisés.

---

Pour toute question sur l'intégration, contactez le backend ou référez-vous à ce fichier.
