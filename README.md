Hello !
Pour faire marcher la machine il faut :
1 - Executer composer install à la racine du repertoire
2 - Executer bin/console doctrine:schema:update --force (ou php bin/console doctrine:schema:update --force sur Windows) pour créer le modèle de données
3 - Executer bin/console doctrine:fixtures:load (ou php bin/console ... sur Windows) pour générer les données de test
4 - ???
5 - Profiter de l'app
