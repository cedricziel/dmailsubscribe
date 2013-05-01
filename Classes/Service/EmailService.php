<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Fromme <fromme@dreipunktnull.come>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Email Service
 *
 * Wrapper Service to quickly send emails
 *
 * @package Dmailsubscribe
 * @subpackage Service
 */
class Tx_Dmailsubscribe_Service_EmailService {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param string $toEmail
	 * @param string $toName
	 * @param string $templateName
	 * @param boolean $html
	 * @param array $variables
	 * @throws Tx_Extbase_Configuration_Exception
	 * @return boolean
	 */
	public function send($toEmail, $toName, $templateName, $html = TRUE, array $variables = array()) {
		$settings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
		$charset = 'utf-8';
		if (TRUE === isset($settings['charset']) && (FALSE === empty($settings['charset']))) {
			$charset = $settings['charset'];
		}
		$subject = 'Newsletter Subscription';
		if (TRUE === isset($settings['subject']) && (FALSE === empty($settings['subject']))) {
			$subject = $settings['subject'];
		}

		if (TRUE === isset($settings['fromEmail']) && FALSE === empty($settings['fromEmail'])) {
			$fromEmail = $settings['fromEmail'];
		} else {
			throw new Tx_Extbase_Configuration_Exception('Sender email address is not specified.');
		}

		if (TRUE === isset($settings['fromName']) && FALSE === empty($settings['fromName'])) {
			$fromName = $settings['fromName'];
		} else {
			throw new Tx_Extbase_Configuration_Exception('Sender name is not specified.');
		}

		$htmlView = $this->getView($templateName . 'Html');
		$htmlView->assignMultiple($variables);
		$htmlView->assign('charset', $charset);
		$htmlView->assign('title', $subject);
		$htmlBody = $htmlView->render();

		$plainView = $this->getView($templateName . 'Plain');
		$plainView->assignMultiple($variables);
		$plainView->assign('charset', $charset);
		$plainView->assign('title', $subject);
		$plainBody = $plainView->render();

		/** @var t3lib_mail_Message $message */
		$message = t3lib_div::makeInstance('t3lib_mail_Message');
		$message->setTo(array($toEmail => $toName))
			->setFrom(array($fromEmail => $fromName))
			->setSubject($subject)
			->setCharset($charset);

		if (FALSE === $html) {
			$message->setBody($plainBody, 'text/plain');
		} else {
			$message->setBody($htmlBody, 'text/html');
			$message->addPart($plainBody, 'text/plain');
		}

		$message->send();

		return $message->isSent();
	}

	/**
	 * @param string $templateName
	 * @return Tx_Fluid_View_StandaloneView
	 */
	protected function getView($templateName) {
		/** @var Tx_Fluid_View_StandaloneView $emailView */
		$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');
		$view->setFormat('html');
		$view->getRequest()->setControllerExtensionName('Dmailsubscribe');

		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

		$templateRootPath = t3lib_div::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
		$layoutRootPath = t3lib_div::getFileAbsFileName($extbaseFrameworkConfiguration['view']['layoutRootPath']);
		$templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . '.html';

		$view->setTemplatePathAndFilename($templatePathAndFilename);
		$view->setLayoutRootPath($layoutRootPath);

		return $view;
	}
}
