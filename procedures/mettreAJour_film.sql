DELIMITER !!
DROP PROCEDURE IF EXISTS mettreAJour_film!!
/******************************
 * Description: Mets à jour un film de la table `Films` d'aprés son id.
 c
 * paramètre: titre VARCHAR(255) OPTIONNEL,
 * paramètre: realisateur VARCHAR(255) OPTIONNEL,
 * paramètre: annee INTEGER OPTIONNEL,
 * paramètre: description VARCHAR(4000) OPTIONNEL,
 * paramètre: contenu VARCHAR(500) OPTIONNEL
 * paramètre: image VARCHAR(4000) OPTIONNEL
 *****************************/
CREATE PROCEDURE mettreAJour_film(
  id INTEGER,
  titre VARCHAR(255),
  realisateur VARCHAR(255),
  annee INTEGER,
  description VARCHAR(4000),
  contenu VARCHAR(500),
  image VARCHAR(4000)
)
BEGIN
  IF COALESCE(id, titre, realisateur, annee, description, contenu) IS NULL THEN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT='Tous les parametres ne peuvent être null en même temps.';
  END IF;

  UPDATE `Films`
  SET
    titre = IFNULL(titre,Films.titre),
    realisateur = IFNULL(realisateur ,Films.realisateur ),
    description = IFNULL(description , Films.description),
    contenu = IFNULL(contenu, Films.contenu),
    annee = IFNULL(annee, Films.annee),
    image = IFNULL(image, Films.image)
  WHERE `Films`.`id` = id;
END!!
DELIMITER ;
