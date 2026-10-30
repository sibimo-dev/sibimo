<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    private const ELEMENT_KEYS = [
        'requirements',
        'procedure',
        'time',
        'cost',
        'product',
        'competence',
        'behavior',
        'complaints',
        'facilities',
    ];

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'service_type' => ['nullable', 'string', 'max:120'],
            'age' => ['required', 'integer', 'between:5,100'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'education' => ['required', 'string', 'max:100'],
            'occupation' => ['required', 'string', 'max:100'],
            'answers' => ['required', 'array'],
            'suggestion' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];

        foreach (self::ELEMENT_KEYS as $key) {
            $rules["answers.{$key}"] = ['required', 'integer', 'between:1,4'];
        }

        $validated = $request->validate($rules);
        $answers = collect(self::ELEMENT_KEYS)
            ->mapWithKeys(fn (string $key) => [$key => (int) $validated['answers'][$key]])
            ->all();

        $validated['service_type'] = $validated['service_type'] ?? 'Lainnya';
        $validated['answers'] = $answers;
        $validated['average_score'] = round(array_sum($answers) / count($answers), 2);
        unset($validated['website']);

        $response = SurveyResponse::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Survei berhasil dikirim.',
            'data' => [
                'id' => $response->survey_response_id,
                'message' => 'ok',
            ],
        ], 201);
    }

    public function publicSummary(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->summary(SurveyResponse::query()->get()),
        ]);
    }

    public function adminResults(Request $request): JsonResponse
    {
        $query = SurveyResponse::query()->latest('created_at');
        $period = $request->string('period', 'all')->toString();

        if ($period !== 'all' && ctype_digit($period)) {
            $query->where('created_at', '>=', now()->subDays((int) $period));
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->string('service_type')->toString());
        }

        $responses = $query->get();

        return response()->json([
            'success' => true,
            'data' => [
                ...$this->summary($responses),
                'by_service' => $this->groupBy($responses, 'service_type'),
                'by_gender' => $this->groupBy($responses, 'gender'),
                'by_age_group' => $this->groupBy($responses, null, fn (SurveyResponse $item) => $this->ageGroup($item->age), [
                    '< 20 tahun',
                    '20–29 tahun',
                    '30–39 tahun',
                    '40–49 tahun',
                    '50+ tahun',
                ]),
                'by_education' => $this->groupBy($responses, 'education'),
                'by_occupation' => $this->groupBy($responses, 'occupation'),
                'trend' => $this->trend($responses),
                'responses' => $responses->map(fn (SurveyResponse $item) => $this->serializeResponse($item))->values(),
            ],
        ]);
    }

    private function summary(Collection $responses): array
    {
        $average = $responses->isEmpty()
            ? 0
            : round((float) $responses->avg(fn (SurveyResponse $item) => (float) $item->average_score), 2);

        return [
            'total_responses' => $responses->count(),
            'average_score' => $average,
            'elements' => collect(self::ELEMENT_KEYS)->map(function (string $key) use ($responses) {
                $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0];

                foreach ($responses as $response) {
                    $score = (int) data_get($response->answers, $key, 0);
                    if (array_key_exists($score, $distribution)) {
                        $distribution[$score]++;
                    }
                }

                $total = array_sum($distribution);
                $weighted = 0;
                foreach ($distribution as $score => $count) {
                    $weighted += $score * $count;
                }

                return [
                    'key' => $key,
                    'average' => $total ? round($weighted / $total, 2) : 0,
                    'distribution' => $distribution,
                ];
            })->values()->all(),
        ];
    }

    private function groupBy(Collection $responses, ?string $field, ?callable $labelResolver = null, ?array $order = null): array
    {
        $groups = $responses->groupBy(function (SurveyResponse $item) use ($field, $labelResolver) {
            return $labelResolver
                ? $labelResolver($item)
                : (string) data_get($item, $field, 'Lainnya');
        });

        $items = $groups->map(fn (Collection $items, string $label) => [
            'label' => $label,
            'count' => $items->count(),
            'average' => round((float) $items->avg(fn (SurveyResponse $item) => (float) $item->average_score), 2),
        ])->values();

        if ($order) {
            $items = $items->sortBy(fn (array $item) => array_search($item['label'], $order, true) ?? PHP_INT_MAX)->values();
        } else {
            $items = $items->sortByDesc('count')->values();
        }

        return $items->all();
    }

    private function trend(Collection $responses): array
    {
        return $responses
            ->groupBy(fn (SurveyResponse $item) => $item->created_at?->format('Y-m'))
            ->map(fn (Collection $items, string $month) => [
                'month' => $month,
                'count' => $items->count(),
                'average' => round((float) $items->avg(fn (SurveyResponse $item) => (float) $item->average_score), 2),
            ])
            ->sortBy('month')
            ->values()
            ->all();
    }

    private function ageGroup(int $age): string
    {
        return match (true) {
            $age < 20 => '< 20 tahun',
            $age < 30 => '20–29 tahun',
            $age < 40 => '30–39 tahun',
            $age < 50 => '40–49 tahun',
            default => '50+ tahun',
        };
    }

    private function serializeResponse(SurveyResponse $response): array
    {
        return [
            'id' => $response->survey_response_id,
            'created_at' => $response->created_at?->toISOString(),
            'service_type' => $response->service_type,
            'age' => $response->age,
            'gender' => $response->gender,
            'education' => $response->education,
            'occupation' => $response->occupation,
            'answers' => $response->answers,
            'average_score' => (float) $response->average_score,
            'suggestion' => $response->suggestion,
        ];
    }
}
