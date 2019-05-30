<?php

declare(strict_types=1);

namespace Nakukryskin\OrchidRepeaterField\Fields;

use Crypt;
use Illuminate\Support\Arr;
use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;

/**
 * Creating repeater fields based on the fields which provided to the endpoint.
 *
 * Class RepeaterField
 *
 * @method $this min($value = null)
 * @method $this max($value = null)
 * @method $this required($value = true)
 * @method $this help(string $value = null)
 * @method $this name($value = true)
 * @method $this button_label(string $value = null)
 */
class Repeater extends Field
{
    /**
     * View name.
     *
     * @var string
     */
    public $view = 'platform::fields.repeater';

    /**
     * Required Attributes.
     *
     * @var array
     */
    public $required = [
        'name',
        'layout',
    ];

    /**
     * Default attributes value.
     *
     * @var array
     */
    public $attributes = [
        'class' => 'form-control',
        'original_name' => null,
        'template' => null,
        'button_label' => null,
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    public $inlineAttributes = [
        'required',
        'min',
        'max',
        'name',
    ];


    /**
     * @param string $layout
     * @return self
     */
    public function layout(string $layout): self
    {
        if (!class_exists($layout) && !(app($layout) instanceof Rows)) {
            throw new \InvalidArgumentException(
                __('":class" does not exists or not supported. Only rows supported by repeater.', [
                    'class' => $layout
                ]));
        }

        $this->set('layout', Crypt::encryptString($layout));

        $this->addBeforeRender(function () use ($layout) {
            $value = $this->get('value');

            if (!is_iterable($value)) {
                $value = Arr::wrap($value);
            }

            $this->set('value', $value);
        });

        return $this;
    }

    /**
     * Creating an instance of the repeater field.
     *
     * @param string $name
     * @return Repeater
     */
    public static function make(string $name): self
    {
        return (new static)->name($name)
            ->set('original_name', $name)
            ->value([])
            ->set('template', 'repeater_' . str_random(32));
    }
}