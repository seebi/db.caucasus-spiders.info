<?php

/**
 * This file is part of the {@link http://ontowiki.net OntoWiki} project.
 *
 * @copyright Copyright (c) 2011, {@link http://aksw.org AKSW}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * OntoWiki OutputDivContent view helper
 *
 * outputs the content of a specific property of a given resource as an RDFa 
 * annotated div-container with (optional) given css classes
 *
 * @category OntoWiki
 * @copyright Copyright (c) 2011, {@link http://aksw.org AKSW}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class Site_View_Helper_OutputDivContent extends Zend_View_Helper_Abstract
{
    // current view, injected with setView from Zend
    public $view;

    /*
     * view setter (dev zone article: http://devzone.zend.com/article/3412)
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function outputDivContent($desc = null, $contentProperties = null, $css = '')
    {

        if (!$desc) {
            echo '';
            return;
        }

        // used default property resources
        if (!$contentProperties) {
            $contentProperties = array();
            $contentProperties[] = 'http://aksw.org/schema/content';
            $contentProperties[] = 'http://lod2.eu/schema/content';
            $contentProperties[] = 'http://rdfs.org/sioc/ns#content';
            $contentProperties[] = 'http://purl.org/dc/terms/description';
            $contentProperties[] = 'http://aksw.org/schema/abstract';
        }

        // select the main property from existing ones
        $mainProperty = null; // the URI of the main content property
        foreach ($contentProperties as $contentProperty) {
            if (isset($desc[$contentProperty])) {
                $mainProperty = $contentProperty;
                break;
            }
        }

        // filter and render the (first) literal value of the main property
        // TODO: striptags and tidying as extension
        if ($mainProperty) {
            $firstLiteral = $desc[$mainProperty][0];
            $literalValue = $firstLiteral['value'];

            // filter by using available extensions
            if (isset($firstLiteral['datatype'])) {
                $datatype = $firstLiteral['datatype'];
                $content = $this->view->displayLiteralPropertyValue(
                    $literalValue, $mainProperty, $datatype);
            } else {
                $content = $this->view->displayLiteralPropertyValue(
                    $literalValue, $mainProperty);
            }

            // render as div element with RDFa annotations
            echo '<div property="'. $this->view->curie($mainProperty) . '">';
            echo $content . '</div>' . PHP_EOL;
        }

    }
}