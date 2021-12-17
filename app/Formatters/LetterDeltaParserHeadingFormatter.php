<?php

namespace App\Formatters;

use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\Line;
use nadar\quill\listener\Text;

class LetterDeltaParserHeadingFormatter extends BlockListener
{
    /**
     * @var array Supported header levels.
     * @since 1.2.0
     */
    public $levels = [1, 2, 3, 4, 5, 6];

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $heading = $line->getAttribute('header');
        if ($heading) {
            $this->pick($line, ['heading' => $heading]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception for unknown heading levels {@since 1.2.0}
     */
    public function render(Lexer $lexer)
    {
        foreach ($this->picks() as $pick) {
            if (!in_array($pick->heading, $this->levels)) {
                // prevent html injection in case the attribute is user input
                throw new \Exception('An unknown heading level "' . $pick->heading . '" has been detected.');
            }
        }

        $this->wrapElement('<mj-text css-class="heading heading--h{heading}"><h{heading}>{__buffer__}</h{heading}></mj-text>', ['heading']);
    }
}
