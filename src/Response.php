<?php
/**
 * TraderInteractive\SolveMedia\Response class used with
 * the TraderInteractive\SolveMedia\Service class.
 * This component has been modified from it's original form to
 * encapsulate the functionality in a class based structure that is
 * compatible with class autoloading functionality.
 *
 * @author Chris Ryan <christopher.ryan@dominionenterprises.com>
 */

namespace TraderInteractive\SolveMedia;

/**
 * A TraderInteractive\SolveMedia\Response is returned from TraderInteractive\SolveMedia\Service::checkAnswer()
 */
final class Response
{
    /**
     * @var boolean
     */
    private $_isValid;

    /**
     * @var string
     */
    private $_error;

    /**
     * Construct a new Response with a valid flag and error message.
     *
     * @param boolean $isValid represents if the response received was valid
     * @param string $error the error message if the response is not valid
     */
    public function __construct(bool $isValid = false, string $error = null)
    {
        $this->_isValid = $isValid;
        $this->_error = $error;
    }

    /**
     * Return if the request was valid.
     *
     * @return boolean
     */
    public function valid() : bool
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
