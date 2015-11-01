DELIMITER !!
DROP PROCEDURE IF EXISTS recuperer_films_liste!!
/******************************
 * Description: Selectionne une liste de films d'aprés son id.
 * paramètre: id INT
 * résultat:
 *   id INT, idFim INT
 *****************************/
 CREATE PROCEDURE recuperer_films_liste(
  id INTEGER
)
BEGIN
    SELECT id, idFilm
    FROM ListeFilms
    WHERE ListeFilms.id=id;
END!!
