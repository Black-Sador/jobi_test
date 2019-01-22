# Initialisation locale
- `composer install`
- `php bin/console server:run`
- Accéder à `http://127.0.0.1:8000/jobs`

# Historique de production
- 40 minutes pour trouver / comprendre les calls API
- 1 heure pour rendre fonctionnel l'accès à l'API et avoir un retour concret
- 20 minutes pour mettre en place symfony.
- 1 heure pour mettre en place les deux call API, dans un controller qui appelle une vue, avec le display du resultat.
- 5 minutes pour se demander ce qu'est Twig.
- 10 minutes pour écrire le readme.

# Ce qui a mis du temps / n'est pas fait
- Pas eu le temps de trouver le flag de call API pour trier les résultats, du plus récent au moins récent (ou même de le faire dans le controller)
- Pas eu le temps de faire une page propre
- Utilisation d'un CDN pour plus de rapidité (proscrit habituellement)
- Mauvaise gestion du call d'authentification. En temps normal, le token serait stocké, et de ce fait, le call serait plus rapide.