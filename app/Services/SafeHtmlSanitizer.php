<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class SafeHtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'h2', 'h3', 'h4', 'strong', 'b', 'em', 'i', 'u',
        'ul', 'ol', 'li', 'blockquote', 'a', 'img', 'figure', 'figcaption',
        'code', 'pre', 'hr', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
    ];

    private const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form',
        'input', 'button', 'textarea', 'select', 'option', 'meta', 'link', 'base',
        'template', 'noscript',
    ];

    private const GLOBAL_ATTRIBUTES = ['title'];

    private const TAG_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height', 'loading'],
        'th' => ['colspan', 'rowspan', 'scope'],
        'td' => ['colspan', 'rowspan'],
    ];

    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (! class_exists(DOMDocument::class)) {
            return htmlspecialchars(strip_tags($html), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        try {
            $document->loadHTML(
                '<?xml encoding="UTF-8"><div id="mtd-sanitize-root">'.$html.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
            );

            $root = $document->getElementById('mtd-sanitize-root');

            if (! $root) {
                return '';
            }

            $this->sanitizeChildren($root);

            $output = '';
            foreach ($root->childNodes as $child) {
                $output .= $document->saveHTML($child);
            }

            return $output;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        for ($node = $parent->firstChild; $node !== null;) {
            $next = $node->nextSibling;

            if ($node instanceof DOMElement) {
                $tag = strtolower($node->tagName);

                if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                    $parent->removeChild($node);
                    $node = $next;
                    continue;
                }

                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    $this->sanitizeChildren($node);
                    while ($node->firstChild) {
                        $parent->insertBefore($node->firstChild, $node);
                    }
                    $parent->removeChild($node);
                    $node = $next;
                    continue;
                }

                $this->sanitizeAttributes($node, $tag);
                $this->sanitizeChildren($node);
            }

            $node = $next;
        }
    }

    private function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowed = array_merge(self::GLOBAL_ATTRIBUTES, self::TAG_ATTRIBUTES[$tag] ?? []);
        $remove = [];

        foreach ($element->attributes as $attribute) {
            $name = strtolower($attribute->name);

            if (str_starts_with($name, 'on') || ! in_array($name, $allowed, true)) {
                $remove[] = $name;
            }
        }

        foreach ($remove as $name) {
            $element->removeAttribute($name);
        }

        if ($tag === 'a' && $element->hasAttribute('href')) {
            $href = $this->safeUrl($element->getAttribute('href'), allowMailAndTel: true);
            if ($href === null) {
                $element->removeAttribute('href');
            } else {
                $element->setAttribute('href', $href);
            }

            if ($element->getAttribute('target') === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
            } else {
                $element->removeAttribute('target');
                $element->removeAttribute('rel');
            }
        }

        if ($tag === 'img' && $element->hasAttribute('src')) {
            $src = $this->safeUrl($element->getAttribute('src'));
            if ($src === null) {
                $element->removeAttribute('src');
            } else {
                $element->setAttribute('src', $src);
                $element->setAttribute('loading', 'lazy');
            }
        }
    }

    private function safeUrl(string $url, bool $allowMailAndTel = false): ?string
    {
        $url = trim(preg_replace('/[\x00-\x20\x7F]+/u', '', $url) ?? '');

        if ($url === '' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url === '' ? null : $url;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $allowedSchemes = ['http', 'https'];

        if ($allowMailAndTel) {
            $allowedSchemes[] = 'mailto';
            $allowedSchemes[] = 'tel';
        }

        return in_array($scheme, $allowedSchemes, true) ? $url : null;
    }
}
