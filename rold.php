<?php




use Closure;

class Functions
{
    /** @var array<string, Closure> */
    private static array $functions = [];

    public static function isExists(string $key): bool
    {
        self::initFunctions();

        return isset(self::$functions[$key]);
    }

    public static function get(string $key): Closure
    {
        self::initFunctions();

        if (!self::isExists($key)) {
            throw new RoldException("function: '{$key}' is not exist");
        }

        return self::$functions[$key];
    }

    public static function set(string $key, Closure $value)
    {
        self::initFunctions();

        self::$functions[$key] = $value;
    }

    private static function initFunctions()
    {
        if (empty(self::$functions)) {
            self::$functions = [
                'echo' => function (Value ...$args) {
                    foreach ($args as $arg) {
                        echo $arg;
                        echo PHP_EOL;
                    }

                    return new NumberValue(0);
                },

                'abs' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(abs($args[0]->asNumber()));
                },

                'round' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(round($args[0]->asNumber()));
                },

                'sqrt' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(sqrt($args[0]->asNumber()));
                },

                'exp' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(exp($args[0]->asNumber()));
                },

                'pow' => function (Value ...$args) {
                    if (count($args) != 2) {
                        throw new RoldException('Two args expected');
                    }

                    return new NumberValue(pow($args[0]->asNumber(), $args[1]->asNumber()));
                },

                'log' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(log($args[0]->asNumber()));
                },

                'sin' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(sin($args[0]->asNumber()));
                },

                'cos' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(cos($args[0]->asNumber()));
                },

                'tan' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(tan($args[0]->asNumber()));
                },

                'asin' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(asin($args[0]->asNumber()));
                },

                'acos' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(acos($args[0]->asNumber()));
                },

                'atan' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(atan($args[0]->asNumber()));
                },

                'sinh' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(sinh($args[0]->asNumber()));
                },

                'cosh' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(cosh($args[0]->asNumber()));
                },

                'tanh' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(tanh($args[0]->asNumber()));
                },

                'asinh' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(asinh($args[0]->asNumber()));
                },

                'acosh' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(acosh($args[0]->asNumber()));
                },

                'atanh' => function (Value ...$args) {
                    if (count($args) != 1) {
                        throw new RoldException('One args expected');
                    }

                    return new NumberValue(atanh($args[0]->asNumber()));
                },
            ];
        }
    }
}

class FunctionStatement implements Statement
{
    private FunctionExpression $function;

    /**
     * FunctionStatement constructor.
     * @param FunctionExpression $function
     */
    public function __construct(FunctionExpression $function)
    {
        $this->function = $function;
    }

    public function execute()
    {
        $this->function->eval();
    }

    public function __toString(): string
    {
        return $this->function;
    }
}

class IfStatement implements Statement
{
    private Expression $expression;
    private Statement $ifStatement;
    private ?Statement $elseStatement;

    /**
     * IfStatement constructor.
     * @param Expression $expression
     * @param Statement $ifStatement
     * @param Statement|null $elseStatement
     */
    public function __construct(Expression $expression, Statement $ifStatement, ?Statement $elseStatement = null)
    {
        $this->expression = $expression;
        $this->ifStatement = $ifStatement;
        $this->elseStatement = $elseStatement;
    }

    public function execute()
    {
        $result = $this->expression->eval()->asNumber();
        if ($result != 0) {
            $this->ifStatement->execute();
        } elseif ($this->elseStatement != null) {
            $this->elseStatement->execute();
        }
    }

    public function __toString(): string
    {
        return sprintf('if %s { %s } else { %s }', $this->expression, $this->ifStatement, $this->elseStatement);
    }

}

class NumberExpression implements Expression
{
    private Value $val;

    /**
     * NumberExpression constructor.
     * @param float $val
     */
    public function __construct(float $val)
    {
        $this->val = new NumberValue($val);
    }

    public function eval(): Value
    {
        return $this->val;
    }

    public function __toString(): string
    {
        return $this->val->asString();
    }
}





class Parser
{
    private array $tokens;
    private int $pos;
    private int $size;

    private Token $eof;

    /**
     * Parser constructor.
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->size = count($tokens);
        $this->pos = 0;
        $this->eof = new Token("", TokenType::EOF);
    }

    /**
     * @return Statement
     */
    public function parse(): Statement
    {
        $result = new BlockStatement();
        while (!$this->match(TokenType::EOF)) {
            $result->add($this->statement());
        }

        return $result;
    }

    private function statement(): Statement
    {
        if ($this->match(TokenType::PRINT)) {
            return new PrintStatement($this->expression());
        }
        if ($this->match(TokenType::IF)) {
            return $this->ifStatement();
        }
        if ($this->match(TokenType::WHILE)) {
            return $this->whileStatement();
        }
        if ($this->match(TokenType::FOR)) {
            return $this->forStatement();
        }
        if ($this->match(TokenType::DO)) {
            return $this->doWhileStatement();
        }
        if ($this->match(TokenType::BREAK)) {
            return new BreakStatement();
        }
        if ($this->match(TokenType::CONTINUE)) {
            return new ContinueStatement();
        }
        // Функция как оператор
        if ($this->peek(0)->getType() == TokenType::IDENT && $this->peek(1)->getType() == TokenType::LPAREN) {
            return new FunctionStatement($this->function());
        }
        if ($this->match(TokenType::FN)) {
            return $this->userFunctionStatement();
        }

        return $this->assignStatement();
    }

    private function block(): Statement
    {
        $block = new BlockStatement();

        $this->consume(TokenType::LBRACE);
        while (!$this->match(TokenType::RBRACE)) {
            $block->add($this->statement());
        }

        return $block;
    }

    private function assignStatement(): Statement
    {
        // IDENT EQ
        $current = $this->peek(0);
        if ($this->match(TokenType::IDENT) && $this->peek(0)->getType() == TokenType::EQ) {
            $variable = $current->getText();
            $this->consume(TokenType::EQ);

            return new AssignmentStatement($variable, $this->expression());
        }

        throw new RoldException('Unknown statement');
    }

    private function ifStatement(): Statement
    {
        $condition = $this->expression();
        $ifStatement = $this->statementOrBlock();
        $elseStatement = null;
        if ($this->match(TokenType::ELSE)) {
            $elseStatement = $this->statementOrBlock();
        }

        return new IfStatement($condition, $ifStatement, $elseStatement);
    }

    private function whileStatement(): Statement
    {
        $condition = $this->expression();
        $statement = $this->statementOrBlock();

        return new WhileStatement($condition, $statement);
    }

    private function doWhileStatement(): Statement
    {
        $statement = $this->statementOrBlock();
        $this->consume(TokenType::WHILE);
        $condition = $this->expression();

        return new DoWhileStatement($statement, $condition);
    }

    private function forStatement(): Statement
    {
        $initialization = $this->assignStatement();
        $this->consume(TokenType::SEMICOLON);
        $condition = $this->expression();
        $this->consume(TokenType::SEMICOLON);
        $increment = $this->assignStatement();
        $statement = $this->statementOrBlock();

        return new ForStatement($initialization, $condition, $increment, $statement);
    }

    private function statementOrBlock(): Statement
    {
        if ($this->peek(0)->getType() == TokenType::LBRACE) {
            return $this->block();
        }

        return $this->statement();
    }

    private function userFunctionStatement(): Statement
    {
        $name = $this->consume(TokenType::IDENT)->getText();
        $this->consume(TokenType::LPAREN);
        $argNames = [];

        while (!$this->match(TokenType::RPAREN)) {
            $argNames[] = $this->consume(TokenType::IDENT)->getText();
            $this->match(TokenType::COMMA);
        }

        $body = $this->statementOrBlock();

        return new UserFunctionStatement($name, $argNames, $body);
    }

    private function function(): FunctionExpression
    {
        $name = $this->consume(TokenType::IDENT)->getText();
        $this->consume(TokenType::LPAREN);

        $args = [];
        while (!$this->match(TokenType::RPAREN)) {
            $args[] = $this->expression();
            $this->match(TokenType::COMMA);
        }

        return new FunctionExpression($name, $args);
    }

    private function expression(): Expression
    {
        return $this->logicalOr();
    }

    private function logicalOr(): Expression
    {
        $result = $this->logicalAnd();

        while (true) {
            if ($this->match(TokenType::BAR_BAR)) {
                $result = new ConditionalExpression('||', $result, $this->logicalAnd());
                continue;
            }
            break;
        }

        return $result;
    }

    private function logicalAnd(): Expression
    {
        $result = $this->equality();

        while (true) {
            if ($this->match(TokenType::AMP_AMP)) {
                $result = new ConditionalExpression('&&', $result, $this->equality());
                continue;
            }
            break;
        }

        return $result;
    }

    private function equality(): Expression
    {
        $result = $this->conditional();

        if ($this->match(TokenType::EQ_EQ)) {
            return new ConditionalExpression('==', $result, $this->conditional());
        }
        if ($this->match(TokenType::NOT_EQ)) {
            return new ConditionalExpression('!=', $result, $this->conditional());
        }

        return $result;
    }

    private function conditional(): Expression
    {
        $result = $this->additive();

        while (true) {
            if ($this->match(TokenType::LT)) {
                $result = new ConditionalExpression('<', $result, $this->additive());
                continue;
            }
            if ($this->match(TokenType::LE)) {
                $result = new ConditionalExpression('<=', $result, $this->additive());
                continue;
            }
            if ($this->match(TokenType::GT)) {
                $result = new ConditionalExpression('>', $result, $this->additive());
                continue;
            }
            if ($this->match(TokenType::GE)) {
                $result = new ConditionalExpression('>=', $result, $this->additive());
                continue;
            }
            break;
        }

        return $result;
    }

    private function additive(): Expression
    {
        $result = $this->multiplicative();

        while (true) {
            if ($this->match(TokenType::PLUS)) {
                $result = new BinaryExpression('+', $result, $this->multiplicative());
                continue;
            }
            if ($this->match(TokenType::MINUS)) {
                $result = new BinaryExpression('-', $result, $this->multiplicative());
                continue;
            }
            break;
        }

        return $result;
    }

    private function multiplicative(): Expression
    {
        $result = $this->unary();

        while (true) {
            if ($this->match(TokenType::STAR)) {
                $result = new BinaryExpression('*', $result, $this->unary());
                continue;
            }
            if ($this->match(TokenType::SLASH)) {
                $result = new BinaryExpression('/', $result, $this->unary());
                continue;
            }
            break;
        }

        return $result;
    }

    private function unary(): Expression
    {
        if ($this->match(TokenType::MINUS)) {
            return new UnaryExpression('-', $this->primary());
        }

        return $this->primary();
    }

    private function primary(): Expression
    {
        $current = $this->peek(0);
        if ($this->match(TokenType::NUMBER)) {
            return new NumberExpression(floatval($current->getText()));
        }
        if ($this->match(TokenType::HEX_NUMBER)) {
            return new NumberExpression(hexdec($current->getText()));
        }
        // Функция как выражение
        if ($this->peek(0)->getType() == TokenType::IDENT && $this->peek(1)->getType() == TokenType::LPAREN) {
            return $this->function();
        }
        if ($this->match(TokenType::IDENT)) {
            return new VariableExpression($current->getText());
        }
        if ($this->match(TokenType::STR)) {
            return new StringExpression($current->getText());
        }
        if ($this->match(TokenType::LPAREN)) {
            $result = $this->expression();
            $this->match(TokenType::RPAREN);

            return $result;
        }

        throw new RoldException('Unknown expression');
    }

    private function peek(int $relativePos)
    {
        $pos = $this->pos + $relativePos;
        if ($pos >= $this->size) {
            return $this->eof;
        }

        return $this->tokens[$pos];
    }

    private function match($tokenType): bool
    {
        $current = $this->peek(0);
        if ($tokenType !== $current->getType()) {
            return false;
        }
        $this->pos++;

        return true;
    }

    private function consume($tokenType): Token
    {
        $current = $this->peek(0);
        if ($tokenType !== $current->getType()) {
            throw new RoldException("Token {$current} doesn't match {$tokenType}");
        }
        $this->pos++;

        return $current;
    }

}

class PrintStatement implements Statement
{
    private Expression $expression;

    /**
     * PrintStatement constructor.
     * @param Expression $expression
     */
    public function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }

    public function execute()
    {
        echo $this->expression->eval()->asString();
    }

    public function __toString(): string
    {
        return 'print ' . $this->expression;
    }

}

class RoldException extends \RuntimeException
{

}




class Scanner
{
    private string $input;
    private array $tokens;
    private int $pos;
    private int $length;

    private array $OPERATOR_CHARS = [
        '+' => TokenType::PLUS,
        '-' => TokenType::MINUS,
        '*' => TokenType::STAR,
        '/' => TokenType::SLASH,

        '(' => TokenType::LPAREN,
        ')' => TokenType::RPAREN,
        '{' => TokenType::LBRACE,
        '}' => TokenType::RBRACE,
        ';' => TokenType::SEMICOLON,
        ',' => TokenType::COMMA,

        '==' => TokenType::EQ_EQ,
        '!=' => TokenType::NOT_EQ,
        '<' => TokenType::LT,
        '<=' => TokenType::LE,
        '>' => TokenType::GT,
        '>=' => TokenType::GE,

        '=' => TokenType::EQ,
        '!' => TokenType::NOT,
        '&' => TokenType::AMP,
        '|' => TokenType::BAR,

        '&&' => TokenType::AMP_AMP,
        '||' => TokenType::BAR_BAR,
    ];

    /**
     * Scanner constructor.
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
        $this->length = strlen($input);
        $this->tokens = [];
        $this->pos = 0;
    }

    public function scanTokens(): array
    {
        while ($this->pos < $this->length) {
            $current = $this->peek(0);
            if (ctype_digit($current)) {
                $this->scanNumber();
            } else if (ctype_alpha($current)) {
                $this->scanIdent();
            } else if ($current == '#') {
                $this->next();
                $this->scanHexNumber();
            } else if ($current == '"') {
                $this->scanString();
            } else if (in_array($current, array_keys($this->OPERATOR_CHARS), true)) {
                $this->scanOperator();
            } else {
                // whitespaces
                $this->next();
            }
        }

        return $this->tokens;
    }

    private function peek(int $relativePos): string
    {
        $pos = $this->pos + $relativePos;
        if ($pos >= $this->length) {
            return "\0";
        }

        return $this->input[$pos];
    }

    private function scanNumber()
    {
        $current = $this->peek(0);
        $buf = '';
        while (true) {
            if ($current == '.') {
                if (strpos($buf, '.') !== false) {
                    new RoldException('Invalid float number');
                }
            } else if (!ctype_digit($current)) {
                break;
            }
            $buf .= $current;
            $current = $this->next();
        }
        $this->addToken(TokenType::NUMBER, $buf);
    }

    private function next(): string
    {
        $this->pos++;

        return $this->peek(0);
    }

    private function addToken($type, string $text = '')
    {
        $this->tokens[] = new Token($text, $type);
    }

    private function scanIdent()
    {
        $current = $this->peek(0);
        $buf = '';
        while (true) {
            if (!ctype_alnum($current) && ($current != '_') && ($current != '$')) {
                break;
            }
            $buf .= $current;
            $current = $this->next();
        }

        switch ($buf) {
            case 'print':
                $this->addToken(TokenType::PRINT);
                break;
            case 'if':
                $this->addToken(TokenType::IF);
                break;
            case 'else':
                $this->addToken(TokenType::ELSE);
                break;
            case 'for':
                $this->addToken(TokenType::FOR);
                break;
            case 'while':
                $this->addToken(TokenType::WHILE);
                break;
            case 'do':
                $this->addToken(TokenType::DO);
                break;
            case 'break':
                $this->addToken(TokenType::BREAK);
                break;
            case 'continue':
                $this->addToken(TokenType::CONTINUE);
                break;
            case 'fn':
                $this->addToken(TokenType::FN);
                break;
            default:
                $this->addToken(TokenType::IDENT, $buf);
        }
    }

    private function scanHexNumber()
    {
        $current = $this->peek(0);
        $buf = '';
        while (ctype_xdigit($current)) {
            $buf .= $current;
            $current = $this->next();
        }
        $this->addToken(TokenType::HEX_NUMBER, $buf);
    }

    private function scanString()
    {
        $this->next(); // skip "
        $current = $this->peek(0);
        $buf = '';
        while (true) {
            if ($current == "\\") {
                $current = $this->next();
                switch ($current) {
                    case '"':
                        $current = $this->next();
                        $buf .= '"';
                        break;
                    case 'n':
                        $current = $this->next();
                        $buf .= "\n";
                        break;
                    case 't':
                        $current = $this->next();
                        $buf .= "\t";
                        break;
                    default:
                        $buf .= "\\";
                }
                continue;
            }
            if ($current == '"') {
                break;
            }
            $buf .= $current;
            $current = $this->next();
        }
        $this->next(); // skip closing "

        $this->addToken(TokenType::STR, $buf);
    }

    private function scanOperator()
    {
        $current = $this->peek(0);
        if ($current == '/') {
            if ($this->peek(1) == '/') {
                $this->next();
                $this->next();
                $this->scanComment();
                return;
            } elseif ($this->peek(1) == '*') {
                $this->next();
                $this->next();
                $this->scanMultilineComment();
                return;
            }
        }

        $buf = '';
        while (true) {
            // <=*
            if (!empty($buf) && !isset($this->OPERATOR_CHARS[$buf . $current])) {
                $this->addToken($this->OPERATOR_CHARS[$buf]);
                return;
            }
            $buf .= $current;
            $current = $this->next();
        }
    }

    private function scanComment()
    {
        $current = $this->peek(0);
        while ($current != "\n" && $current != "\r" && $current != "\0") {
            $current = $this->next();
        }
    }

    private function scanMultilineComment()
    {
        $current = $this->peek(0);
        while (true) {
            if ($current == "\0") {
                throw new RoldException('Missing close tag comment');
            }
            if ($current == '*' && $this->peek(1) == '/') break;
            $current = $this->next();
        }
        $this->next(); // skip *
        $this->next(); // skip /
    }
}

interface Statement
{
    public function execute();
    public function __toString(): string;
}

class StringExpression implements Expression
{
    private Value $val;

    /**
     * StringExpression constructor.
     * @param string $val
     */
    public function __construct(string $val)
    {
        $this->val = new StringValue($val);
    }

    public function eval(): Value
    {
        return $this->val;
    }

    public function __toString(): string
    {
        return $this->val->asString();
    }
}

class StringValue implements Value
{
    private string $value;

    /**
     * StringValue constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function asNumber(): float
    {
        return floatval($this->value);
    }

    public function asString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->asString();
    }

}


class Token
{
    private string $text;
    private $type;

    /**
     * Token constructor.
     * @param string $text
     * @param $type
     */
    public function __construct(string $text, $type)
    {
        $this->text = $text;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    public function __toString()
    {
        return sprintf('%s %s', TokenType::tokenToStr($this->type), $this->text);
    }


}



class TokenType
{
    const NUMBER = 1;
    const HEX_NUMBER = 2; // Шестнадцатеричное число
    const IDENT = 3; // идентификатор
    const STR = 4; // строка

    // keyword
    const PRINT = 5;
    const IF = 6;
    const ELSE = 7;
    const WHILE = 8;
    const FOR = 9;
    const DO = 10;
    const BREAK = 11;
    const CONTINUE = 12;
    const FN = 13;

    const PLUS = 14; // +
    const MINUS = 15; // -
    const STAR = 16; // *
    const SLASH = 17; // /
    const EQ = 18; // =
    const EQ_EQ = 19; // ==
    const NOT = 20; // !
    const NOT_EQ = 21; // !=
    const LT = 22; // <
    const LE = 23; // <=
    const GT = 24; // >
    const GE = 25; // >=
    const BAR = 26; // |
    const BAR_BAR = 27; // ||
    const AMP = 28; // &
    const AMP_AMP = 29; // &&

    const LPAREN = 30; // (
    const RPAREN = 31; // )
    const LBRACE = 32; // {
    const RBRACE = 33; // }
    const SEMICOLON = 34; // ;
    const COMMA = 35; // ,

    const EOF = 36;

    public static function tokenToStr($tokenType)
    {
        $t2s = [
            self::NUMBER => 'NUMBER',
            self::HEX_NUMBER => 'HEX_NUMBER',
            self::IDENT => 'IDENT',
            self::STR => 'STR',

            self::PRINT => 'PRINT',
            self::IF => 'IF',
            self::ELSE => 'ELSE',
            self::WHILE => 'WHILE',
            self::FOR => 'FOR',
            self::DO => 'DO',
            self::BREAK => 'BREAK',
            self::CONTINUE => 'CONTINUE',
            self::FN => 'FN',

            self::PLUS => 'PLUS',
            self::MINUS => 'MINUS',
            self::STAR => 'STAR',
            self::SLASH => 'SLASH',
            self::EQ => 'EQ',
            self::EQ_EQ => 'EQ_EQ',
            self::NOT_EQ => 'NOT_EQ',
            self::LT => 'LT',
            self::LE => 'LE',
            self::GT => 'GT',
            self::GE => 'GE',
            self::BAR => 'BAR',
            self::BAR_BAR => 'BAR_BAR',
            self::AMP => 'AMP',
            self::AMP_AMP => 'AMP_AMP',

            self::LPAREN => 'LPAREN',
            self::RPAREN => 'RPAREN',
            self::LBRACE => 'LBRACE',
            self::RBRACE => 'RBRACE',
            self::SEMICOLON => 'SEMICOLON',
            self::COMMA => 'COMMA',

            self::EOF => "\0",
        ];

        return $t2s[$tokenType];
    }
}




class UnaryExpression implements Expression
{
    private Expression $expr1;
    private string $operation;

    /**
     * UnaryExpression constructor.
     * @param Expression $expr1
     * @param string $operation
     */
    public function __construct(string $operation, Expression $expr1)
    {
        $this->operation = $operation;
        $this->expr1 = $expr1;
    }

    /**
     * @return float
     */
    public function eval(): Value
    {
        switch ($this->operation) {
            case '+':
                return new NumberValue($this->expr1->eval()->asNumber());
            case '-':
                return new NumberValue(-$this->expr1->eval()->asNumber());
            default:
                throw new RoldException("Error expression unary operation: {$this->operation}");
        }
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->operation, $this->expr1);
    }
}





class UserFunctionStatement implements Statement
{
    private string $name;
    private array $argNames;
    private Statement $body;

    /**
     * UserFunctionStatement constructor.
     * @param string $name
     * @param array $argNames
     * @param Statement $body
     */
    public function __construct(string $name, array $argNames, Statement $body)
    {
        $this->name = $name;
        $this->argNames = $argNames;
        $this->body = $body;
    }

    public function execute()
    {
        if (Functions::isExists($this->name)) {
            throw new RoldException("function '{$this->name}' is exists");
        }

        Functions::set($this->name, function (Value ...$args) {
            for ($i=0; $i<count($this->argNames); $i++) {
                /** @TODO Нужно восстанавливать значения переменных */
                Variables::set($this->argNames[$i], $args[$i]);
            }
            $this->body->execute();

            return new NumberValue(0);
        });
    }

    public function __toString(): string
    {
        return sprintf('fn (%s) { %s }', implode(',', $this->argNames), $this->body);
    }
}

interface Value
{
    public function asNumber(): float;
    public function asString(): string;
    public function __toString(): string;
}



class Variables
{
    private static array $variables = [];

    public static function get(string $key): Value
    {
        if (!self::isExists($key)) {
            throw new RoldException("Variable: '{$key}' is not exist");
        }

        return self::$variables[$key];
    }

    public static function set(string $key, Value $value)
    {
        self::$variables[$key] = $value;
    }

    public static function isExists(string $key): bool
    {
        return isset(self::$variables[$key]);
    }
}




class WhileStatement implements Statement
{
    private Expression $condition;
    private Statement $statement;

    /**
     * WhileStatement constructor.
     * @param Expression $expression
     * @param Statement $statement
     */
    public function __construct(Expression $expression, Statement $statement)
    {
        $this->condition = $expression;
        $this->statement = $statement;
    }

    public function execute()
    {
        while ($this->condition->eval()->asNumber() != 0) {
            try {
                $this->statement->execute();
            } catch (BreakStatement $e) {
                break;
            } catch (ContinueStatement $e) {
                // continue;
            }
        }
    }

    public function __toString(): string
    {
        return sprintf('while %s { %s }', $this->condition, $this->statement);
    }

}


class VariableExpression implements Expression
{
    private string $name;

    /**
     * VariableExpression constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function eval(): Value
    {
        return Variables::get($this->name);
    }

    public function __toString(): string
    {
        return sprintf('%s', $this->name);
    }


}



class NumberValue implements Value
{
    private float $value;

    /**
     * NumberValue constructor.
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function asNumber(): float
    {
        return $this->value;
    }

    public function asString(): string
    {
        return strval($this->value);
    }

    public function __toString(): string
    {
        return $this->asString();
    }
}


class ForStatement implements Statement
{
    private Statement $initialization;
    private Expression $condition;
    private Statement $increment;
    private Statement $block;

    /**
     * ForStatement constructor.
     * @param Statement $initialization
     * @param Expression $condition
     * @param Statement $increment
     * @param Statement $block
     */
    public function __construct(Statement $initialization, Expression $condition, Statement $increment, Statement $block)
    {
        $this->condition = $condition;
        $this->initialization = $initialization;
        $this->increment = $increment;
        $this->block = $block;
    }


    public function execute()
    {
        for ($this->initialization->execute(); $this->condition->eval()->asNumber() != 0; $this->increment->execute()) {
            try {
                $this->block->execute();
            } catch (BreakStatement $e) {
                break;
            } catch (ContinueStatement $e) {
                // continue;
            }
        }
    }

    public function __toString(): string
    {
        return sprintf('for (%s; %s; %s) { %s }', $this->initialization, $this->condition, $this->increment, $this->block);
    }

}

class FunctionExpression implements Expression
{
    private string $name;
    /** @var array<Expression> */
    private array $args;

    /**
     * FunctionExpression constructor.
     * @param string $name
     * @param array $args
     */
    public function __construct(string $name, array $args)
    {
        $this->name = $name;
        $this->args = $args;
    }

    /**
     * @return Value
     */
    public function eval(): Value
    {
        $values = [];
        foreach ($this->args as $arg) {
            /** @var Expression $arg */
            $values[] = $arg->eval();
        }

        return Functions::get($this->name)(...$values);
    }

    public function __toString(): string
    {
        return sprintf('%s(%s)', $this->name, implode(',', $this->args));
    }
}




class ConditionalExpression implements Expression
{
    private Expression $expr1, $expr2;
    private string $operation;

    private array $OPERATOR = [
        'PLUS',
        'MINUS',
        'MULT',
        'DIV',

        'EQUAL',
        'NOT_EQUAL',

        'LT',
        'LE',
        'GT',
        'GE',

        'AND',
        'OR',
    ];

    /**
     * ConditionalExpression constructor.
     * @param string $operation
     * @param Expression $expr1
     * @param Expression $expr2
     */
    public function __construct(string $operation, Expression $expr1, Expression $expr2)
    {
        $this->operation = $operation;
        $this->expr1 = $expr1;
        $this->expr2 = $expr2;
    }

    /**
     * @return Value
     */
    public function eval(): Value
    {
        $val1 = $this->expr1->eval();
        $val2 = $this->expr2->eval();

        if ($val1 instanceof StringValue) {
            $num1 = strcmp($val1->asString(), $val2->asString());
            $num2 = 0;
        } elseif ($val1 instanceof NumberValue) {
            $num1 = $val1->asNumber();
            $num2 = $val2->asNumber();
        } else {
            throw new RoldException("Error evaluating condition");
        }

        $result = false;
        switch ($this->operation) {
            case '==': $result = ($num1 === $num2); break;
            case '!=': $result = ($num1 !== $num2); break;
            case '>': $result = ($num1 > $num2); break;
            case '>=': $result = ($num1 >= $num2); break;
            case '<': $result = ($num1 < $num2); break;
            case '<=': $result = ($num1 <= $num2); break;
            case '&&': $result = ($num1 != 0) && ($num2 != 0); break;
            case '||': $result = ($num1 != 0) || ($num2 != 0); break;
            default:
                throw new RoldException("Error expression operation: {$this->operation}");
        }

        return new NumberValue($result ? 1 : 0);
    }

    public function __toString(): string
    {
        return sprintf('[%s %s %s]', $this->expr1, $this->operation, $this->expr2);
    }
}

class Constants
{
    const CONSTANTS = [
        'PI' => M_PI,
        'E' => M_E,
    ];

    public static function get(string $key): float
    {
        if (!self::isExists($key)) {
            throw new RoldException("Constant '{$key}' is not exist");
        }

        return self::CONSTANTS[$key];
    }

    public static function isExists(string $key): bool
    {
        return isset(self::CONSTANTS[$key]);
    }
}

class BreakStatement extends \RuntimeException implements Statement
{
    public function execute()
    {
        throw $this;
    }

    public function __toString(): string
    {
        return 'break';
    }
}

class BlockStatement implements Statement
{
    /** @var array<Statement> */
    private array $statements;

    /**
     * BlockStatement constructor.
     */
    public function __construct()
    {
        $this->statements = [];
    }

    public function add(Statement $statement)
    {
        $this->statements[] = $statement;
    }

    public function execute()
    {
        foreach ($this->statements as $statement) {
            /** @var Statement $statement */
            $statement->execute();
        }
    }

    public function __toString(): string
    {
        $result = '';
        foreach ($this->statements as $statement) {
            /** @var Statement $statement */
            $result .= $statement . PHP_EOL;
        }

        return $result;
    }
}

class AssignmentStatement implements Statement
{
    private string $variable;
    private Expression $expression;

    /**
     * AssignmentStatement constructor.
     * @param string $variable
     * @param Expression $expression
     */
    public function __construct(string $variable, Expression $expression)
    {
        $this->variable = $variable;
        $this->expression = $expression;
    }


    public function execute()
    {
        $result = $this->expression->eval();
        Variables::set($this->variable, $result);
    }

    public function __toString(): string
    {
        return sprintf('%s = %s', $this->variable, $this->expression);
    }

}


class BinaryExpression implements Expression
{
    private Expression $expr1, $expr2;
    private string $operation;

    /**
     * BinaryExpression constructor.
     * @param string $operation
     * @param Expression $expr1
     * @param Expression $expr2
     */
    public function __construct(string $operation, Expression $expr1, Expression $expr2)
    {
        $this->operation = $operation;
        $this->expr1 = $expr1;
        $this->expr2 = $expr2;
    }

    /**
     * @return Value
     */
    public function eval(): Value
    {
        /** @TODO Сделать проверку типов */
        $val1 = $this->expr1->eval();
        $val2 = $this->expr2->eval();

        // если строка
        if ($val1 instanceof StringValue) {
            $str1 = $val1->asString();
            $str2 = $val2->asString();
            switch ($this->operation) {
                case '+': return new StringValue($str1 . $str2);
                case '*':
                    $buf = '';
                    for ($i = 0; $i < intval($str2); $i++) {
                        $buf .= $str1;
                    }
                    return new StringValue($buf);
                default:
                    throw new RoldException("Error expression operation: {$this->operation}");
            }
        } elseif ($val1 instanceof NumberValue) {
            // если вещественное значение
            $num1 = $val1->asNumber();
            $num2 = $val2->asNumber();
            switch ($this->operation) {
                case '+': return new NumberValue($num1 + $num2);
                case '-': return new NumberValue($num1 - $num2);
                case '*': return new NumberValue($num1 * $num2);
                case '/':
                    if ($num2 != 0) {
                        return new NumberValue($num1 / $num2);
                    } else {
                        throw new RoldException("Error expression division is zero");
                    }
                default:
                    throw new RoldException("Error expression operation: {$this->operation}");
            }
        } else {
            throw new RoldException('Unknown type in BinaryExpression');
        }
    }

    public function __toString(): string
    {
        return sprintf('[%s %s %s]', $this->expr1, $this->operation, $this->expr2);
    }
}

interface Expression
{
    public function eval(): Value;
    public function __toString(): string;
}

class ContinueStatement extends \RuntimeException implements Statement
{
    public function execute()
    {
        throw $this;
    }

    public function __toString(): string
    {
        return 'continue';
    }
}


class DoWhileStatement implements Statement
{
    private Statement $statement;
    private Expression $condition;

    /**
     * DoWhileStatement constructor.
     * @param Statement $statement
     * @param Expression $expression
     */
    public function __construct(Statement $statement, Expression $expression)
    {
        $this->condition = $expression;
        $this->statement = $statement;
    }

    public function execute()
    {
        do {
            try {
                $this->statement->execute();
            } catch (BreakStatement $e) {
                break;
            } catch (ContinueStatement $e) {
                // continue;
            }
        } while ($this->condition->eval()->asNumber() != 0);
    }

    public function __toString(): string
    {
        return sprintf('do { %s } while ( %s )', $this->statement, $this->condition);
    }

}
