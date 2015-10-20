<?php

/*
 *  Emrah TOY .
 *  http://www.emrahtoy.com
 *  is@emrahtoy.com
 */

namespace Emr\Common;

/**
 * Converts GET Query for QuearyParser
 *
 * @author Emrah TOY <is@emrahtoy.com>
 */
class QueryLexer extends \Doctrine\Common\Lexer\AbstractLexer {

    // All tokens that are not valid identifiers must be < 100
    const T_NONE = 1;
    const T_INTEGER = 2;
    const T_STRING = 3;
    const T_CLOSE_PARENTHESIS = 6;
    const T_OPEN_PARENTHESIS = 7;
    const T_COMMA = 8;
    const T_DOT = 10;
    const T_EQUALS = 11;
    const T_GREATER_THAN = 12;
    const T_LOWER_THAN = 13;
    const T_NEGATE = 16;
    // All tokens that are also identifiers should be >= 100
    const T_FIELD = 100;
    const T_FIELDS = 101;
    const T_LIMIT = 102;
    const T_OFFSET = 103;
    const T_ORDER_BY = 104;
    const T_ASC = 105;
    const T_DESC = 106;

    /**
     * Creates a new query scanner object.
     *
     * @param string $input A query string.
     */
    public function __construct($input) {
        $this->setInput($input);
    }

    /**
     * @inheritdoc
     */
    protected function getCatchablePatterns() {
        return array(
            '[a-z_\\\][a-z0-9_\:\\\]*[a-z0-9_]{1}', // safe string
            '(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?', //integer, float
            "'(?:[^']|'')*'", // quoted strings
            '\?[0-9]*|:[a-z_][a-z0-9_]*' // alpha numeric
        );
    }

    /**
     * @inheritdoc
     */
    protected function getNonCatchablePatterns() {
        return array('\s+', '(.)'); // whitespace and single chars
    }

    /**
     * @inheritdoc
     */
    protected function getType(&$value) {
        $type = self::T_NONE;

        switch (true) {
            // Recognize numeric values
            case (is_numeric($value)):
                if (strpos($value, '.') !== false || stripos($value, 'e') !== false) {
                    return self::T_FLOAT;
                }

                return self::T_INTEGER;

            // Recognize quoted strings
            case ($value[0] === "'"):
                $value = str_replace("''", "'", substr($value, 1, strlen($value) - 2));

                return self::T_STRING;

            // Recognize identifiers
            case (ctype_alpha($value[0]) || $value[0] === '_'):
                $name = 'self::T_' . strtoupper($value);

                if (defined($name)) {
                    $type = constant($name);

                    if ($type > 100) {
                        return $type;
                    }
                }

                return self::T_FIELD;

            // Recognize input parameters
            case ($value[0] === '?' || $value[0] === ':'):
                return self::T_INPUT_PARAMETER;

            // Recognize symbols
            case ($value === '.'): return self::T_DOT;
            case ($value === ','): return self::T_COMMA;
            case ($value === '('): return self::T_OPEN_PARENTHESIS;
            case ($value === ')'): return self::T_CLOSE_PARENTHESIS;
            case ($value === '='): return self::T_EQUALS;
            case ($value === '>'): return self::T_GREATER_THAN;
            case ($value === '<'): return self::T_LOWER_THAN;
            case ($value === '+'): return self::T_PLUS;
            case ($value === '-'): return self::T_MINUS;
            case ($value === '*'): return self::T_MULTIPLY;
            case ($value === '/'): return self::T_DIVIDE;
            case ($value === '!'): return self::T_NEGATE;
            case ($value === '{'): return self::T_OPEN_CURLY_BRACE;
            case ($value === '}'): return self::T_CLOSE_CURLY_BRACE;

            // Default
            default:
            // Do nothing
        }

        return $type;
    }

}
