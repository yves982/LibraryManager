DELIMITER !!
DROP PROCEDURE IF EXISTS creer_films_liste!!
/******************************
 * Description: Créer une liste de films d'aprés leurs ids.
 * paramètre: id INT OUT identifiant de la liste crée ,
 * paramètre: ids VARCHAR(4000) liste d'identifiants de films séparés par ,
 *****************************/
 CREATE PROCEDURE creer_films_liste(
  OUT id INTEGER,
  ids VARCHAR(4000)
)
BEGIN
    

    IF instr(ids, ',')<>0 THEN
        SET @query = CONCAT('
            INSERT INTO ListeFilms (idFilm)
            SELECT id
            FROM Films
            WHERE Films.id in (', substr(ids, 1, instr(ids, ',')-1), ')'
         );

        PREPARE stmt FROM @query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        SET @id = last_insert_id();

        SET @query = CONCAT('
            INSERT INTO ListeFilms (id, idFilm)
            SELECT ', @id, ',id
            FROM Films
            WHERE Films.id in (',substr(ids, instr(ids, ',')+1),')'
        );

        PREPARE stmt2 FROM @query;
        EXECUTE stmt2;
        DEALLOCATE PREPARE stmt2;
    ELSEIF char_length(ids)>0 THEN
        INSERT INTO ListeFilms (idFilm)
        VALUES(ids);
    END IF;

    SET id = last_insert_id();
END!!
DELIMITER ;