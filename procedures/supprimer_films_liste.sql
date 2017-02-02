DELIMITER !!
DROP PROCEDURE IF EXISTS supprimer_films_liste!!
/******************************
 * Description: Supprimes des films de la table `Films` d'aprés leurs ids.
 * paramètre: id INT identifiant de la liste de films à supprimer
 *****************************/
 CREATE PROCEDURE supprimer_films_liste(
  id INTEGER
)
BEGIN
    DELETE Films
    FROM Films
    INNER JOIN ListeFilms on Films.id=ListeFilms.idFilm
    WHERE ListeFilms.id=id;
END!!

