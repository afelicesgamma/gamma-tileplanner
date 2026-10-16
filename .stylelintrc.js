module.exports = {
  'extends': 'stylelint-config-standard',
  'rules': {
    'no-empty-source': null,
    'string-quotes': 'double',
    'at-rule-no-unknown': [
      true,
      {
        'ignoreAtRules': [
          'tailwind',
        ],
      },
    ],
  },
};
