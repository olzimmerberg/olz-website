<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Common;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use Olz\Entity\Common\TestableInterface;

class ExpressionEvaluationVisitor extends ExpressionVisitor {
    public bool $isMatching = true;

    public function __construct(protected TestableInterface $fakeEntity) {
    }

    public function walkComparison(Comparison $comparison): mixed {
        $operator_functions = [
            Comparison::EQ => fn ($value, $comparator) => $value === $comparator,
            Comparison::NEQ => fn ($value, $comparator) => $value !== $comparator,
            Comparison::LT => fn ($value, $comparator) => $value < $comparator,
            Comparison::LTE => fn ($value, $comparator) => $value <= $comparator,
            Comparison::GT => fn ($value, $comparator) => $value > $comparator,
            Comparison::GTE => fn ($value, $comparator) => $value >= $comparator,
            Comparison::IN => fn ($value, $comparator) => in_array($value, $comparator),
            Comparison::NIN => fn ($value, $comparator) => !in_array($value, $comparator),
            Comparison::CONTAINS => fn ($value, $comparator) => str_contains($value, $comparator),
            // Comparison::MEMBER_OF => fn($value, $comparator) => true,
            Comparison::STARTS_WITH => fn ($value, $comparator) => str_starts_with($value, $comparator),
            Comparison::ENDS_WITH => fn ($value, $comparator) => str_ends_with($value, $comparator),
        ];

        $value = $this->fakeEntity->testOnlyGetField($comparison->getField());
        $comparator = $comparison->getValue()->getValue();
        [$norm_value, $norm_comparator] = $this->normalizeValues($value, $comparator);
        $operator_function = $operator_functions[$comparison->getOperator()] ?? null;
        if ($operator_function === null) {
            throw new \Exception("Missing operator function for {$comparison->getOperator()}");
        }
        $this->isMatching = $operator_function($norm_value, $norm_comparator);
        if (in_array('--debug', $_SERVER['argv'] ?? [])) {
            $pretty_value = var_export($norm_value, true);
            $pretty_comparator = var_export($norm_comparator, true);
            $pretty_is_matching = $this->isMatching ? 'TRUE' : 'FALSE';
            echo "{$comparison->getField()}: {$pretty_value} {$comparison->getOperator()} {$pretty_comparator} ==> {$pretty_is_matching}\n\n";
        }
        return $comparison;
    }

    /** @return array{0: mixed, 1: mixed} */
    protected function normalizeValues(mixed $value, mixed $comparator): array {
        if ($value instanceof Date || $comparator instanceof Date) {
            return [$value?->format('Y-m-d'), $comparator?->format('Y-m-d')];
        }
        if ($value instanceof Time || $comparator instanceof Time) {
            return [$value?->format('H:i:s'), $comparator?->format('H:i:s')];
        }
        if ($value instanceof \DateTime || $comparator instanceof \DateTime) {
            return [$value?->format('Y-m-d H:i:s'), $comparator?->format('Y-m-d H:i:s')];
        }
        return [$value, $comparator];
    }

    public function walkCompositeExpression(CompositeExpression $composite): mixed {
        if ($composite->getType() === CompositeExpression::TYPE_AND) {
            $is_matching = true;
            foreach ($composite->getExpressionList() as $expr) {
                $this->isMatching = true;
                $expr->visit($this);
                // @phpstan-ignore-next-line booleanNot.alwaysFalse
                if (!$this->isMatching) {
                    $is_matching = false;
                }
            }
            $this->isMatching = $is_matching;
        } elseif ($composite->getType() === CompositeExpression::TYPE_OR) {
            $is_matching = false;
            foreach ($composite->getExpressionList() as $expr) {
                $this->isMatching = true;
                $expr->visit($this);
                // @phpstan-ignore-next-line booleanNot.alwaysTrue
                if ($this->isMatching) {
                    $is_matching = true;
                }
            }
            $this->isMatching = $is_matching;
        } elseif ($composite->getType() === CompositeExpression::TYPE_NOT) {
            $is_matching = true;
            $expr = $composite->getExpressionList()[0];
            $this->isMatching = true;
            $expr->visit($this);
            // @phpstan-ignore-next-line booleanNot.alwaysFalse
            $this->isMatching = !$this->isMatching;
        } else {
            throw new \Exception("Unknown CompositeExpression type: {$composite->getType()}");
        }
        return $composite;
    }

    public function walkValue(Value $value): mixed {
        $str_value = var_export($value->getValue(), true);
        throw new \Exception("Cannot match value {$str_value}");
    }
}
