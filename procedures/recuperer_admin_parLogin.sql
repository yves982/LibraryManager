DELIMITER !!
DROP PROCEDURE IF EXISTS recuperer_admin_parLogin!!
/******************************
 * Description: Selectionne un administrateur de la table `Admin` d'aprés son login.
 * paramètre: login VARCHAR(255)
 * paramètre: mdp VARCHAR(64)
 * résultat:
 *   id INT,
 *   nom VARCHAR(255),
 *   login VARCHAR(255),
 *   mdp VARCHAR(64),
 *   email VARCHAR(400)
 *****************************/
CREATE PROCEDURE recuperer_admin_parLogin(
  login VARCHAR(255)
)
BEGIN
  SELECT `id`, `nom`, `login` as login, `mdp`, `email`
  FROM `Admin`
  WHERE `Admin`.`login` = login;
END!!
DELIMITER ;
