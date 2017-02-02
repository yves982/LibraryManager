DELIMITER !!
DROP PROCEDURE IF EXISTS supprimer_film!!
/******************************
 * Description: Supprime un fim de la table `Films`
 * paramètre: id INTEGER
 *****************************/
CREATE PROCEDURE supprimer_film(
  id INTEGER
)
BEGIN
  DELETE
  FROM `Films`
  WHERE `Films`.`id` = id;
END!!
DELIMITER ;
