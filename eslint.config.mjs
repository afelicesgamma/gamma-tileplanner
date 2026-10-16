import js from '@eslint/js';

export default [
  {
    ignores: ['dist/**', 'node_modules/**', 'vendor/**'],
  },
  {
    ...js.configs.recommended,
    files: ['resources/scripts/**/*.ts'],
  },
  {
    files: ['resources/scripts/**/*.ts'],
    languageOptions: {
      ecmaVersion: 2017,
      sourceType: 'module',
      globals: {
        CustomEvent: 'readonly',
        document: 'readonly',
        window: 'readonly',
      },
    },
    rules: {
      'no-console': 'off',
      quotes: ['error', 'single'],
      'comma-dangle': [
        'error',
        {
          arrays: 'always-multiline',
          objects: 'always-multiline',
          imports: 'always-multiline',
          exports: 'always-multiline',
          functions: 'ignore',
        },
      ],
    },
  },
];
