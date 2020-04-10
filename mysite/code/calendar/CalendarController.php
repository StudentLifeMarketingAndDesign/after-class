<?php
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\Form;
use SilverStripe\Control\Email\Email;

class CalendarController extends PageController{

	private static $allowed_actions = array(
		// 'year'
		'ics',
		'AddForm',
		'add'
	);

	private static $url_handlers = array(
		// // 'year//$Year' => 'year',
		// 'ics' => 'ics'
		'add' => 'add'
	);

	// public function year(){
	// 	$params = $this->getRequest()->params();
	// 	$year = $params['Year'];

	// 	if($year){
	// 		echo 'year set';

	// 	}

	// 	//$endDate = sfDate::getInstance($d->finalDayOfYear()->date());


	// }


	public function add(){
		return $this->renderWith(array('Calendar_add', 'Page'));

	}

	public function AddForm(){ 

        $fields = new FieldList( 
            new TextField('SocialLink')
        ); 
        $actions = new FieldList( 
            new FormAction('submit', 'Submit') 
        ); 
        return new Form($this, 'AddForm', $fields, $actions); 
    }

    public function submit($data, $form) { 

    	$existingLinkCheck = CalendarEvent::get()->filter(array('SocialLink' => $data['SocialLink']))->First();

    	if($existingLinkCheck){
    		return [
    			'Content' => '<p>Sorry, this social media link has already been submitted.</p>'
    		];

    	}

    	$newEvent = CalendarEvent::create();
    	$newEvent->ParentID = $this->ID;

    	$formData = $form->getData();

    	$form->saveInto($newEvent);

    	$newEvent->write();

        return [
            'Content' => '<p>Thank you for submitting your event!</p>',
            'Form' => ''
        ];


        $email = new Email(); 
         
        $email->setTo('dustin-quam@uiowa.edu'); 
        $email->setFrom($data['Email']); 
        $email->setSubject("[Social Calendar Submission] A link was submitted"); 
         
        $messageBody = " 
            <p><strong>Link:</strong> <a href=\"{$data['SocialLink']}\">{$data['SocialLink']}</a></p>
        "; 
        $email->setBody($messageBody); 
        $email->send(); 
        return [
            'Content' => '<p>Thank you for your feedback.</p>',
            'Form' => ''
        ];
    }
	public function ics() {
		//echo 'hello';
		$params = $this->getRequest()->params();

	// 	$year = $params['Year'];
		$feed = false;
		$announcement = false;
		$id = $params['ID'];
		$oid = $params['OtherID'];
		if(stristr($id, "ICS_") !== false) {
			$id = str_replace("ICS_","",$id);
			$feed = true;
		}
		if(is_numeric($id) && $oid) {
			if(!$feed) {
				$event = DataObject::get_by_id('CalendarEvent', $id);

                // return if not found
                if (!$event) {
                	echo 'event not found?';
                    //return $this->httpError(404);
                }
				$FILENAME = $announcement ? preg_replace("/[^a-zA-Z0-9s]/", "", $event->Title) : $event->URLSegment;
			}
			else {
				$FILENAME = preg_replace("/[^a-zA-Z0-9s]/", "", urldecode($_REQUEST['title']));
			}


			$FILENAME .= ".ics";
			$HOST = $_SERVER['HTTP_HOST'];
			$TIMEZONE = Calendar::config()->timezone;
			$LANGUAGE = Calendar::config()->language;
			$CALSCALE = "GREGORIAN";
			$parts = explode('-',$oid);
			$START_TIMESTAMP = $parts[0];
			$END_TIMESTAMP = $parts[1];
			if(!$feed) {
				$URL = $announcement ? $event->Calendar()->AbsoluteLink() : $event->AbsoluteLink();
			}
			else {
				$URL = "";
			}
			$TITLE = $feed ? $_REQUEST['title'] : $event->Title;
			$CONTENT = $feed ? $_REQUEST['content'] : $event->obj('Content')->Summary();
			$LOCATION = $feed ? $_REQUEST['location'] : $event->Location;

			// $this->setResponse(new HTTPResponse());
			$this->getResponse()->addHeader('Cache-Control','private');
			$this->getResponse()->addHeader('Content-Description','File Transfer');
			$this->getResponse()->addHeader('Content-Type','text/calendar');
			$this->getResponse()->addHeader('Content-Transfer-Encoding','binary');
			if(stristr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
 				$this->getResponse()->addHeader("Content-disposition","filename=".$FILENAME."; attachment;");
 			}
 			else {
 				$this->getResponse()->addHeader("Content-disposition","attachment; filename=".$FILENAME);
 			}
			$result = trim(strip_tags($this->customise(array(
				'HOST' => $HOST,
				'LANGUAGE' => $LANGUAGE,
				'TIMEZONE' => $TIMEZONE,
				'CALSCALE' => $CALSCALE,
				'START_TIMESTAMP' => $START_TIMESTAMP,
				'END_TIMESTAMP' => $END_TIMESTAMP,
				'URL' => $URL,
				'TITLE' => $TITLE,
				'CONTENT' => $CONTENT,
				'LOCATION' => $LOCATION
			))->renderWith(array('ics'))));

			$this->getResponse()->setBody($result);
			//print_r($this->getResponse());
			return $this->getResponse();

		}
		else {

			$this->redirectBack();
		}

	}
}
