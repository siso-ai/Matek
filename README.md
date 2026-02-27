# Universal Math Gate

**All of symbolic mathematics in one gate.**

216 pattern-rewrite rules across 11 mathematical domains. The stream's depth-first loop is the evaluation engine — expressions re-enter the gate until nothing matches and the result falls to pending. Math isn't calculation. It's transformation.

Built with the [SISO Framework](https://siso-framework.org).

## How It Works

One gate. One stream. The gate holds ~216 pattern-rewrite transforms. When an expression enters the stream:

1. The gate pattern-matches against its rules
2. If a rule matches, it emits the rewritten expression
3. The rewritten expression re-enters the same gate (depth-first)
4. When nothing matches, the expression falls to pending — that's the result

```
d/dx x^4  →  4*x^3              (power rule)
sin(x)^2 + cos(x)^2  →  1      (pythagorean identity)
∫ cos(x) dx  →  sin(x) + C     (integral table)
e^(i*π)  →  -1                  (euler's formula)
¬¬p  →  p                       (double negation)
```

The stream's `→E→E→` loop *is* the reduction engine. No special evaluation strategy needed.

## Domains

| Domain | Examples |
|---|---|
| **Arithmetic** | Addition, subtraction, multiplication, division, powers, factorial, identity rules |
| **Algebra** | Distribution, factoring, exponent rules, logarithm properties, square roots, fractions |
| **Calculus** | Derivatives (power, chain, product, quotient), integrals, limits |
| **Trigonometry** | Pythagorean identities, reciprocal/quotient, sum/difference, double/half angle, special values |
| **Linear Algebra** | Determinants, trace, transpose, dot product, cross product, magnitude, eigenvalues |
| **Set Theory** | Union, intersection, complement, De Morgan's laws, cardinality |
| **Logic** | Negation, conjunction, disjunction, implication, biconditional, De Morgan's, distribution |
| **Number Theory** | Divisibility, GCD/LCM, modular arithmetic, congruence, floor/ceiling |
| **Complex Analysis** | Powers of i, Euler's formula, conjugates, modulus, real/imaginary parts |
| **Differential Equations** | First/second order ODEs, separable equations, Laplace transforms |
| **Statistics** | Probability rules, Bayes' theorem, expectation, variance, standard distributions |

## Usage

```php
<?php
require_once 'math-demo.php';

$stream = new UniversalMathStream();
$stream->setMaxIterations(100);
$stream->emit(new MathEvent('d/dx x^4', $stream->getId()));
$stream->process();

$result = $stream->getResult();
echo $result->data;    // "4*x^3"
echo $result->history;  // [{rule: "d_power", from: "d/dx x^4", to: "4*x^3"}]
```

## Examples

```
Arithmetic:
  (5 + 3)                    →  8
  (10 * 7)                   →  70
  5!                         →  120

Algebra:
  x^2 * x^3                  →  x^(2+3)
  log(a * b)                  →  log(a)+log(b)

Calculus:
  d/dx x^4                   →  4*x^3
  d/dx sin(x)                →  cos(x)
  d/dx e^x                   →  e^x
  ∫ x^2 dx                   →  x^3/3 + C
  ∫ cos(x) dx                →  sin(x) + C
  lim_{x->0} sin(x)/x        →  1

Trigonometry:
  sin(x)^2 + cos(x)^2        →  1
  sin(0)                      →  0
  cos(π)                      →  -1
  sin(a + b)                  →  sin(a)*cos(b)+cos(a)*sin(b)

Logic:
  ¬¬p                         →  p
  p ∧ T                       →  p
  p ∨ ¬p                      →  T

Set Theory:
  A ∪ ∅                       →  A
  A ∩ A                       →  A
  (A ∪ B)'                    →  A' ∩ B'

Complex:
  i^2                         →  -1
  e^(i*π)                     →  -1

Statistics:
  P(A')                       →  1 - P(A)
  E[3*X + 5]                  →  3*E[X] + 5
  Var[Bin(n,p)]               →  n*p*(1-p)
```

## Audit Trail

Every reduction step is recorded in the event's history:

```php
$result->history = [
    ['rule' => 'd_power', 'from' => 'd/dx x^4', 'to' => '4*x^3'],
];
```

For multi-step reductions, the full chain is preserved. The proof *is* the event history.

## What This Demonstrates

- **CORE's `→E→E→` is a term rewriting system.** One gate + the stream loop = arbitrary symbolic computation
- **Convergence = no gate matches.** The expression is fully reduced when it falls to pending
- **The history array is the audit trail.** Every derivation step recorded as `{rule, from, to}` — not by logging infrastructure, but by the data flowing through the stream
- **Single gate, arbitrary depth.** 11 mathematical domains, 216 rules, no special recursion mechanism. The stream does what it already does

## Requirements

- PHP 8.1+
- Zero external dependencies
- Requires the SISO Framework v1 core (Event, Gate, Stream)

## License

GPL-3.0. See [LICENSE](LICENSE) for details.

## Outro
1/3 is not .33333(repeating)
