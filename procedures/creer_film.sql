DELIMITER !!
DROP PROCEDURE IF EXISTS creer_film!!
/******************************
 * Description: Ajoute un film dans la table `Films`
 * paramètre: id INTEGER OUT
 * paramètre: titre VARCHAR(255)
 * paramètre: realisateur VARCHAR(255)
 * paramètre: annee INTEGER
 * paramètre: description VARCHAR(4000)
 * paramètre: contenu VARCHAR(500)
 * paramètre: image VARCHAR(4000)
 *****************************/
CREATE PROCEDURE creer_film(
  OUT id INTEGER,
  titre VARCHAR(255),
  realisateur VARCHAR(255),
  annee INTEGER,
  description VARCHAR(4000),
  contenu VARCHAR(500),
  image VARCHAR(4000)
)
BEGIN
  INSERT INTO Films (`titre`, `realisateur`, `annee`, `description`, `contenu`, `image`)
  VALUE (titre, realisateur, annee, description, contenu, image);

  SET id = last_insert_id();
END!!
DELIMITER ;
