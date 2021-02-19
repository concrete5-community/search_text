<?php

namespace A3020\SearchText;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

final class Highlight implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * Highlight parts of a text (extended version).
     *
     * @param string $fulltext the whole text
     * @param string $highlight The text to be highlighted
     *
     * @return string|null
     */
    public function change($fulltext, $highlight)
    {
        // Remove line end characters.
        $text = @preg_replace("#\n|\r#", ' ', $fulltext);

        $matches = [];

        // Strip quotes as they mess with the regex.
        $highlight = str_replace(['"', "'", '&quot;'], '', $highlight);

        $result = null;
        $regex = '(.{0,45})' . preg_quote($highlight, '#') . '(.{0,45})';
        preg_match_all("#$regex#ui", $text, $matches);

        if (!empty($matches[0])) {
            $body_length = 0;
            $body_string = [];
            foreach ($matches[0] as $line) {
                $body_length += strlen($line);

                $r = $this->highlightedMarkup($line, $highlight);
                if ($r) {
                    $body_string[] = $r;
                }
                if ($body_length > 150) {
                    break;
                }
            }
            if (!empty($body_string)) {
                $result = (string) @implode('&hellip;<wbr>', $body_string);
            }
        }

        return $result;
    }

    /**
     * Highlight parts of a text.
     *
     * @param string $fulltext the whole text
     * @param string $highlight The text to be highlighted
     *
     * @return string
     */
    private function highlightedMarkup($fulltext, $highlight)
    {
        if (!$highlight) {
            return $fulltext;
        }

        return @preg_replace(
            '#' . preg_quote($highlight, '#') . '#ui',
            '<span style="background: yellow;">$0</span>',
            $fulltext
        );
    }
}