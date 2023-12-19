<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

interface ElementInterface
{
    /**
     * @constuctor
     *
     * @param   string  $absolutename absolutename
     * @param   string  $name name
     * @param   array   $configuration configuration
     * @param   \Ameos\AmeosForm\Form $form form
     */
    public function __construct($absolutename, $name, $configuration, $form);

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml();

    /**
     * return true if the element is valide
     *
     * @return  bool true if the element is valide
     */
    public function isValid();

    /**
     * add configuration
     *
     * @param   string  $key configuration key
     * @param   string  $value value
     * @return  ElementAbstract this
     */
    public function addConfiguration($key, $value);

    /**
     * return search field
     *
     * @return  string search field
     */
    public function getSearchField();

    /**
     * return where clause
     *
     * @return  bool|array FALSE if no search. Else array with search type and value
     */
    public function getClause();

    /**
     * set the value
     *
     * @param   string  $value value
     * @return  ElementAbstract this
     */
    public function setValue($value);

    /**
     * return the value
     *
     * @return  mixed value
     */
    public function getValue();

    /**
     * return the name
     *
     * @return  string name
     */
    public function getName();

    /**
     * return the name
     *
     * @return  string name
     */
    public function getHtmlId();

    /**
     * return errors
     *
     * @return  array errors
     */
    public function getErrors();

    /**
     * return rendering information
     *
     * @return  array rendering information
     */
    public function getRenderingInformation();

    /**
     * add configuration
     *
     * alias    addConfiguration
     * @param    string    $key configuration key
     * @param    string    $value value
     * @return     ElementAbstract this
     */
    public function with($key, $value);

    /**
     * add constraint
     *
     * @param    \Ameos\AmeosForm\Validators\ValidatorInterface $constraint
     * @return    \Ameos\AmeosForm\Form this
     */
    public function addConstraint($constraint);
}
