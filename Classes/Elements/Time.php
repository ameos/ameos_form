<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Widgets\Time\TimeChoice;
use Ameos\AmeosForm\Widgets\Time\TimeText;
use Ameos\AmeosForm\Widgets\WidgetInterface;

class Time extends ElementAbstract
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
        $configuration['minutestep'] = $configuration['minutestep'] ?? 1;

        parent::__construct($absolutename, $name, $configuration, $form);

        $widget = $this->getConfigurationItem('widget') ?? 'choice';
        switch ($widget) {
            case 'text':
                $this->widget = new TimeText($this, $form);
                break;
            case 'choice':
                $this->widget = new TimeChoice($this, $form);
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
        if (is_array($value)) {
            $value = implode(':', $value);
        }

        parent::setValue($value);

        return $this;
    }
}
