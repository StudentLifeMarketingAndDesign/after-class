<?php
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LabelField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;

class CalendarAddPageController extends PageController {

	private static $allowed_actions = array(
		'AddForm',
	);

	public function AddForm() {
		$fields = new FieldList(
			new TextField('SocialLink', 'Social media link:'),
			new LiteralField('SocialLinkInfo', '<label class="readonly">We currently support links from <i aria-hidden="true" class="fab fa-twitter"></i> Twitter and <i aria-hidden="true" class="fab fa-instagram"></i> Instagram </label>'),
			DateField::create('Expires', 'Expiry date (optional):'),

			new LabelField('ExpiresLabel', 'We\'ll show this post on After Class until the date above. Usually this would be the day after the event. If unsure, please leave this field blank.'),
			$emailField = new EmailField('SubmitterEmail', "Your email address (optional, only used if we need to clarify anything regarding the event).")
		);

		if (Permission::check('ADMIN')) {
			$fields->push(CheckboxField::create('PublishNow', 'Publish this post immediately')->setDescription('This will skip the approval process. Use with care, please.'));
		}

		$actions = new FieldList(
			FormAction::create('submit', 'Submit your post')->addExtraClass('btn-lg')
		);

		if ($member = Security::getCurrentUser()) {

			$emailField->setValue($member->Email);
		}

		$form = new Form($this, 'AddForm', $fields, $actions);

		//Disable captcha if logged in or in dev mode:
		if (!($member) && (Director::isLive())) {
			$form->enableSpamProtection();
		}

		return $form;
	}

	public function submit($data, $form) {

		$link = $data['SocialLink'];

		$existingLinkCheck = CalendarEvent::get()->filter(array('SocialLink' => $link))->First();

		//Only check for existing links in live mode
		if (Director::isLive()) {
			if ($existingLinkCheck) {

				$data = new ArrayData(array(
					'Alert' => '<div class="alert alert-failure">Sorry, this social media link has already been submitted.</div>',
					'Form' => '',
				));

				return $this->customise($data)->render();

			}
		}
//publishRecursive

		//Check for invalid link:
		if (filter_var($link, FILTER_VALIDATE_URL) === false) {
			$data = new ArrayData(array(
				'Alert' => '<div class="alert alert-warning">Sorry, this is not a valid link.</div>',
				'Form' => '',
			));

			return $this->customise($data)->render();
		}

		//Check for invalid domain in link:
		$parsedLink = parse_url($link);
		$validDomains = array('instagram.com', 'www.instagram.com', 'twitter.com', 'www.twitter.com');

		if (!in_array($parsedLink['host'], $validDomains)) {

			$data = new ArrayData(array(
				'Alert' => '<div class="alert alert-warning">Sorry, we are currently only accepting Instagram and Twitter posts.</div>',
				'Form' => '',
			));

			return $this->customise($data)->render();

		}

		//print_r($parsedLink);

		$calendar = Calendar::get()->First();

		if (!$calendar) {
			throw new \LogicException("No calendar found");
		}

		$newEvent = CalendarEvent::create();
		$newEvent->ParentID = $calendar->ID;

		$formData = $form->getData();
		$form->saveInto($newEvent);

		$newEvent->writeToStage('Stage');

		$alertText = '<div class="alert alert-success" role="alert">' . $this->SubmissionThanks . '</div>';

		if (isset($data['PublishNow'])) {
			if (($data['PublishNow'] == 1) && (Permission::check('ADMIN'))) {
				$newEvent->publishRecursive();
				$alertText = '<div class="alert alert-success" role="alert">This post has <strong>saved and published</strong>. View here: <a style="color: #155724; text-decoration: underline;" href="' . $newEvent->AbsoluteLink() . '">' . $newEvent->AbsoluteLink() . '</a></div>';
			}
		}

		//Injector::inst()->get(LoggerInterface::class)->debug('Query executed: ' . $sql);
		// print_r($formData);

		$this->sendNotificationEmail($data, $newEvent);
		$data = new ArrayData(array(
			'Alert' => $alertText,
			'Form' => '',
		));
		return $this->customise($data)->render();

	}

	private function sendNotificationEmail($data, $newEvent) {

		$recipients = $this->EmailRecipients();

		foreach ($recipients as $recipient) {

			$email = new Email();

			$email->setTo($recipient->EmailAddress);
			if ($newEvent->SubmitterEmail) {
				$email->setReplyTo($newEvent->SubmitterEmail);
			}

			$email->setSubject("[Social Calendar Submission] A link was submitted");
			//TODO: Show some of the newly parsed link data in the email below:
			$messageBody = "
	        	<p>The following link was submitted to the After Class submit-a-post form: </p>
	            <p><strong>Social Media Link:</strong> <a href=\"{$data['SocialLink']}\">{$data['SocialLink']}</a></p>";

			if ($newEvent->SubmitterEmail) {
				$messageBody .= '<p><strong>Link submitted by:</strong> <a href="mailto:' . $newEvent->SubmitterEmail . '">' . $newEvent->SubmitterEmail . '</a>';
			}
			if ($newEvent->Expires) {
				$messageBody .= '<p><strong>Link expires on:</strong> ' . $newEvent->obj('Expires')->Nice() . '</p>';
			}
			$messageBody .= "
				<p><a href=\"admin/pages/edit/show/" . $newEvent->ID . "\">You can either publish or remove this post by editing this entry on After Class &rarr;</a>
				</p>";
			$email->setBody($messageBody);
			// print_r($newEvent->SubmitterEmail);
			// print_r($newEvent->Expires);
			// print_r($messageBody);
			$email->send();
		}
	}

}
