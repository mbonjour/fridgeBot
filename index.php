<?php

/*///////////////////////////////////////////////////////////
// NE PAS TOUCHER
///////////////////////////////////////////////////////////*/
file_put_contents("last-message.json", json_encode($_GET)."\n\n".file_get_contents("php://input"));
include(__DIR__."/../../vendor/autoload.php");
use MessengersIO\App;
use MessengersIO\Component\Element;
use MessengersIO\Message\Button;
use MessengersIO\Message\CallbackMessage;
use MessengersIO\Message\GalleryMessage;
use MessengersIO\Message\ImageMessage;
use MessengersIO\Message\ListMessage;
use MessengersIO\Message\LocationMessage;
use MessengersIO\Message\Message;
use MessengersIO\Message\TextMessage;
use MessengersIO\Thread;
$config = json_decode(file_get_contents(__DIR__.'/conf.json'));
$app = new App($config->apiKey);
/*///////////////////////////////////////////////////////////

   _____ _           ______ _         _   _            _
  |_   _| |          | ___ (_)       | | | |          | |
    | | | |__   ___  | |_/ /_  __ _  | |_| | __ _  ___| | __
    | | | '_ \ / _ \ | ___ \ |/ _` | |  _  |/ _` |/ __| |/ /
    | | | | | |  __/ | |_/ / | (_| | | | | | (_| | (__|   <
    \_/ |_| |_|\___| \____/|_|\__, | \_| |_/\__,_|\___|_|\_\
                               __/ |
                              |___/

	Bienvenue au big hack! Vous pouvez éditer tout ce qui est ci-dessous.
	Faites des copier-coller, expérimentez, salissez-vous les mains, etc.

	Nous sommes à votre disposition au TechBar.

*/


/*
 * DEFINITION DE L'ETAT PAR DEFAUT
 */
$app->setDefaultState("WELCOME");


/*
 * ETAT: WELCOME
 *
 * Ceci est également l'état par défaut (car nommé comme ci-dessus)
 * Répondre un texte de bienvenue, et rediriger au niveau suivant
 */
$app->state("WELCOME", function(Thread $thread,  Message $message){

	// On envoie directement une image
	$thread->send(new ImageMessage("https://www.creageneve.com/wp-content/uploads/2017/07/bighack_cover_dev.jpg"));

	// Création d'un texte
	$answer = new TextMessage("Bienvenue au Big Hack!");

	// Ajout de deux boutons
	$answer->addButton(new Button("Merci!"));
	$answer->addButton(new Button("C'est parti"));

	// Changer vers un autre état (appliqué lors d'un envoi de la réponse)
	$thread->moveToState('INTRO');

	// Envoi du texte
	$thread->send($answer);

});


/*
 * ETAT: INTRO
 *
 * - Présentation du lieu
 * - Demander que faire après
 */
$app->state('INTRO', function(Thread $thread, Message $message){

	// Vérification du changement d'état
	if($message instanceof CallbackMessage){
		if($message->getValue() === "TEAMS"){
			$thread->moveAndLoadState("TEAMS")->send(new TextMessage("Moving to teams"));
			return; // Interruption
		}elseif($message->getValue() === "EXAMPLES"){
			$thread->moveAndLoadState("EXAMPLES")->send(new TextMessage("Moving to examples"));
			return; // Interruption
		}
	}

	// Affichage d'un lieu
	$thread->send(new TextMessage("Vous êtes ici:"));
	$thread->send(new LocationMessage(46.1912586,6.1303793));


	// Envoi d'une suggestion de suite (traité ci-dessus)
	$answer = new TextMessage("Que souhaites-tu faire?");

	$answer->addButton(new Button("Faire les équipes","TEAMS")); // Le callback TEAMS est utilisé ci-dessus
	$answer->addButton(new Button("Exemples","EXAMPLES")); // Le callback EXAMPLES aussi.

	$thread->send($answer);


});


/*
 * ETAT: EXAMPLES
 */
$app->state('EXAMPLES', function(Thread $thread, Message $message){

	// Vérification du changement d'état, si ça n'a pas été traité avant
	if($message instanceof CallbackMessage and ! $message->isTreated()){
		if($message->getValue() === "CAROUSEL"){
			$thread->moveAndLoadState("EXAMPLE_CAROUSEL");
			return; // Interruption
		}elseif($message->getValue() === "LIST"){
			$thread->moveAndLoadState("EXAMPLE_LIST");
			return; // Interruption
		}elseif($message->getValue() === "DATA"){
			$thread->moveAndLoadState("EXAMPLE_DATA");
			return; // Interruption
		}elseif($message->getValue() === "NLP"){
			$thread->moveAndLoadState("EXAMPLE_NLP");
			return; // Interruption
		}elseif($message->getValue() === "ASK"){
			$thread->moveAndLoadState("ASK_MEAL");
			return; // Interruption
		}
	}

	// On affiche les possibilités
	$message = new TextMessage("Quel exemple voulez-vous afficher ?");

	$message->addButton(new Button("Une galerie", "CAROUSEL"));
	$message->addButton(new Button("Une liste", "LIST"));
	$message->addButton(new Button("Persistence", "DATA"));
	$message->addButton(new Button("Langage naturel", "NLP"));
	$message->addButton(new Button("Test", "ASK"));

	$thread->send($message);

});


/*
 * ETAT: EXAMPLE_CAROUSEL
 */
$app->state('EXAMPLE_CAROUSEL', function(Thread $thread, Message $message){

	$gallery = new GalleryMessage();

	$image1 = new Element("Titre A");
	$image1->setText("Contenu A Contenu A Contenu A Contenu A Contenu A Contenu A Contenu A Contenu A Contenu A");
	$image1->setImage("http://placehold.it/300x200?text=Image%20A");

	$image2 = new Element("Titre B");
	$image2->setText("Contenu B Contenu B Contenu B Contenu B Contenu B Contenu B Contenu B Contenu B Contenu B");
	$image2->setImage("http://placehold.it/300x200?text=Image%20B");

	$gallery->addElement($image1);
	$gallery->addElement($image2);

	$thread->send($gallery);

	// Pause de 3 secondes puis redirection à l'index
	sleep(3);
	$thread->moveAndLoadState('EXAMPLES');

});


/*
 * ETAT: EXAMPLE_LIST
 */
$app->state('EXAMPLE_LIST', function(Thread $thread, Message $message){


	$list = new ListMessage();

	for($i = 1; $i <= 4 ; $i++){
		$image = new Element("Titre $i");
		$image->setText("Contenu $i Contenu $i Contenu $i Contenu $i Contenu $i Contenu $i Contenu $i Contenu $i Contenu $i");
		$image->setImage("http://placehold.it/300x200?text=Image%20$i");

		$list->addElement($image);
	}

	$thread->send($list);

	// Pause de 3 secondes puis redirection à l'index
	sleep(3);
	$thread->moveAndLoadState('EXAMPLES');

});



/*
 * ETAT: EXAMPLE_DATA
 */
$app->state('EXAMPLE_DATA', function(Thread $thread, Message $message){

	// Obtention des données associées au thread
	if($message->isTreated()){ 	// Si on entre dans l'état (message traité ailleurs)
		$cpt = 0;
	}else{ // Sinon on récupère les données déjà stockées
		$cpt = $thread->getData();
	}

	// Vérification du changement d'état, si ça n'a pas été traité avant
	if($message instanceof CallbackMessage and ! $message->isTreated()){
		if($message->getValue() === "DONE"){
			$thread->send(new TextMessage("Compteur final: $cpt"));
			$thread->moveAndLoadState("EXAMPLES");
			return; // Interruption
		}
	}

	// On incrémente
	$cpt++ ;

	// On sauvegarde la nouvelle donnée
	$thread->setData($cpt);

	// Envoi du résultat
	$message = new TextMessage("Compteur actuel: $cpt");
	$message->addButton(new Button("Plus!"));
	$message->addButton(new Button("Fini...","DONE"));
	$thread->send($message);

});



/*
 * ETAT: EXAMPLE_NLP
 */
$app->state('EXAMPLE_NLP', function(Thread $thread, Message $message){


	/*
	 * Important: vous devez avoir associé ces données à un compte API.ai
	 * Contactez le TechBar pour la mettre en place
	 * */


	// Vérification du changement d'état, si ça n'a pas été traité avant
	if($message instanceof CallbackMessage and ! $message->isTreated()){
		if($message->getValue() === "DONE"){
			$thread->moveAndLoadState("EXAMPLES");
			return; // Interruption
		}
	}


	if(! $message->isTreated()){
		if($data = $message->getApiAi() and isset($data['result'])){
			foreach($data['result'] as $k => $value){
				$thread->send(new TextMessage("$k :\n".json_encode($value, JSON_PRETTY_PRINT)));
			}
		}else{
			$thread->send(new TextMessage("Aucune donnée retournée par API.ai"));
		}
		sleep(2);
	}


	// Envoi du résultat
	$message = new TextMessage("Entrez un texte pour le parser:");
	$message->addButton(new Button("Fini de jouer","DONE"));
	$thread->send($message);

});



/*
 * ETAT: AskMeal
 */
 $app->state('ASK_MEAL', function(Thread $thread, Message $message){
	
	
		/*
		 * Important: vous devez avoir associé ces données à un compte API.ai
		 * Contactez le TechBar pour la mettre en place
		 * */
	
	
		// Vérification du changement d'état, si ça n'a pas été traité avant
		if($message instanceof CallbackMessage and ! $message->isTreated()){
			if($message->getValue() === "DONE"){
				$thread->moveAndLoadState("INTRO");
				return; // Interruption
			}
		}
	
	
		if(! $message->isTreated()){
			$thread->send(new TextMessage($message))
			sleep(2);
		}
	
	
		// Envoi du résultat
		$message = new TextMessage("Entrez un texte pour que je le copies ;)");
		$message->addButton(new Button("Fini de jouer","DONE"));
		$thread->send($message);
	
	});
	


// Récupération de la requête
$result = $app->run();


// Show result
var_dump($result);
