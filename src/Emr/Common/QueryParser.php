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

    /**
     * @var null|mixed
     */
    private $sub_query_field_name = null;

    /**
     * @var int
     */
    private $sub_level = 0;

    /**
     * @var array
     */
    public $tokens = array();

    /**
     * @param string $input
     */
    function __construct($input)
    {
        $this->lexer = new QueryLexer($input);
    }

    /**
     * @return array
     */
    function parse()
    {
        $value = array();
        $this->sub_level++;
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
                        $this->sub_query_field_name = $val['value'];
                    }
                    break;
                case QueryLexer::T_FIELDS:
                    if ($this->sub_query_field_name !== null) {
                        $value['fields'][$this->sub_query_field_name] = $this->parse();
                        $this->sub_query_field_name = null;
                    }
                    break;
                case QueryLexer::T_INTEGER:
                    if ($this->sub_level > 1) {
                        $this->sub_level--;
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
                    if ($this->sub_level > 1) {
                        $this->sub_level--;
                        return $value;
                    }
                    break;
            }

        }
        return $value;
    }

}
