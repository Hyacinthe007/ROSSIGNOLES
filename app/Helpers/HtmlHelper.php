<?php
/**
 * Helper pour le HTML
 */

class HtmlHelper {
    
    /**
     * Génère un lien
     */
    public static function link($text, $url, $attributes = []) {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"" . htmlspecialchars($value) . "\"";
        }
        
        return "<a href=\"{$url}\"{$attrs}>{$text}</a>";
    }
    
    /**
     * Génère un bouton
     */
    public static function button($text, $type = 'button', $attributes = []) {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"" . htmlspecialchars($value) . "\"";
        }
        
        return "<button type=\"{$type}\"{$attrs}>{$text}</button>";
    }
    
    /**
     * Génère un select
     */
    public static function select($name, $options, $selected = null, $attributes = []) {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"" . htmlspecialchars($value) . "\"";
        }
        
        $html = "<select name=\"{$name}\"{$attrs}>";
        
        foreach ($options as $value => $label) {
            $selectedAttr = ($value == $selected) ? ' selected' : '';
            $html .= "<option value=\"" . htmlspecialchars($value) . "\"{$selectedAttr}>";
            $html .= htmlspecialchars($label);
            $html .= "</option>";
        }
        
        $html .= "</select>";
        return $html;
    }
}

