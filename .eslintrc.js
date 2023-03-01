module.exports = {
    'extends': [
        'standard',
        'prettier',
        'plugin:cypress/recommended'
    ],
    'root': true,
    'env': {
        'browser': true,
        'jquery': true,
        'amd': true
    },
    'globals': {
        'jQuery': true,
        'google': true,
        'vue': true,
        'Vue': true,
    },
    'rules': {
        'arrow-parens': 0,
        'space-before-function-paren': 0,
        'keyword-spacing': [
            'warn'
        ],
        'padded-blocks': [
            'warn'
        ],
        'space-in-parens': [
            'warn'
        ],
        'spaced-comment': 0,
        'camelcase': 0,
        'no-undef': 0,
        'no-unused-vars': 0,
        'prefer-const': 0,
        'generator-star-spacing': 0,
        'no-shadow-restricted-names': 0,
        'eqeqeq': 0,
        'no-debugger': 0,
        'semi': [
            'error',
            'always'
        ],
        'one-var': 0,
        'indent': [
            'error',
            4,
            { 'SwitchCase': 1 }
        ],

        'standard/no-callback-literal': 0
    }
};
