<?php
/**
 * DominionEnterprises\SolveMedia\Response class used with
 * the DominionEnterprises\SolveMedia\Service class.
 * This component has been modified from it's original form to
 * encapsulate the functionality in a class based structure that is
 * compatible with class autoloading functionality.
 *
 * @author Chris Ryan <christopher.ryan@dominionenterprises.com>
 */

namespace DominionEnterprises\SolveMedia;

/**
 * A DominionEnterprises\SolveMedia\Response is returned from DominionEnterprises\SolveMedia\Service::checkAnswer()
 */
final class Response
{
    private $_isValid;
    private $_error;

    /**
     * Construct a new Response with a valid flag and error message.
     *
     * @param boolean $isValid represents if the response received was valid
     * @param string $error the error message if the response is not valid
     */
    public function __construct($isValid = false, $error = null)
    {
        $this->_isValid = $isValid;
        $this->_error = $error;
    }

    /**
     * Return if the request was valid.
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_isValid;
    }

    /**
     * Return the error message, if one is set.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_error;
    }
}
