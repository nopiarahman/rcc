<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Membersihkan HTML dari editor teks (CKEditor) sebelum ditampilkan ke pembeli.
 * Hanya tag format yang diizinkan dan semua atribut dibuang (kecuali href aman pada <a>),
 * sehingga <script>, onclick=, javascript: dan sejenisnya tidak bisa dijalankan.
 */
class HtmlSanitizer
{
    protected const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'blockquote', 'hr',
        'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'div', 'a', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
    ];

    /** Tag yang dibuang beserta seluruh isinya. */
    protected const DROP_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'noscript', 'template', 'svg', 'math', 'form', 'textarea', 'select'];

    public static function clean(?string $html): string
    {
        $html = (string) $html;
        if (trim($html) === '') {
            return '';
        }

        // Teks biasa (tanpa tag): cukup di-escape dan pertahankan baris baru
        if (strip_tags($html) === $html) {
            return nl2br(e($html));
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('__root');
        if (! $root) {
            return nl2br(e(strip_tags($html)));
        }

        static::cleanChildren($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }

    protected static function cleanChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (in_array($tag, static::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);
                continue;
            }

            static::cleanChildren($child);

            if (! in_array($tag, static::ALLOWED_TAGS, true)) {
                // Tag tidak dikenal: buang tag-nya tetapi pertahankan isinya
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            $href = $tag === 'a' ? trim((string) $child->getAttribute('href')) : null;
            foreach (iterator_to_array($child->attributes) as $attribute) {
                $child->removeAttribute($attribute->nodeName);
            }

            if ($tag === 'a' && $href !== null && preg_match('#^(https?://|mailto:|tel:)#i', $href)) {
                $child->setAttribute('href', $href);
                $child->setAttribute('target', '_blank');
                $child->setAttribute('rel', 'noopener nofollow');
            }
        }
    }
}
