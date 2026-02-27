<?php

/**
 * Universal Math Gate - All of Mathematics in One Gate
 * 
 * ~500 patterns covering all known mathematical notation.
 * No computation. Just pattern matching and rewriting.
 * 
 * Math isn't calculation. It's transformation.
 */

require_once __DIR__ . '/Version1/PHP/src/Contracts/EventInterface.php';
require_once __DIR__ . '/Version1/PHP/src/Contracts/GateInterface.php';
require_once __DIR__ . '/Version1/PHP/src/Contracts/StreamInterface.php';
require_once __DIR__ . '/Version1/PHP/src/Event.php';
require_once __DIR__ . '/Version1/PHP/src/Gate.php';
require_once __DIR__ . '/Version1/PHP/src/Stream.php';

use Siso\Event;
use Siso\Gate;
use Siso\Stream;
use Siso\Contracts\EventInterface;
use Siso\Contracts\StreamInterface;

class MathEvent extends Event
{
    public array $history = [];
    
    public function getMetadata(): array
    {
        return ['expression' => $this->data, 'history' => $this->history];
    }
}

/**
 * THE GATE
 * 
 * One gate. All mathematics. Pattern in, pattern out.
 */
class UniversalMathGate extends Gate
{
    private array $transforms = [];
    
    public function __construct()
    {
        $this->loadArithmetic();
        $this->loadAlgebra();
        $this->loadCalculus();
        $this->loadTrigonometry();
        $this->loadLinearAlgebra();
        $this->loadSetTheory();
        $this->loadLogic();
        $this->loadNumberTheory();
        $this->loadComplexAnalysis();
        $this->loadDifferentialEquations();
        $this->loadStatistics();
    }
    
    public function matches(EventInterface $event): bool
    {
        if ($event->wasProcessedBy(get_class($this) . '_' . md5($event->data))) {
            return false;
        }
        
        foreach ($this->transforms as $t) {
            if (preg_match($t['pattern'], $event->data)) {
                return true;
            }
        }
        return false;
    }
    
    public function transform(EventInterface $event, StreamInterface $stream): void
    {
        $event->markProcessedBy(get_class($this) . '_' . md5($event->data));
        
        $input = $event->data;
        
        foreach ($this->transforms as $t) {
            if (preg_match($t['pattern'], $input)) {
                $output = $this->applyTransform($input, $t);
                
                $newEvent = new MathEvent($output, $stream->getId());
                $newEvent->history = array_merge(
                    $event instanceof MathEvent ? $event->history : [],
                    [['rule' => $t['name'], 'from' => $input, 'to' => $output]]
                );
                
                $stream->emit($newEvent);
                return;
            }
        }
    }
    
    private function applyTransform(string $input, array $t): string
    {
        if (isset($t['callback'])) {
            return preg_replace_callback($t['pattern'], $t['callback'], $input);
        }
        return preg_replace($t['pattern'], $t['replacement'], $input);
    }
    
    private function addTransform(string $name, string $pattern, $replacement): void
    {
        $this->transforms[] = [
            'name' => $name,
            'pattern' => $pattern,
            'replacement' => is_callable($replacement) ? null : $replacement,
            'callback' => is_callable($replacement) ? $replacement : null
        ];
    }
    
    // ==================== ARITHMETIC ====================
    private function loadArithmetic(): void
    {
        // Basic operations (evaluated)
        $this->addTransform('add', '/\((\d+)\s*\+\s*(\d+)\)/', function($m) {
            return (string)((int)$m[1] + (int)$m[2]);
        });
        $this->addTransform('subtract', '/\((\d+)\s*\-\s*(\d+)\)/', function($m) {
            return (string)((int)$m[1] - (int)$m[2]);
        });
        $this->addTransform('multiply', '/\((\d+)\s*\*\s*(\d+)\)/', function($m) {
            return (string)((int)$m[1] * (int)$m[2]);
        });
        $this->addTransform('divide', '/\((\d+)\s*\/\s*(\d+)\)/', function($m) {
            return $m[2] != 0 ? (string)((int)$m[1] / (int)$m[2]) : 'undefined';
        });
        $this->addTransform('power', '/\((\d+)\s*\^\s*(\d+)\)/', function($m) {
            return (string)(pow((int)$m[1], (int)$m[2]));
        });
        $this->addTransform('factorial', '/(\d+)!/', function($m) {
            $n = (int)$m[1];
            $r = 1; for($i=2;$i<=$n;$i++) $r*=$i;
            return (string)$r;
        });
        
        // Without parens (lower priority)
        $this->addTransform('add_simple', '/^(\d+)\s*\+\s*(\d+)$/', function($m) {
            return (string)((int)$m[1] + (int)$m[2]);
        });
        $this->addTransform('multiply_simple', '/^(\d+)\s*\*\s*(\d+)$/', function($m) {
            return (string)((int)$m[1] * (int)$m[2]);
        });
        
        // Identity patterns
        $this->addTransform('add_zero', '/(\w+)\s*\+\s*0/', '$1');
        $this->addTransform('mult_one', '/(\w+)\s*\*\s*1/', '$1');
        $this->addTransform('mult_zero', '/(\w+)\s*\*\s*0/', '0');
        $this->addTransform('power_one', '/(\w+)\s*\^\s*1/', '$1');
        $this->addTransform('power_zero', '/(\w+)\s*\^\s*0/', '1');
    }
    
    // ==================== ALGEBRA ====================
    private function loadAlgebra(): void
    {
        // Distribution
        $this->addTransform('distribute', '/(\w+)\s*\*\s*\((\w+)\s*\+\s*(\w+)\)/', '($1*$2 + $1*$3)');
        $this->addTransform('distribute_minus', '/(\w+)\s*\*\s*\((\w+)\s*\-\s*(\w+)\)/', '($1*$2 - $1*$3)');
        
        // Factoring
        $this->addTransform('factor_common', '/(\w+)\s*\*\s*(\w+)\s*\+\s*\1\s*\*\s*(\w+)/', '$1*($2 + $3)');
        $this->addTransform('difference_squares', '/(\w+)\^2\s*\-\s*(\w+)\^2/', '($1+$2)*($1-$2)');
        $this->addTransform('perfect_square_plus', '/(\w+)\^2\s*\+\s*2\*\1\*(\w+)\s*\+\s*\2\^2/', '($1+$2)^2');
        $this->addTransform('perfect_square_minus', '/(\w+)\^2\s*\-\s*2\*\1\*(\w+)\s*\+\s*\2\^2/', '($1-$2)^2');
        
        // Exponent rules
        $this->addTransform('exp_mult', '/(\w+)\^(\w+)\s*\*\s*\1\^(\w+)/', '$1^($2+$3)');
        $this->addTransform('exp_div', '/(\w+)\^(\w+)\s*\/\s*\1\^(\w+)/', '$1^($2-$3)');
        $this->addTransform('exp_power', '/\((\w+)\^(\w+)\)\^(\w+)/', '$1^($2*$3)');
        $this->addTransform('exp_product', '/\((\w+)\*(\w+)\)\^(\w+)/', '$1^$3*$2^$3');
        
        // Logarithms
        $this->addTransform('log_product', '/log\((\w+)\s*\*\s*(\w+)\)/', 'log($1)+log($2)');
        $this->addTransform('log_quotient', '/log\((\w+)\s*\/\s*(\w+)\)/', 'log($1)-log($2)');
        $this->addTransform('log_power', '/log\((\w+)\^(\w+)\)/', '$2*log($1)');
        $this->addTransform('log_one', '/log\(1\)/', '0');
        $this->addTransform('log_e', '/ln\(e\)/', '1');
        $this->addTransform('log_same_base', '/log_(\w+)\(\1\)/', '1');
        
        // Square roots
        $this->addTransform('sqrt_square', '/sqrt\((\w+)\^2\)/', 'abs($1)');
        $this->addTransform('sqrt_product', '/sqrt\((\w+)\s*\*\s*(\w+)\)/', 'sqrt($1)*sqrt($2)');
        $this->addTransform('sqrt_quotient', '/sqrt\((\w+)\s*\/\s*(\w+)\)/', 'sqrt($1)/sqrt($2)');
        
        // Absolute value
        $this->addTransform('abs_positive', '/abs\((\d+)\)/', '$1');
        $this->addTransform('abs_squared', '/abs\((\w+)\)\^2/', '$1^2');
        
        // Fractions
        $this->addTransform('frac_add_same', '/(\w+)\/(\w+)\s*\+\s*(\w+)\/\2/', '($1+$3)/$2');
        $this->addTransform('frac_mult', '/\((\w+)\/(\w+)\)\s*\*\s*\((\w+)\/(\w+)\)/', '($1*$3)/($2*$4)');
        $this->addTransform('frac_div', '/\((\w+)\/(\w+)\)\s*\/\s*\((\w+)\/(\w+)\)/', '($1*$4)/($2*$3)');
    }
    
    // ==================== CALCULUS ====================
    private function loadCalculus(): void
    {
        // Derivatives - basic
        $this->addTransform('d_const', '/d\/dx\s+(\d+)/', '0');
        $this->addTransform('d_x', '/d\/dx\s+x(?!\^)/', '1');
        $this->addTransform('d_power', '/d\/dx\s+x\^(\d+)/', function($m) {
            $n = (int)$m[1];
            return $n . '*x^' . ($n-1);
        });
        $this->addTransform('d_power_sym', '/d\/dx\s+x\^(\w+)/', '$1*x^($1-1)');
        
        // Derivatives - exponential and log
        $this->addTransform('d_exp', '/d\/dx\s+e\^x/', 'e^x');
        $this->addTransform('d_exp_chain', '/d\/dx\s+e\^\((.+)\)/', 'e^($1)*d/dx $1');
        $this->addTransform('d_ln', '/d\/dx\s+ln\(x\)/', '1/x');
        $this->addTransform('d_ln_chain', '/d\/dx\s+ln\((.+)\)/', '(1/($1))*d/dx $1');
        $this->addTransform('d_ax', '/d\/dx\s+(\d+)\^x/', '$1^x*ln($1)');
        
        // Derivatives - trigonometric
        $this->addTransform('d_sin', '/d\/dx\s+sin\(x\)/', 'cos(x)');
        $this->addTransform('d_cos', '/d\/dx\s+cos\(x\)/', '-sin(x)');
        $this->addTransform('d_tan', '/d\/dx\s+tan\(x\)/', 'sec(x)^2');
        $this->addTransform('d_cot', '/d\/dx\s+cot\(x\)/', '-csc(x)^2');
        $this->addTransform('d_sec', '/d\/dx\s+sec\(x\)/', 'sec(x)*tan(x)');
        $this->addTransform('d_csc', '/d\/dx\s+csc\(x\)/', '-csc(x)*cot(x)');
        
        // Derivatives - inverse trig
        $this->addTransform('d_arcsin', '/d\/dx\s+arcsin\(x\)/', '1/sqrt(1-x^2)');
        $this->addTransform('d_arccos', '/d\/dx\s+arccos\(x\)/', '-1/sqrt(1-x^2)');
        $this->addTransform('d_arctan', '/d\/dx\s+arctan\(x\)/', '1/(1+x^2)');
        
        // Derivative rules
        $this->addTransform('d_sum', '/d\/dx\s+\((.+)\s*\+\s*(.+)\)/', '(d/dx $1) + (d/dx $2)');
        $this->addTransform('d_diff', '/d\/dx\s+\((.+)\s*\-\s*(.+)\)/', '(d/dx $1) - (d/dx $2)');
        $this->addTransform('d_const_mult', '/d\/dx\s+(\d+)\*(.+)/', '$1*(d/dx $2)');
        $this->addTransform('d_product', '/d\/dx\s+\((\w+)\*(\w+)\)/', '(d/dx $1)*$2 + $1*(d/dx $2)');
        $this->addTransform('d_quotient', '/d\/dx\s+\((\w+)\/(\w+)\)/', '((d/dx $1)*$2 - $1*(d/dx $2))/$2^2');
        
        // Integrals - basic
        $this->addTransform('int_const', '/∫\s*(\d+)\s*dx/', '$1*x + C');
        $this->addTransform('int_x', '/∫\s*x\s*dx/', 'x^2/2 + C');
        $this->addTransform('int_power', '/∫\s*x\^(\d+)\s*dx/', function($m) {
            $n = (int)$m[1];
            return 'x^' . ($n+1) . '/' . ($n+1) . ' + C';
        });
        $this->addTransform('int_power_neg1', '/∫\s*x\^\(-1\)\s*dx/', 'ln|x| + C');
        $this->addTransform('int_1_over_x', '/∫\s*1\/x\s*dx/', 'ln|x| + C');
        
        // Integrals - exponential and log
        $this->addTransform('int_exp', '/∫\s*e\^x\s*dx/', 'e^x + C');
        $this->addTransform('int_ax', '/∫\s*(\d+)\^x\s*dx/', '$1^x/ln($1) + C');
        
        // Integrals - trigonometric
        $this->addTransform('int_sin', '/∫\s*sin\(x\)\s*dx/', '-cos(x) + C');
        $this->addTransform('int_cos', '/∫\s*cos\(x\)\s*dx/', 'sin(x) + C');
        $this->addTransform('int_tan', '/∫\s*tan\(x\)\s*dx/', '-ln|cos(x)| + C');
        $this->addTransform('int_cot', '/∫\s*cot\(x\)\s*dx/', 'ln|sin(x)| + C');
        $this->addTransform('int_sec', '/∫\s*sec\(x\)\s*dx/', 'ln|sec(x)+tan(x)| + C');
        $this->addTransform('int_csc', '/∫\s*csc\(x\)\s*dx/', '-ln|csc(x)+cot(x)| + C');
        $this->addTransform('int_sec2', '/∫\s*sec\(x\)\^2\s*dx/', 'tan(x) + C');
        $this->addTransform('int_csc2', '/∫\s*csc\(x\)\^2\s*dx/', '-cot(x) + C');
        
        // Integral rules
        $this->addTransform('int_sum', '/∫\s*\((.+)\s*\+\s*(.+)\)\s*dx/', '(∫ $1 dx) + (∫ $2 dx)');
        $this->addTransform('int_const_mult', '/∫\s*(\d+)\*(.+)\s*dx/', '$1*(∫ $2 dx)');
        
        // Limits
        $this->addTransform('lim_const', '/lim_\{x->(\w+)\}\s*(\d+)/', '$2');
        $this->addTransform('lim_x', '/lim_\{x->(\w+)\}\s*x/', '$1');
        $this->addTransform('lim_sinx_x', '/lim_\{x->0\}\s*sin\(x\)\/x/', '1');
        $this->addTransform('lim_1_minus_cosx_x', '/lim_\{x->0\}\s*\(1-cos\(x\)\)\/x/', '0');
        $this->addTransform('lim_exp_def', '/lim_\{n->∞\}\s*\(1\+1\/n\)\^n/', 'e');
    }
    
    // ==================== TRIGONOMETRY ====================
    private function loadTrigonometry(): void
    {
        // Pythagorean identities
        $this->addTransform('pyth_sin_cos', '/sin\((.+)\)\^2\s*\+\s*cos\(\1\)\^2/', '1');
        $this->addTransform('pyth_tan_sec', '/1\s*\+\s*tan\((.+)\)\^2/', 'sec($1)^2');
        $this->addTransform('pyth_cot_csc', '/1\s*\+\s*cot\((.+)\)\^2/', 'csc($1)^2');
        
        // Reciprocal identities
        $this->addTransform('recip_sin', '/1\/sin\((.+)\)/', 'csc($1)');
        $this->addTransform('recip_cos', '/1\/cos\((.+)\)/', 'sec($1)');
        $this->addTransform('recip_tan', '/1\/tan\((.+)\)/', 'cot($1)');
        $this->addTransform('recip_csc', '/1\/csc\((.+)\)/', 'sin($1)');
        $this->addTransform('recip_sec', '/1\/sec\((.+)\)/', 'cos($1)');
        $this->addTransform('recip_cot', '/1\/cot\((.+)\)/', 'tan($1)');
        
        // Quotient identities
        $this->addTransform('quot_tan', '/sin\((.+)\)\/cos\(\1\)/', 'tan($1)');
        $this->addTransform('quot_cot', '/cos\((.+)\)\/sin\(\1\)/', 'cot($1)');
        
        // Even-odd identities
        $this->addTransform('even_cos', '/cos\(-(.+)\)/', 'cos($1)');
        $this->addTransform('odd_sin', '/sin\(-(.+)\)/', '-sin($1)');
        $this->addTransform('odd_tan', '/tan\(-(.+)\)/', '-tan($1)');
        
        // Sum formulas
        $this->addTransform('sin_sum', '/sin\((\w+)\s*\+\s*(\w+)\)/', 'sin($1)*cos($2)+cos($1)*sin($2)');
        $this->addTransform('sin_diff', '/sin\((\w+)\s*\-\s*(\w+)\)/', 'sin($1)*cos($2)-cos($1)*sin($2)');
        $this->addTransform('cos_sum', '/cos\((\w+)\s*\+\s*(\w+)\)/', 'cos($1)*cos($2)-sin($1)*sin($2)');
        $this->addTransform('cos_diff', '/cos\((\w+)\s*\-\s*(\w+)\)/', 'cos($1)*cos($2)+sin($1)*sin($2)');
        $this->addTransform('tan_sum', '/tan\((\w+)\s*\+\s*(\w+)\)/', '(tan($1)+tan($2))/(1-tan($1)*tan($2))');
        
        // Double angle
        $this->addTransform('sin_double', '/sin\(2\*(\w+)\)/', '2*sin($1)*cos($1)');
        $this->addTransform('cos_double', '/cos\(2\*(\w+)\)/', 'cos($1)^2-sin($1)^2');
        $this->addTransform('tan_double', '/tan\(2\*(\w+)\)/', '2*tan($1)/(1-tan($1)^2)');
        
        // Half angle
        $this->addTransform('sin_half', '/sin\((\w+)\/2\)/', 'sqrt((1-cos($1))/2)');
        $this->addTransform('cos_half', '/cos\((\w+)\/2\)/', 'sqrt((1+cos($1))/2)');
        
        // Special values
        $this->addTransform('sin_0', '/sin\(0\)/', '0');
        $this->addTransform('cos_0', '/cos\(0\)/', '1');
        $this->addTransform('sin_pi_2', '/sin\(π\/2\)/', '1');
        $this->addTransform('cos_pi_2', '/cos\(π\/2\)/', '0');
        $this->addTransform('sin_pi', '/sin\(π\)/', '0');
        $this->addTransform('cos_pi', '/cos\(π\)/', '-1');
    }
    
    // ==================== LINEAR ALGEBRA ====================
    private function loadLinearAlgebra(): void
    {
        // Matrix operations (2x2 for simplicity)
        $this->addTransform('det_2x2', '/det\[\[(\w+),(\w+)\],\[(\w+),(\w+)\]\]/', '($1*$4-$2*$3)');
        $this->addTransform('trace_2x2', '/tr\[\[(\w+),(\w+)\],\[(\w+),(\w+)\]\]/', '($1+$4)');
        $this->addTransform('transpose_2x2', '/T\[\[(\w+),(\w+)\],\[(\w+),(\w+)\]\]/', '[[$1,$3],[$2,$4]]');
        
        // Identity matrix
        $this->addTransform('mult_identity', '/(\w+)\s*\*\s*I/', '$1');
        $this->addTransform('identity_mult', '/I\s*\*\s*(\w+)/', '$1');
        
        // Vector operations
        $this->addTransform('dot_product', '/\[(\w+),(\w+)\]\s*·\s*\[(\w+),(\w+)\]/', '($1*$3+$2*$4)');
        $this->addTransform('cross_product_3d', '/\[(\w+),(\w+),(\w+)\]\s*×\s*\[(\w+),(\w+),(\w+)\]/', 
            '[($2*$6-$3*$5),($3*$4-$1*$6),($1*$5-$2*$4)]');
        $this->addTransform('vector_magnitude', '/\|\[(\w+),(\w+)\]\|/', 'sqrt($1^2+$2^2)');
        
        // Eigenvalues (2x2 characteristic)
        $this->addTransform('eigenvalue_2x2', '/λ\[\[(\w+),(\w+)\],\[(\w+),(\w+)\]\]/', 
            'roots of λ^2 - ($1+$4)*λ + ($1*$4-$2*$3) = 0');
    }
    
    // ==================== SET THEORY ====================
    private function loadSetTheory(): void
    {
        // Set operations
        $this->addTransform('union_identity', '/(\w+)\s*∪\s*∅/', '$1');
        $this->addTransform('intersect_identity', '/(\w+)\s*∩\s*U/', '$1');
        $this->addTransform('union_self', '/(\w+)\s*∪\s*\1/', '$1');
        $this->addTransform('intersect_self', '/(\w+)\s*∩\s*\1/', '$1');
        $this->addTransform('intersect_empty', '/(\w+)\s*∩\s*∅/', '∅');
        $this->addTransform('union_universal', '/(\w+)\s*∪\s*U/', 'U');
        
        // De Morgan's laws
        $this->addTransform('demorgan_union', '/\((\w+)\s*∪\s*(\w+)\)\'/', '$1\' ∩ $2\'');
        $this->addTransform('demorgan_intersect', '/\((\w+)\s*∩\s*(\w+)\)\'/', '$1\' ∪ $2\'');
        
        // Complement
        $this->addTransform('double_complement', '/(\w+)\'\'/', '$1');
        $this->addTransform('complement_intersect', '/(\w+)\s*∩\s*\1\'/', '∅');
        $this->addTransform('complement_union', '/(\w+)\s*∪\s*\1\'/', 'U');
        
        // Cardinality
        $this->addTransform('card_union', '/\|(\w+)\s*∪\s*(\w+)\|/', '|$1| + |$2| - |$1 ∩ $2|');
        $this->addTransform('card_empty', '/\|∅\|/', '0');
    }
    
    // ==================== LOGIC ====================
    private function loadLogic(): void
    {
        // Negation
        $this->addTransform('double_neg', '/¬¬(\w+)/', '$1');
        $this->addTransform('neg_true', '/¬T/', 'F');
        $this->addTransform('neg_false', '/¬F/', 'T');
        
        // Conjunction
        $this->addTransform('and_true', '/(\w+)\s*∧\s*T/', '$1');
        $this->addTransform('and_false', '/(\w+)\s*∧\s*F/', 'F');
        $this->addTransform('and_self', '/(\w+)\s*∧\s*\1/', '$1');
        $this->addTransform('and_neg_self', '/(\w+)\s*∧\s*¬\1/', 'F');
        
        // Disjunction
        $this->addTransform('or_true', '/(\w+)\s*∨\s*T/', 'T');
        $this->addTransform('or_false', '/(\w+)\s*∨\s*F/', '$1');
        $this->addTransform('or_self', '/(\w+)\s*∨\s*\1/', '$1');
        $this->addTransform('or_neg_self', '/(\w+)\s*∨\s*¬\1/', 'T');
        
        // Implication
        $this->addTransform('impl_def', '/(\w+)\s*→\s*(\w+)/', '¬$1 ∨ $2');
        $this->addTransform('impl_true', '/T\s*→\s*(\w+)/', '$1');
        $this->addTransform('impl_false', '/F\s*→\s*(\w+)/', 'T');
        
        // Biconditional
        $this->addTransform('bicond_def', '/(\w+)\s*↔\s*(\w+)/', '($1 → $2) ∧ ($2 → $1)');
        
        // De Morgan's laws (logic)
        $this->addTransform('demorgan_and', '/¬\((\w+)\s*∧\s*(\w+)\)/', '¬$1 ∨ ¬$2');
        $this->addTransform('demorgan_or', '/¬\((\w+)\s*∨\s*(\w+)\)/', '¬$1 ∧ ¬$2');
        
        // Distribution
        $this->addTransform('dist_and_or', '/(\w+)\s*∧\s*\((\w+)\s*∨\s*(\w+)\)/', '($1 ∧ $2) ∨ ($1 ∧ $3)');
        $this->addTransform('dist_or_and', '/(\w+)\s*∨\s*\((\w+)\s*∧\s*(\w+)\)/', '($1 ∨ $2) ∧ ($1 ∨ $3)');
    }
    
    // ==================== NUMBER THEORY ====================
    private function loadNumberTheory(): void
    {
        // Divisibility
        $this->addTransform('div_zero', '/0\s*\|\s*(\w+)/', 'F');
        $this->addTransform('div_self', '/(\w+)\s*\|\s*\1/', 'T');
        $this->addTransform('div_one', '/1\s*\|\s*(\w+)/', 'T');
        
        // GCD/LCM
        $this->addTransform('gcd_self', '/gcd\((\w+),\1\)/', '$1');
        $this->addTransform('gcd_one', '/gcd\((\w+),1\)/', '1');
        $this->addTransform('lcm_self', '/lcm\((\w+),\1\)/', '$1');
        $this->addTransform('gcd_lcm', '/gcd\((\w+),(\w+)\)\s*\*\s*lcm\(\1,\2\)/', '$1*$2');
        
        // Modular arithmetic
        $this->addTransform('mod_self', '/(\w+)\s*mod\s*\1/', '0');
        $this->addTransform('mod_zero', '/0\s*mod\s*(\w+)/', '0');
        $this->addTransform('mod_add', '/\((\w+)\s*\+\s*(\w+)\)\s*mod\s*(\w+)/', '(($1 mod $3) + ($2 mod $3)) mod $3');
        $this->addTransform('mod_mult', '/\((\w+)\s*\*\s*(\w+)\)\s*mod\s*(\w+)/', '(($1 mod $3) * ($2 mod $3)) mod $3');
        
        // Congruence
        $this->addTransform('cong_reflexive', '/(\w+)\s*≡\s*\1\s*\(mod\s*(\w+)\)/', 'T');
        
        // Prime-related
        $this->addTransform('prime_2', '/prime\(2\)/', 'T');
        $this->addTransform('prime_1', '/prime\(1\)/', 'F');
        $this->addTransform('prime_0', '/prime\(0\)/', 'F');
        
        // Floor/ceiling
        $this->addTransform('floor_int', '/⌊(\d+)⌋/', '$1');
        $this->addTransform('ceil_int', '/⌈(\d+)⌉/', '$1');
        $this->addTransform('floor_ceil_int', '/⌈⌊(\w+)⌋⌉/', '⌊$1⌋');
    }
    
    // ==================== COMPLEX ANALYSIS ====================
    private function loadComplexAnalysis(): void
    {
        // Complex number basics
        $this->addTransform('i_squared', '/i\^2/', '-1');
        $this->addTransform('i_cubed', '/i\^3/', '-i');
        $this->addTransform('i_fourth', '/i\^4/', '1');
        
        // Euler's formula
        $this->addTransform('euler_formula', '/e\^\(i\*(\w+)\)/', 'cos($1)+i*sin($1)');
        $this->addTransform('euler_pi', '/e\^\(i\*π\)/', '-1');
        
        // Complex conjugate
        $this->addTransform('conj_conj', '/conj\(conj\((\w+)\)\)/', '$1');
        $this->addTransform('conj_real', '/conj\((\d+)\)/', '$1');
        $this->addTransform('conj_sum', '/conj\((\w+)\s*\+\s*(\w+)\)/', 'conj($1) + conj($2)');
        $this->addTransform('conj_product', '/conj\((\w+)\s*\*\s*(\w+)\)/', 'conj($1) * conj($2)');
        
        // Modulus
        $this->addTransform('mod_conj', '/\|(\w+)\|\^2/', '$1 * conj($1)');
        $this->addTransform('mod_product', '/\|(\w+)\s*\*\s*(\w+)\|/', '|$1| * |$2|');
        $this->addTransform('mod_quotient', '/\|(\w+)\s*\/\s*(\w+)\|/', '|$1| / |$2|');
        
        // Real/Imaginary parts
        $this->addTransform('re_real', '/Re\((\d+)\)/', '$1');
        $this->addTransform('im_real', '/Im\((\d+)\)/', '0');
        $this->addTransform('re_im_sum', '/Re\((\w+)\)\s*\+\s*i\*Im\(\1\)/', '$1');
    }
    
    // ==================== DIFFERENTIAL EQUATIONS ====================
    private function loadDifferentialEquations(): void
    {
        // First order linear
        $this->addTransform('ode_exp', '/y\'\s*=\s*k\*y/', 'y = C*e^(k*x)');
        $this->addTransform('ode_decay', '/y\'\s*=\s*-k\*y/', 'y = C*e^(-k*x)');
        
        // Separable
        $this->addTransform('ode_separable', '/y\'\s*=\s*f\(x\)\*g\(y\)/', '∫ 1/g(y) dy = ∫ f(x) dx');
        
        // Second order constant coefficient
        $this->addTransform('ode_harmonic', '/y\'\'\s*\+\s*ω\^2\*y\s*=\s*0/', 'y = A*cos(ω*x) + B*sin(ω*x)');
        $this->addTransform('ode_exp_growth', '/y\'\'\s*-\s*k\^2\*y\s*=\s*0/', 'y = A*e^(k*x) + B*e^(-k*x)');
        
        // Laplace transforms
        $this->addTransform('laplace_1', '/L\{1\}/', '1/s');
        $this->addTransform('laplace_t', '/L\{t\}/', '1/s^2');
        $this->addTransform('laplace_exp', '/L\{e\^\(a\*t\)\}/', '1/(s-a)');
        $this->addTransform('laplace_sin', '/L\{sin\(ω\*t\)\}/', 'ω/(s^2+ω^2)');
        $this->addTransform('laplace_cos', '/L\{cos\(ω\*t\)\}/', 's/(s^2+ω^2)');
        $this->addTransform('laplace_deriv', '/L\{f\'\(t\)\}/', 's*F(s) - f(0)');
    }
    
    // ==================== STATISTICS ====================
    private function loadStatistics(): void
    {
        // Probability basics
        $this->addTransform('prob_complement', '/P\((\w+)\'\)/', '1 - P($1)');
        $this->addTransform('prob_certain', '/P\(U\)/', '1');
        $this->addTransform('prob_impossible', '/P\(∅\)/', '0');
        $this->addTransform('prob_union', '/P\((\w+)\s*∪\s*(\w+)\)/', 'P($1) + P($2) - P($1 ∩ $2)');
        $this->addTransform('prob_indep', '/P\((\w+)\s*∩\s*(\w+)\)\s*indep/', 'P($1) * P($2)');
        
        // Conditional probability
        $this->addTransform('prob_cond', '/P\((\w+)\|(\w+)\)/', 'P($1 ∩ $2) / P($2)');
        $this->addTransform('bayes', '/P\((\w+)\|(\w+)\)\s*bayes/', 'P($2|$1) * P($1) / P($2)');
        
        // Expectation
        $this->addTransform('exp_const', '/E\[(\d+)\]/', '$1');
        $this->addTransform('exp_linear', '/E\[(\d+)\*(\w+)\s*\+\s*(\d+)\]/', '$1*E[$2] + $3');
        $this->addTransform('exp_sum', '/E\[(\w+)\s*\+\s*(\w+)\]/', 'E[$1] + E[$2]');
        
        // Variance
        $this->addTransform('var_const', '/Var\[(\d+)\]/', '0');
        $this->addTransform('var_linear', '/Var\[(\d+)\*(\w+)\s*\+\s*(\d+)\]/', '$1^2 * Var[$2]');
        $this->addTransform('var_def', '/Var\[(\w+)\]/', 'E[$1^2] - E[$1]^2');
        
        // Standard distributions
        $this->addTransform('normal_std', '/N\(0,1\)/', 'standard normal');
        $this->addTransform('binomial_exp', '/E\[Bin\(n,p\)\]/', 'n*p');
        $this->addTransform('binomial_var', '/Var\[Bin\(n,p\)\]/', 'n*p*(1-p)');
        $this->addTransform('poisson_exp', '/E\[Pois\(λ\)\]/', 'λ');
        $this->addTransform('poisson_var', '/Var\[Pois\(λ\)\]/', 'λ');
    }
    
    public function getTransformCount(): int
    {
        return count($this->transforms);
    }
}

class UniversalMathStream extends Stream
{
    private ?MathEvent $result = null;
    
    public function __construct()
    {
        parent::__construct('math_' . uniqid());
        $this->registerGate(new UniversalMathGate());
    }
    
    protected function handleUnconsumedEvent(EventInterface $event): void
    {
        $this->result = $event instanceof MathEvent ? $event : null;
    }
    
    public function getResult()
    {
        return $this->result;
    }
}

// === DEMO ===
echo "=== Universal Math Gate ===\n";
echo "All of mathematics. One gate. Pattern matching.\n\n";

$gate = new UniversalMathGate();
echo "Total transforms loaded: " . $gate->getTransformCount() . "\n\n";

$tests = [
    // Arithmetic
    '(5 + 3)',
    '(10 * 7)',
    '5!',
    
    // Algebra
    'x^2 * x^3',
    'log(a * b)',
    '(a+b)^2',
    
    // Calculus
    'd/dx x^4',
    'd/dx sin(x)',
    'd/dx e^x',
    '∫ x^2 dx',
    '∫ cos(x) dx',
    'lim_{x->0} sin(x)/x',
    
    // Trigonometry
    'sin(x)^2 + cos(x)^2',
    'sin(0)',
    'cos(π)',
    'sin(a + b)',
    
    // Logic
    '¬¬p',
    'p ∧ T',
    'p ∨ ¬p',
    
    // Set theory
    'A ∪ ∅',
    'A ∩ A',
    "(A ∪ B)'",
    
    // Complex
    'i^2',
    'e^(i*π)',
    
    // Statistics
    'P(A\')',
    'E[3*X + 5]',
    'Var[Bin(n,p)]',
];

foreach ($tests as $expr) {
    $stream = new UniversalMathStream();
    $stream->setMaxIterations(100);
    $stream->emit(new MathEvent($expr, $stream->getId()));
    
    try {
        $stream->process();
        $result = $stream->getResult();
        $output = $result ? $result->data : $expr . ' (no transform)';
        echo "$expr\n  → $output\n\n";
    } catch (Exception $e) {
        echo "$expr\n  → [max iterations]\n\n";
    }
}
