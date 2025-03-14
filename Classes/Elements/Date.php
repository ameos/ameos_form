<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Widgets\Date\DateChoice;
use Ameos\AmeosForm\Widgets\Date\DateText;
use Ameos\AmeosForm\Widgets\WidgetInterface;

class Date extends ElementAbstract
{
    protected WidgetInterface $widget;

    /**
     * @constuctor
     *
     * @param string $absolutename absolutename
     * @param string $name name
     * @param array $configuration configuration
     * @param Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        $widget = $this->getConfigurationItem('widget') ?? 'choice';
        switch ($widget) {
            case 'text':
                $this->widget = new DateText($this, $form);
                break;
            case 'choice':
                $this->widget = new DateChoice($this, $form);
                break;
            default:
                throw new BadConfigurationException('Invalid widget type');
        }
    }

    /**
     * return rendering information
     *
     * @return  array rendering information
     */
    public function getRenderingInformation(): array
    {
        return $this->widget->getRenderingInformation(
            parent::getRenderingInformation()
        );
    }

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml(): string
    {
        return $this->widget->toHtml();
    }

    /**
     * set the value
     *
     * @param   mixed $value
     * @return  self this
     */
    public function setValue(mixed $value): self
    {
        $inputMode = $this->getConfigurationItem('input') ?? 'datetime';
        if (is_numeric($value)) {
            $value = new \DateTime('@' . $value);
        } elseif (is_string($value)) {
            $value = \DateTime::createFromFormat('Y-m-d', $value);
        } elseif (is_array($value)) {
            $value = new \DateTime($value['year'] . '-' . $value['month'] . '-' . $value['day']);
        }

        if ($inputMode === 'timestamp') {
            $value = $value->getTimestamp();
        }

        parent::setValue($value);

        return $this;
    }
}
