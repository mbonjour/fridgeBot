<?php
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


$app->state('GIVE_RESULTS', function (Thread $thread, Message $message){
	$objAnswer = json_decode(file_get_contents('user-'.$thread->getId().'.json'));
	if ($objAnswer->count == 0) {
		$thread->send(new TextMessage("Veuillez réessayer !"));
		$thread->moveAndLoadState('HUNGRY');
		return;
	}

	$gallery = new GalleryMessage();
	// __DIR__."/recettes/user-".$thread->getId().'.json'
	
	for($i = 0; $i <= 3 && $i< $objAnswer->count; $i++){
		$image[$i] = new Element($objAnswer->hits[$i]->recipe->label);
		$image[$i]->setText(implode(",",$objAnswer->hits[$i]->recipe->ingredientLines));
		$image[$i]->setImage($objAnswer->hits[$i]->recipe->image);
		$image[$i]->addButton(new Button("Celui là !","result".$i));
	}

	for($i = 0; $i <= 3 && $i< $objAnswer->count; $i++){
	$gallery->addElement($image[$i]);
	}

	$thread->send($gallery);
	// Pause de 3 secondes puis redirection à l'index
	sleep(3);
	$thread->moveAndLoadState('SPECIAL');
});	

?>