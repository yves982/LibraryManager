DELIMITER !!
DROP PROCEDURE IF EXISTS recuperer_bloc_film_parPremiereLettre!!
/******************************
 * Description: Selectionne un groupe de films de la table `Films` d'aprés leur premiere lettre la taille maximum et le dernier element chargé.
 * paramètre: premiereLettre CHAR(1)
 * paramètre: startInd INT
 * paramètre: endInd INT
 * paramètre: nbElementsTot INT OUT
 * résultat:
 *   id INT,
 *   titre VARCHAR(255),
 *   realisateur VARCHAR(255),
 *   annee INTEGER,
 *   description VARCHAR(4000),
 *   contenu VARCHAR(500),
 *   image VARCHAR(4000)
 *****************************/
 CREATE PROCEDURE recuperer_bloc_film_parPremiereLettre(
  premiereLettre CHAR(1),
  startInd INT,
  endInd INT,
  OUT nbElementsTot INTEGER
)
BEGIN
  SELECT `id`, `titre`, `realisateur`, `annee`, `description`, `contenu`, `image`
  FROM `Films`
  WHERE substring(`Films`.`titre`, 1, 1) = premiereLettre
  ORDER BY `Films`.`id`
  LIMIT startInd, endInd;

  SET nbElementsTot =(SELECT count(*)
  FROM `Films`
  WHERE substring(`Films`.`titre`, 1, 1) = premiereLettre);
END!!

