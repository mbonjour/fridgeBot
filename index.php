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
ini_set("allow_url_fopen", 1);
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
$app->state('WELCOME', function(Thread $thread,  Message $message){
	if(!$message->isTreated()){	
		$thread->moveAndLoadState("SPECIAL");
		return;
			
	}

	// On envoie directement une image
	$thread->send(new ImageMessage("https://www.creageneve.com/wp-content/uploads/2017/07/bighack_cover_dev.jpg"));

	// Création d'un texte
	$answer = new TextMessage("Hey! I am the chef fridgeBot, I will help you find a good recipe for your leftover in your fridge");

	$answer = new TextMessage("Are you hungry?");
	$answer->addButton(new Button("I'm hungry!","HUNG"));
	

	// Envoi du texte
	$thread->send($answer);

});


$app->state('SPECIAL', function(Thread $thread, Message $message){
	if($message instanceof CallbackMessage){
		if($message->getValue() === "VEGE"){
			$thread->setData("&health=vegetarian");
			$thread->moveAndLoadState("HUNGRY");
			return;
		}
		elseif($message->getValue() ==="GF"){
			$thread->setData("&health=gluten-free");
			$thread->moveAndLoadState("HUNGRY");
			return;
		}
		elseif($message->getValue()=== "OK"){
			$thread->setData("");
			$thread->moveAndLoadState("HUNGRY");
			return;	
		}
		else{
			$thread->setData("");
			
		}
	}
	
	$answer = new TextMessage("P.S: \"hungry\" is the magic word that allows you to restart from here");
	$answer = new TextMessage("Do you have any preference about your alimentation? :)\nMore to come later");

	$answer->addButton(new Button("Vegetarian","VEGE"));
	$answer->addButton(new Button("Gluten-free","GF"));
	$answer->addButton(new Button("No, thanks","OK"));

	$thread->send($answer);

});
/*
 * ETAT: EXAMPLES
 */
$app->state('HUNGRY', function(Thread $thread, Message $message){
	

	
	if($message instanceof TextMessage and !$message->isTreated()){
		if($message->getText() === "test"){
			$thread->moveAndLoadState("WELCOME");
			return;
		}
		if($message->getText() === "hungry"){
			$thread->moveAndLoadState("SPECIAL");
			return;
		}
		$searchOption = $thread->getData();
		$json_string = file_get_contents("https://api.edamam.com/search?q=".$message->getText().$searchOption."&app_id=3db2da4d&app_key=fa39784d560d0f2ca6e95294ef79498f");
		$fp = fopen('user-'.$thread->getId().'.json', 'w');
		fwrite($fp, $json_string);
		fclose($fp);

    	//json string to array
        $answer = json_decode($json_string, true);
        //$thread->send(new TextMessage($answer['q']." ".$thread->getData()));
        $thread->moveAndLoadState("GIVE_RESULTS");
        return;
	}
	$thread->send(new TextMessage("Please tell me what aliments you want to cook!\nP.S: write them in a single message, separated with only spaces."));
});

include 'module1.php';
// Récupération de la requête
$result = $app->run();
// Show result
var_dump($result);
