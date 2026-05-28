<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION', null),
        'default_model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout' => env('OPENAI_TIMEOUT', 60),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pitch Deck Intelligence
    |--------------------------------------------------------------------------
    */
    'pitch_deck' => [
        'max_sections' => 12,
        'supported_upload_types' => ['pdf', 'pptx'],
        'max_upload_size_kb' => 10240, // 10MB
        'analysis_categories' => [
            'clarity',
            'problem_statement',
            'solution_fit',
            'market_opportunity',
            'business_model',
            'competitive_advantage',
            'team_strength',
            'financial_projections',
            'ask_clarity',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Main AI Features (flagged for gradual rollout)
    |--------------------------------------------------------------------------
    */
    'features' => [
        'pitch_deck_generator' => true,
        'pitch_deck_analyzer' => true,
        'startup_scorer' => false,
        'investor_matching' => false,
    ],
];