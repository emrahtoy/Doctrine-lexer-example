<?php
/*
 *  Emrah TOY .
 *  http://www.emrahtoy.com
 *  code@emrahtoy.com
 */
namespace Emr\Common;

/**
 * Converts Lexer to SQL
 *
 * @author Emrah TOY <code@emrahtoy.com>
 */
class QueryParser
{
    /*
     * Lexer object
     * 
     * @var Emr\Common\QueryLexer
     */

    public $lexer;

    private $subQueryFieldName = null;

    /**
     * @var int
     */
    private $subLevel = 0;

    /**
     * @var array
     */
    public $tokens = array();

    function __construct($input)
    {
        $this->lexer = new QueryLexer($input);
    }

    function parse()
    {
        $value = array();
        $this->subLevel++;
        $this->lexer->moveNext();

        while ($this->lexer->lookahead !== null) {
            $this->lexer->moveNext();
            if ($this->lexer->token === null)
                continue; // TODO : there is an empty element in array

            $val = $this->lexer->token;
            $this->tokens[] = $val;
            switch ($val['type']) {
                case QueryLexer::T_FIELD:
                    if (!$this->lexer->isNextToken(QueryLexer::T_DOT)) {
                        $value['fields'][] = $val['value'];
                    } else {
//                        var_dump($this->lexer->glimpse());
                        $this->subQueryFieldName = $val['value'];
                    }
                    break;
                case QueryLexer::T_FIELDS:
                    if ($this->subQueryFieldName !== null) {
                        $value['fields'][$this->subQueryFieldName] = $this->parse();
                        $this->subQueryFieldName = null;
                    }
                    break;
                case QueryLexer::T_INTEGER:
                    if ($this->subLevel > 1) {
                        $this->subLevel--;
                    }
                    return (int)$val['value'];

                case QueryLexer::T_ORDER_BY:
                    $value['order_by'] = $this->parse();
                    break;
                case QueryLexer::T_ASC:
                    $value['order'] = 'ASC';
                    break;
                case QueryLexer::T_DESC:
                    $value['order'] = 'DESC';
                    break;
                case QueryLexer::T_LIMIT:
                    $value['limit'] = $this->parse();
                    break;
                case QueryLexer::T_OFFSET:
                    $value['offset'] = $this->parse();
                    break;
                case QueryLexer::T_OPEN_PARENTHESIS:
                    break;
                case QueryLExer::T_CLOSE_PARENTHESIS:
                    if ($this->subLevel > 1) {
                        $this->subLevel--;
                        return $value;
                    }
                    break;
            }

        }
        return $value;
    }

}
