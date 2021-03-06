<?php

/**
 * OntoWiki error controller.
 * Fetched by default through the Zend_Controller_Plugin_ErrorHandler
 * 
 * @package    application
 * @subpackage mvc
 * @author     Norman Heino <norman.heino@gmail.com>
 * @copyright  Copyright (c) 2008, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @version    $Id: ErrorController.php 3470 2009-06-29 06:58:02Z pfrischmuth $
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * Default action that is triggered when an error occures
     * during the dispatch process.
     */
    public function errorAction()
    {
        if (defined('_OWDEBUG')) {
            $this->_debugError();
        } else {
            $this->_gracefulError();
        }

        // we provide a complete page
        $this->_helper->layout()->disableLayout();
    }

    protected function _debugError()
    {
        if ($this->_request->has('error_handler')) {
            // get errors passed by error handler plug-in
            $errors    = $this->_getParam('error_handler');
            $exception = $errors->exception;

            // check error type and send headers accordingly
            switch ($errors->type) {
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                    $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                    break;
                default:
                    // don't change headers
            }
            
            switch (true) {
                case ($exception instanceof OntoWiki_Http_Exception):
                    $this->_helper->layout()->disableLayout();
                    $this->_helper->viewRenderer->setNoRender();
                    
                    $response = $this->getResponse();
                    $response->setHttpResponseCode($exception->getResponseCode());
                    $response->setBody($exception->getResponseMessage());
                    $response->sendResponse();
                    
                    exit;
            }

            // exception code determines whether error or info
            // see erfurt developer documentation
            if (($exception->getCode() > 0) && ($exception->getCode() < 2000) and false) {
                $this->view->heading   = 'OntoWiki Info Notice';
                $this->view->errorType = 'info';
                $this->view->code      = $exception->getCode();
            } else {
                $this->view->heading   = 'OntoWiki Error';
                $this->view->errorType = 'error';
                
                if ($exception->getCode() !== 0) {
                    $this->view->code      = $exception->getCode();
                }
            }

            $errorString = $exception->getMessage();
            
            
            
            $this->view->exceptionType = get_class($exception);  
            $this->view->exceptionFile = $exception->getFile() . '@' . $exception->getLine();
            
            $stacktrace = $exception->getTrace();
            $stacktraceString = '';
            foreach ($stacktrace as $i=>$spec) {
                $lineStr = isset($spec['line']) ? ('@'.$spec['line']) : '';                
                $stacktraceString .= '#' . $i . ': ' .$spec['class'] . $spec['type'] . $spec['function']
                                  .  $lineStr . '<br />';
                
                // foreach ($spec['args'] as $arg) {
                //                     if (is_string($arg)) {
                //                         $stacktraceString .= '    - ' . $arg . '<br />';
                //                     } else if (is_object($arg)) {
                //                         $stacktraceString .= '    - ' . get_class($arg) . '<br />';
                //                     } else {
                //                         $stacktraceString .= '    - ' . (string)$arg . '<br />';
                //                     }
                //                 }
            }
            
            $this->view->stacktrace = $stacktraceString;
        } else {
            $this->view->heading   = 'OntoWiki Error';
            $this->view->errorType = 'error';
            
            $message = current(OntoWiki::getInstance()->drawMessages());
            if ($message instanceof OntoWiki_Message) {
                $errorString = $message->getText();
            } else {
                // No message, redirect to index page
                $this->_redirect($this->config->urlBase, array('code' => 302));
            }
        }
        
        $this->view->errorText = $errorString;
    }

    protected function _gracefulError()
    {
        $requestExtra = str_replace($this->getRequest()->getBaseUrl(),
                                    '',
                                    $this->getRequest()->getRequestUri());
        $requestedUri = OntoWiki::getInstance()->config->urlBase . ltrim($requestExtra, '/');

        $createUrl = new OntoWiki_Url(array(), array());
        $createUrl->controller = 'resource';
        $createUrl->action = 'new';
        $createUrl->setParam('r', $requestedUri);
        $this->view->createUrl = (string)$createUrl;
        $this->_helper->viewRenderer->setScriptAction('404');
    }
}

