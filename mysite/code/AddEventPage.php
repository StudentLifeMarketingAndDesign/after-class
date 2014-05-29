<?php
class AddEventPage extends Page {
	
	public static $db = array(
	
	);
	public static $has_one = array(
	);
	static $has_many = array(
	);
	function getCMSFields() {
		$fields = parent::getCMSFields();
		//$fields->removeFieldFromTab('Root.Content', 'Content'); // remove a field from a tab
		
		return $fields;
	}
	
}

class AddEventPage_Controller extends Page_Controller {
	public static $allowed_actions = array (
		"addEventForm"
	);
	public function init() {
		parent::init();
	}
	public function submittedEvent() {
		if(Session::get('Submitted')) {
        	return true;
    	} else {
    		return false;
    	}
	}
	
	public function addEventForm() {
	
		$fields = new FieldList(
            new TextField('Title',"What's the name of the event?"),
            new TextField('Location',"Where's the event located?"),
            new TextField('MoreInfoLink',"What's a website or link that has more information about the event?"),
            new TextField('FacebookEventLink', 'If applicable, please give us a <span class="fbButton" href="#">Facebook Event Link</span>'),
            new TextField('Submitterdate',"When's the event happening? We need both the Date(s) AND Time(s) of the event."),
            new TextField('Cost','How much does it cost to attend?'),
            new TextareaField('Content','Describe what the event is about.'),

            new TextField('SubmitterSponsor','Who is sponsoring or hosting the event?'),
            new FileField('Image','Event Image. We accept JPG, PNG, GIF image formats only. Please do not attach PDFs to this form. (730 x 462 pixels is preferred)'),

            new TextField('Submittername','What is your name in case we need more info?'),
            new TextField('Submitteremail','What is your email address in case we need more info?')
        );
        $actions = new FieldList(
            new FormAction('addEvent', 'Submit')
        );
        $validator = new RequiredFields('Title','Location','Cost','Submitterdate', 'Description','Submittername','Submitteremail','Submitterdate');
     	$form = new Form($this, 'addEventForm', $fields, $actions, $validator);
     	$form->enableSpamProtection();
     	
        return $form;
        
	}
	
	function addEvent($data, $form) {
		$event = new AfterClassEvent();
	    $event->ParentID = 6;

        $form->saveInto($event);
        
        $event->write();
        $event->writeToStage("Stage");

        $event->publish("Stage", "Live");

        $event->deleteFromStage('Live');
        
<<<<<<< HEAD
=======

        Session::set('Submitted', true);
        
>>>>>>> e0fafe6236a847b3e184c3c4ce1480238e573321
        //Email notification
		
		if($event->Submitteremail){
			$from = $event->Submitteremail;
		}else {
			$from = "After Class Submissions";
		}
		
		$to = "dustin-quam@uiowa.edu, benjamin-lewis@uiowa.edu, afterclass@uiowa.edu";
		$subject = "[AC]".$event->Title;
		$body = '<p>Someone submitted an After Class Event. This is the event information:</p>
		
				<ul>
				
					<li><strong>Title:</strong> '.$event->Title.'</li>
					<li><strong>Submitted By:</strong> '.$event->Submittername.'['.$event->Submitteremail.']</li>
					<li><strong>Date:</strong> '.$event->Submitterdate.'</li>
					<li><strong>Website or More Info Link:</strong> '.$event->MoreInfoLink.'</li>
					<li><strong>Facebook Event Link:</strong> '.$event->FacebookEventLink.'</li>
					<li><strong>Location:</strong> '.$event->Location.'</li>
					<li><strong>Cost:</strong> '.$event->Cost.'</li>
					<li><strong>Sponsored By:</strong> '.$event->Sponsor.'</li>
					<li><strong>Description:</strong> '.$event->Content.'</li>
					
				</ul>
				
				<p><a href="http://afterclass.uiowa.edu/admin/pages/edit/show/'.$event->ID.'">Approve it (or don\'t) here</a></p>';
			
		$email = new Email($from, $to, $subject, $body);
		
		$email->send();
        
        Controller::curr()->redirect('./thanks');
	}
}