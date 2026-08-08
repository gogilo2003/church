<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrganizationalUnit;
use App\Models\RevenueDistribution;
use App\Models\RevenueSharingRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class RevenueSharingService
{
    /**
     * Simulate revenue splits for preview without persisting transactions.
     */
    public function simulate(
        string $sourceType,
        ?int $sourceId,
        int $sourceUnitId,
        float $grossAmount
    ): array {
        $sourceUnit = OrganizationalUnit::with(['level', 'parent'])->find($sourceUnitId);

        if (! $sourceUnit) {
            return [];
        }

        $rules = $this->getMatchingRules($sourceType, $sourceId, $sourceUnit);
        $splits = [];

        foreach ($rules as $rule) {
            if ($grossAmount < $rule->min_collection_threshold) {
                continue;
            }

            $destinationUnit = $this->resolveDestinationUnit($rule, $sourceUnit);

            if (! $destinationUnit) {
                continue;
            }

            $amount = $rule->calculation_type === 'percentage'
                ? ($grossAmount * ($rule->value / 100))
                : $rule->value;

            if ($rule->max_cap_amount !== null && $rule->max_cap_amount > 0) {
                $amount = min($amount, $rule->max_cap_amount);
            }

            $splits[] = [
                'rule_id' => $rule->id,
                'rule_name' => $rule->name,
                'direction' => $rule->direction,
                'source_unit' => [
                    'id' => $sourceUnit->id,
                    'name' => $sourceUnit->name,
                ],
                'destination_unit' => [
                    'id' => $destinationUnit->id,
                    'name' => $destinationUnit->name,
                ],
                'gross_amount' => round($grossAmount, 2),
                'distributed_amount' => round($amount, 2),
                'summary' => $rule->calculation_type === 'percentage'
                    ? "{$rule->value}% of {$grossAmount}" . ($rule->max_cap_amount ? " (capped at {$rule->max_cap_amount})" : "")
                    : "Fixed amount {$rule->value}",
            ];
        }

        return $splits;
    }

    /**
     * Evaluate and persist revenue distributions for an actual collection transaction.
     */
    public function processTransaction(
        Model $transactionModel,
        string $sourceType,
        ?int $sourceId,
        int $sourceUnitId,
        float $grossAmount
    ): array {
        $splits = $this->simulate($sourceType, $sourceId, $sourceUnitId, $grossAmount);
        $records = [];

        DB::transaction(function () use ($splits, $transactionModel, &$records) {
            foreach ($splits as $split) {
                $records[] = RevenueDistribution::create([
                    'revenue_sharing_rule_id' => $split['rule_id'],
                    'transaction_type' => class_basename($transactionModel),
                    'transaction_id' => $transactionModel->getKey(),
                    'source_unit_id' => $split['source_unit']['id'],
                    'destination_unit_id' => $split['destination_unit']['id'],
                    'gross_amount' => $split['gross_amount'],
                    'distributed_amount' => $split['distributed_amount'],
                    'calculation_summary' => $split['summary'],
                    'status' => 'pending',
                ]);
            }
        });

        return $records;
    }

    private function getMatchingRules(string $sourceType, ?int $sourceId, OrganizationalUnit $sourceUnit): iterable
    {
        $query = RevenueSharingRule::query()
            ->where('is_active', true)
            ->where('source_type', $sourceType);

        if ($sourceId !== null) {
            $query->where(function ($q) use ($sourceId) {
                $q->whereNull('source_id')->orWhere('source_id', $sourceId);
            });
        }

        return $query->get()->filter(function (RevenueSharingRule $rule) use ($sourceUnit) {
            if ($rule->source_scope_type === 'global') {
                return true;
            }

            if ($rule->source_scope_type === 'hierarchy_level' && $sourceUnit->hierarchy_level_id === $rule->source_scope_id) {
                return true;
            }

            if ($rule->source_scope_type === 'org_unit' && $sourceUnit->id === $rule->source_scope_id) {
                return true;
            }

            return false;
        })->sortBy('priority');
    }

    private function resolveDestinationUnit(RevenueSharingRule $rule, OrganizationalUnit $sourceUnit): ?OrganizationalUnit
    {
        if ($rule->destination_type === 'specific_unit') {
            return OrganizationalUnit::find($rule->destination_id);
        }

        if ($rule->destination_type === 'parent') {
            return $sourceUnit->parent;
        }

        if ($rule->destination_type === 'ancestor_at_level') {
            $ancestorIds = $sourceUnit->ancestors()->pluck('id')->toArray();
            return OrganizationalUnit::whereIn('id', $ancestorIds)
                ->where('hierarchy_level_id', $rule->destination_id)
                ->first();
        }

        return null;
    }
}
