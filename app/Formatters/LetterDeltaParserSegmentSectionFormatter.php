<?php

namespace App\Formatters;

use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\Line;

class LetterDeltaParserSegmentSectionFormatter extends BlockListener
{
    public function process(Line $line)
    {
        $segmentSection = $line->insertJsonKey('segmentSection');

        if ($segmentSection) {
            $this->pick($line, ['segmentSection' => $segmentSection['mergeTag']]);
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
            $segmentSection = $pick->segmentSection;

            if (strpos($segmentSection, '*|IF') !== false) {
                $pick->line->output = "<mj-raw>{$segmentSection}</mj-raw>";
            } else {
                $pick->line->output = "<mj-raw>{$segmentSection}</mj-raw>";
            }
            $pick->line->setDone();
        }
    }
}
