
DELIMITER !!
DROP PROCEDURE IF EXISTS recuperer_film_parPremiereLettre!!
/******************************
 * Description: Selectionne des films de la table `Films` d'aprés leur premiere lettre.
 * paramètre: premiereLettre CHAR(1)
 * résultat:
 *   id INT,
 *   titre VARCHAR(255),
 *   realisateur VARCHAR(255),
 *   annee INTEGER,
 *   description VARCHAR(4000),
 *   contenu VARCHAR(500),
 *   image VARCHAR(4000)
 *****************************/
 CREATE PROCEDURE recuperer_film_parPremiereLettre(
  premiereLettre CHAR(1)
)
BEGIN
  SELECT `id`, `titre`, `realisateur`, `annee`, `description`, `contenu`, `image`
  FROM `Films`
  WHERE substring(`Films`.`titre`, 1, 1) = premiereLettre 
  ORDER BY `Films`.`id`;
END!!

