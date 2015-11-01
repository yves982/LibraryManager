DELIMITER !!
DROP PROCEDURE IF EXISTS recuperer_film!!
/******************************
 * Description: Selectionne un film de la table `Films` d'aprés son id.
 * paramètre: id INTEGER
 * résultat:
 *   id INT,
 *   titre VARCHAR(255),
 *   realisateur VARCHAR(255),
 *   annee INTEGER,
 *   description VARCHAR(4000),
 *   contenu VARCHAR(500),
 *   image VARCHAR(4000)
 *****************************/
 CREATE PROCEDURE recuperer_film(
  id INTEGER
)
BEGIN
  SELECT `id` as id, `titre`, `realisateur`, `annee`, `description`, `contenu`, `image`
  FROM `Films`
  WHERE `Films`.`id` = id;
END!!
