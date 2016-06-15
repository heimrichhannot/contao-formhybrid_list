<?php

namespace HeimrichHannot\FormHybridList;

class Comments extends \Comments
{

	/**
	 * Adds delete functionality to every comment
	 */
	public function addCommentsToTemplate(\FrontendTemplate $objTemplate, \stdClass $objConfig, $strSource, $intParent, $varNotifies)
	{
		/** @var \PageModel $objPage */
		global $objPage;

		$limit = 0;
		$offset = 0;
		$total = 0;
		$gtotal = 0;
		$arrComments = array();

		$objTemplate->comments = array(); // see #4064

		// Pagination
		if ($objConfig->perPage > 0)
		{
			// Get the total number of comments
			$intTotal = \CommentsModel::countPublishedBySourceAndParent($strSource, $intParent);
			$total = $gtotal = $intTotal;

			// Calculate the key (e.g. tl_form_field becomes page_cff12)
			$key = '';
			$chunks = explode('_', substr($strSource, ((strncmp($strSource, 'tl_', 3) === 0) ? 3 : 0)));

			foreach ($chunks as $chunk)
			{
				$key .= substr($chunk, 0, 1);
			}

			// Get the current page
			$id = 'page_c' . $key . $intParent; // see #4141
			$page = (\Input::get($id) !== null) ? \Input::get($id) : 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$objConfig->perPage), 1))
			{
				/** @var \PageError404 $objHandler */
				$objHandler = new $GLOBALS['TL_PTY']['error_404']();
				$objHandler->generate($objPage->id);
			}

			// Set limit and offset
			$limit = $objConfig->perPage;
			$offset = ($page - 1) * $objConfig->perPage;

			// Initialize the pagination menu
			$objPagination = new \Pagination($total, $objConfig->perPage, \Config::get('maxPaginationLinks'), $id);
			$objTemplate->pagination = $objPagination->generate("\n  ");
		}

		$objTemplate->allowComments = true;

		// Get all published comments
		if ($limit)
		{
			$objComments = \CommentsModel::findPublishedBySourceAndParent($strSource, $intParent, ($objConfig->order == 'descending'), $limit, $offset);
		}
		else
		{
			$objComments = \CommentsModel::findPublishedBySourceAndParent($strSource, $intParent, ($objConfig->order == 'descending'));
		}

		// Parse the comments
		if ($objComments !== null && ($total = $objComments->count()) > 0)
		{
			$count = 0;

			if ($objConfig->template == '')
			{
				$objConfig->template = 'com_default';
			}

			/** @var \FrontendTemplate|object $objPartial */
			$objPartial = new \FrontendTemplate($objConfig->template);

			while ($objComments->next())
			{
				$objPartial->setData($objComments->row());

				// Clean the RTE output
				if ($objPage->outputFormat == 'xhtml')
				{
					$objPartial->comment = \StringUtil::toXhtml($objComments->comment);
				}
				else
				{
					$objPartial->comment = \StringUtil::toHtml5($objComments->comment);
				}

				$objPartial->comment = trim(str_replace(array('{{', '}}'), array('&#123;&#123;', '&#125;&#125;'), $objPartial->comment));

				$objPartial->datim = \Date::parse($objPage->datimFormat, $objComments->date);
				$objPartial->date = \Date::parse($objPage->dateFormat, $objComments->date);
				$objPartial->class = (($count < 1) ? ' first' : '') . (($count >= ($total - 1)) ? ' last' : '') . (($count % 2 == 0) ? ' even' : ' odd');
				$objPartial->by = $GLOBALS['TL_LANG']['MSC']['com_by'];
				$objPartial->id = 'c' . $objComments->id;
				$objPartial->timestamp = $objComments->date;
				$objPartial->datetime = date('Y-m-d\TH:i:sP', $objComments->date);
				$objPartial->addReply = false;

				// delete -> only if an email address is linked!
				$objPartial->delete = false;
				if ($objComments->email && $objComments->email == \FrontendUser::getInstance()->email)
				{
					$objPartial->delete = true;
					$strFormId = $strFormId = 'com_'. $strSource .'_'. $intParent . '_' . $objComments->id;
					$objPartial->formId = $strFormId;
					$objPartial->action = ampersand(\Environment::get('request'));

					if (\Input::post('FORM_SUBMIT') == $strFormId)
					{
						$objComments->delete();
						die();
					}
				}

				// Reply
				if ($objComments->addReply && $objComments->reply != '')
				{
					if (($objAuthor = $objComments->getRelated('author')) !== null)
					{
						$objPartial->addReply = true;
						$objPartial->rby = $GLOBALS['TL_LANG']['MSC']['com_reply'];
						$objPartial->reply = $this->replaceInsertTags($objComments->reply);
						$objPartial->author = $objAuthor;

						// Clean the RTE output
						if ($objPage->outputFormat == 'xhtml')
						{
							$objPartial->reply = \StringUtil::toXhtml($objPartial->reply);
						}
						else
						{
							$objPartial->reply = \StringUtil::toHtml5($objPartial->reply);
						}
					}
				}

				$arrComments[] = $objPartial->parse();
				++$count;
			}
		}

		$objTemplate->comments = $arrComments;
		$objTemplate->addComment = $GLOBALS['TL_LANG']['MSC']['addComment'];
		$objTemplate->name = $GLOBALS['TL_LANG']['MSC']['com_name'];
		$objTemplate->email = $GLOBALS['TL_LANG']['MSC']['com_email'];
		$objTemplate->website = $GLOBALS['TL_LANG']['MSC']['com_website'];
		$objTemplate->commentsTotal = $limit ? $gtotal : $total;

		// Add a form to create new comments
		$this->renderCommentForm($objTemplate, $objConfig, $strSource, $intParent, $varNotifies);
	}

	/**
	 * removes $this->reload(); call (last line) of core method \Comments::renderCommentForm()
	 */
	protected function renderCommentForm(\FrontendTemplate $objTemplate, \stdClass $objConfig, $strSource, $intParent, $varNotifies)
	{
		$this->import('FrontendUser', 'User');

		// Access control
		if ($objConfig->requireLogin && !BE_USER_LOGGED_IN && !FE_USER_LOGGED_IN)
		{
			$objTemplate->requireLogin = true;
			$objTemplate->login = $GLOBALS['TL_LANG']['MSC']['com_login'];

			return;
		}

		// Confirm or remove a subscription
		if (\Input::get('token'))
		{
			static::changeSubscriptionStatus($objTemplate);

			return;
		}

		// Form fields
		$arrFields = array
		(
			'name' => array
			(
				'name'      => 'name',
				'label'     => $GLOBALS['TL_LANG']['MSC']['com_name'],
				'value'     => trim($this->User->firstname . ' ' . $this->User->lastname),
				'inputType' => 'text',
				'eval'      => array('mandatory'=>true, 'maxlength'=>64)
			),
			'email' => array
			(
				'name'      => 'email',
				'label'     => $GLOBALS['TL_LANG']['MSC']['com_email'],
				'value'     => $this->User->email,
				'inputType' => 'text',
				'eval'      => array('rgxp'=>'email', 'mandatory'=>true, 'maxlength'=>128, 'decodeEntities'=>true)
			),
			'website' => array
			(
				'name'      => 'website',
				'label'     => $GLOBALS['TL_LANG']['MSC']['com_website'],
				'inputType' => 'text',
				'eval'      => array('rgxp'=>'url', 'maxlength'=>128, 'decodeEntities'=>true)
			)
		);

		// Captcha
		if (!$objConfig->disableCaptcha)
		{
			$arrFields['captcha'] = array
			(
				'name'      => 'captcha',
				'inputType' => 'captcha',
				'eval'      => array('mandatory'=>true)
			);
		}

		// Comment field
		$arrFields['comment'] = array
		(
			'name'      => 'comment',
			'label'     => $GLOBALS['TL_LANG']['MSC']['com_comment'],
			'inputType' => 'textarea',
			'eval'      => array('mandatory'=>true, 'rows'=>4, 'cols'=>40, 'preserveTags'=>true)
		);

		// Notify me of new comments
		$arrFields['notify'] = array
		(
			'name'      => 'notify',
			'label'     => '',
			'inputType' => 'checkbox',
			'options'   => array(1=>$GLOBALS['TL_LANG']['MSC']['com_notify'])
		);

		$doNotSubmit = false;
		$arrWidgets = array();
		$strFormId = 'com_'. $strSource .'_'. $intParent;

		// Initialize the widgets
		foreach ($arrFields as $arrField)
		{
			/** @var \Widget $strClass */
			$strClass = $GLOBALS['TL_FFL'][$arrField['inputType']];

			// Continue if the class is not defined
			if (!class_exists($strClass))
			{
				continue;
			}

			$arrField['eval']['required'] = $arrField['eval']['mandatory'];

			/** @var \Widget $objWidget */
			$objWidget = new $strClass($strClass::getAttributesFromDca($arrField, $arrField['name'], $arrField['value']));

			// Validate the widget
			if (\Input::post('FORM_SUBMIT') == $strFormId)
			{
				$objWidget->validate();

				if ($objWidget->hasErrors())
				{
					$doNotSubmit = true;
				}
			}

			$arrWidgets[$arrField['name']] = $objWidget;
		}

		$objTemplate->fields = $arrWidgets;
		$objTemplate->submit = $GLOBALS['TL_LANG']['MSC']['com_submit'];
		$objTemplate->action = ampersand(\Environment::get('request'));
		$objTemplate->messages = ''; // Backwards compatibility
		$objTemplate->formId = $strFormId;
		$objTemplate->hasError = $doNotSubmit;

		// Do not index or cache the page with the confirmation message
		if ($_SESSION['TL_COMMENT_ADDED'])
		{
			/** @var \PageModel $objPage */
			global $objPage;

			$objPage->noSearch = 1;
			$objPage->cache = 0;

			$objTemplate->confirm = $GLOBALS['TL_LANG']['MSC']['com_confirm'];
			$_SESSION['TL_COMMENT_ADDED'] = false;
		}

		// Store the comment
		if (!$doNotSubmit && \Input::post('FORM_SUBMIT') == $strFormId)
		{
			$strWebsite = $arrWidgets['website']->value;

			// Add http:// to the website
			if (($strWebsite != '') && !preg_match('@^(https?://|ftp://|mailto:|#)@i', $strWebsite))
			{
				$strWebsite = 'http://' . $strWebsite;
			}

			// Do not parse any tags in the comment
			$strComment = specialchars(trim($arrWidgets['comment']->value));
			$strComment = str_replace(array('&amp;', '&lt;', '&gt;'), array('[&]', '[lt]', '[gt]'), $strComment);

			// Remove multiple line feeds
			$strComment = preg_replace('@\n\n+@', "\n\n", $strComment);

			// Parse BBCode
			if ($objConfig->bbcode)
			{
				$strComment = $this->parseBbCode($strComment);
			}

			// Prevent cross-site request forgeries
			$strComment = preg_replace('/(href|src|on[a-z]+)="[^"]*(contao\/main\.php|typolight\/main\.php|javascript|vbscri?pt|script|alert|document|cookie|window)[^"]*"+/i', '$1="#"', $strComment);

			$time = time();

			// Prepare the record
			$arrSet = array
			(
				'tstamp'    => $time,
				'source'    => $strSource,
				'parent'    => $intParent,
				'name'      => $arrWidgets['name']->value,
				'email'     => $arrWidgets['email']->value,
				'website'   => $strWebsite,
				'comment'   => $this->convertLineFeeds($strComment),
				'ip'        => $this->anonymizeIp(\Environment::get('ip')),
				'date'      => $time,
				'published' => ($objConfig->moderate ? '' : 1)
			);

			// Store the comment
			$objComment = new \CommentsModel();
			$objComment->setRow($arrSet)->save();

			// Store the subscription
			if ($arrWidgets['notify']->value)
			{
				static::addCommentsSubscription($objComment);
			}

			// HOOK: add custom logic
			if (isset($GLOBALS['TL_HOOKS']['addComment']) && is_array($GLOBALS['TL_HOOKS']['addComment']))
			{
				foreach ($GLOBALS['TL_HOOKS']['addComment'] as $callback)
				{
					$this->import($callback[0]);
					$this->{$callback[0]}->{$callback[1]}($objComment->id, $arrSet, $this);
				}
			}

			// Prepare the notification mail
			$objEmail = new \Email();
			$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
			$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
			$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['com_subject'], \Idna::decode(\Environment::get('host')));

			// Convert the comment to plain text
			$strComment = strip_tags($strComment);
			$strComment = \StringUtil::decodeEntities($strComment);
			$strComment = str_replace(array('[&]', '[lt]', '[gt]'), array('&', '<', '>'), $strComment);

			// Add the comment details
			$objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['com_message'],
									  $arrSet['name'] . ' (' . $arrSet['email'] . ')',
									  $strComment,
									  \Idna::decode(\Environment::get('base')) . \Environment::get('request'),
									  \Idna::decode(\Environment::get('base')) . 'contao/main.php?do=comments&act=edit&id=' . $objComment->id);

			// Do not send notifications twice
			if (is_array($varNotifies))
			{
				$objEmail->sendTo(array_unique($varNotifies));
			}
			elseif ($varNotifies != '')
			{
				$objEmail->sendTo($varNotifies); // see #5443
			}

			// Pending for approval
			if ($objConfig->moderate)
			{
				$_SESSION['TL_COMMENT_ADDED'] = true;
			}
			else
			{
				static::notifyCommentsSubscribers($objComment);
			}
		}
	}
}